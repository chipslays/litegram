<?php

namespace Litegram\Plugins;

use Litegram\Plugins\AbstractPlugin;
use Litegram\Database\Models\User as UserModel;
use Litegram\Payload;
use Litegram\Support\Collection;

class User extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'user';

    /**
     * @var array
     */
    public static $depends = [
        'database',
    ];

    private static $currentUserId;

    private static $user;

    private static $userData = [];

    private static $firstTime = false;

    private static $newVersion = false;

    private static $floodTime;

    /**
     * @return void
     */
    public static function boot(): void
    {
        self::$firstTime = false;
        self::$newVersion = false;
        self::$floodTime = null;
        
        self::$currentUserId = self::$payload->get('*.from.id');

        self::$user = UserModel::find(self::$currentUserId);

        if (self::$user) {
            // we getting exists user
            self::$userData = self::$user->toArray();
            self::diffBotVersion();
            return;
        }

        // insert new user
        $insert = [];

        // get source (deeplink)
        $source = null;
        $text = self::$payload->get('*.text');
        if (Payload::isCommand() && stripos($text, '/start') !== false) {
            $text = explode(' ', $text);
            if (is_array($text) && count($text) > 1) {
                unset($text[0]);
                $source = implode(' ', $text);
            }
        }

        $from = new Collection(self::$payload->get('*.from'));
        $firstname = trim($from->get('first_name', null));
        $lastname = trim($from->get('last_name', null));

        $insert = [
            // Общая информация
            'id' => self::$currentUserId, // telegram id юзера
            'blocked' => 0, // юзер не заблокировал бота
            'fullname' => trim("{$firstname} {$lastname}"), // имя фамилия
            'firstname' => $firstname, // имя
            'lastname' => $lastname, // фамилия
            'username' => $from->get('username', null), // telegram юзернейм
            'photo' => null, // фото
            'locale' => $from->get('language_code', self::$config->get('plugins.localization.fallback', 'en')), // язык

            // Сообщения
            'first_message' => time(), // первое сообщение (дата регистрации) (unix)
            'last_message' => time(), // последнее сообщение (unix)
            'source' => $source, // откуда пользователь пришел (/start botcatalog)

            // Бан
            'banned' => 0, // забанен или нет
            'ban_comment' => null, // комментарий при бане
            'ban_start' => null, // бан действует с (unix)
            'ban_end' => null, // бан до (unix)

            // Дополнительно
            'role' => 'user', // группа юзера
            'nickname' => null, // никнейм (например для игровых ботов)
            'emoji' => null, // эмодзи/иконка (префикс)
            'phone' => null, // телефон

            // json row
            'extra' => null,

            // Служебное
            'note' => null, // заметка о юзере
            'version' => self::$config->get('bot.version'), // последняя версия бота с которой взаимодействовал юзер
        ];

        // merge default values from config for new user
        $insert = array_merge($insert, self::$config->get('plugins.user.data', []));

        self::$user = UserModel::firstOrCreate(['id' => self::$currentUserId], $insert);
        self::$userData = self::$user->toArray();

        self::$firstTime = true;
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        $update = array_merge([
            'last_message' => time(),
        ], self::getDiffUserData());

        if (self::$userData['blocked']) {
            $update['blocked'] = 0;
        }

        self::update($update);
    }

    /**
     * @param int|string $userId
     * @param array $data
     * @return void
     */
    public static function updateById($userId, array $data = []): void
    {
        UserModel::where('id', $userId)->update($data);
    }

    /**
     * @param array $data
     * @return void
     */
    public static function update(array $data = []): void
    {
        UserModel::where('id', self::$currentUserId)->update($data);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value = null): void
    {
        self::$userData[$key] = $value;
        self::update([$key => $value]);
    }

    /**
     * @param int|string $userId
     * @return bool
     */
    public static function exists($userId): bool
    {
        return UserModel::where('id', $userId)->exists();
    }

    /**
     * Retrive value by key in current user data.
     *
     * @param string $key
     * @param mixed $default
     * @return string|int
     */
    public static function get($key, $default = null)
    {
        return isset(self::$userData[$key]) ? self::$userData[$key] : $default;
    }

    public static function save(): void
    {
        $userData = self::$userData;
        unset($userData['id']);
        self::update($userData);
    }

    public function __get($key)
    {
        return self::get($key);
    }

    /**
     * Set localy value withou update in Model and Database.
     */
    public function __set($key, $value)
    {
        self::$userData[$key] = $value;
    }

    /**
     * Get user data from database by id.
     *
     * @param int|string $userId
     * @return \stdClass
     */
    public static function find($userId)
    {
        return UserModel::find($userId);
    }


    public static function isFlood(): bool
    {
        $timeout = self::$bot->config('plugins.user.flood_time');
        $diffMessageTime = time() - self::$user->last_message;

        self::$floodTime = $timeout - $diffMessageTime;

        return $diffMessageTime <= $timeout;
    }

    public static function getFloodTime()
    {
        return self::$floodTime;
    }

    public static function firstTime()
    {
        return self::$firstTime;
    }

    public static function newVersion()
    {
        return self::$newVersion;
    }

    public static function isBanned(): bool
    {
        return self::$user->banned == 1;
    }

    public static function isAdmin(): bool
    {
        $adminList = (array) self::$config->get('admin.list', []);
        if (array_key_exists(self::$payload->get('*.from.id'), $adminList) || array_key_exists(self::$payload->get('*.from.username'), $adminList)) {
            return true;
        }
        return false;
    }

    private static function diffBotVersion(): void
    {
        $userVersion = (string) self::get('version');
        $currentVersion = (string) self::$config->get('bot.version');

        self::$newVersion = $userVersion !== $currentVersion;

        if (self::$newVersion) {
            self::update(['version' => $currentVersion]);
            self::$userData['version'] = $currentVersion;
        }
    }

    private static function getDiffUserData(): array
    {
        $from = new Collection(self::$payload->get('*.from'));

        $firstname = trim($from->get('first_name', null));
        $lastname = trim($from->get('last_name', null));
        $username = trim($from->get('username', null));

        $update = [];

        if ($firstname !== self::$user->firstname) {
            $update['firstname'] = $firstname;
            $update['fullname'] = trim("{$firstname} {$lastname}");
        }

        if ($lastname !== self::$user->lastname) {
            $update['lastname'] = $lastname;
            $update['fullname'] = trim("{$firstname} {$lastname}");
        }

        if ($username !== self::$user->username) {
            $update['username'] = $username;
        }

        return $update;
    }

    /**
     * Retrive user model.
     */
    public static function model()
    {
        return self::$user;
    }
}
