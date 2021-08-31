<?php

namespace Litegram\Plugins;

use Litegram\Payload;
use Litegram\Plugins\Database;
use Litegram\Plugins\AbstractPlugin;
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

    /**
     * @var int
     */
    private static $currentUserId;

    /**
     * @return string
     */
    private static $userData = [];

    private static $firstTime = false;
    private static $newVersion = false;
    private static $floodTime;

    /**
     * @return void
     */
    public static function boot(): void
    {
        self::$currentUserId = self::$payload->get('*.from.id');

        // если юзер существует, получаем данные и пропускаем остальное
        if (self::exists(self::$currentUserId)) {
            self::$userData = (array) self::getDataById(self::$currentUserId);
            self::diffBotVersion();
            return;
        }

        // если юзер не существует в базе, добавляем
        $source = null;
        $text = self::$payload->get('*.text');
        if (Payload::isCommand() && $text && stripos($text, '/start') !== false) {
            $text = explode(' ', $text);
            if (is_array($text) && count($text) > 1) {
                unset($text[0]);
                $source = implode(' ', $text);
            }
        }

        self::$firstTime = true;

        // Создаем новую запись о юзере
        $from = new Collection(self::$payload->get('*.from'));
        $firstname = $from->get('first_name', null);
        $lastname = $from->get('last_name', null);

        $data = [
            // Общная информация
            'user_id' => $from->get('id', null), // telegram id юзера
            'active' => 1, // юзер не заблокировал бота
            'fullname' => trim("{$firstname} {$lastname}"), // имя фамилия
            'firstname' => $firstname, // имя
            'lastname' => $lastname, // фамилия
            'username' => $from->get('username', null), // telegram юзернейм
            'locale' => $from->get('language_code', self::$config->get('localization.default_language', 'en')), // язык

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

            // json row
            'extra' => '',

            // Служебное
            'note' => null, // заметка о юзере
            'version' => self::$config->get('bot.version'), // последняя версия бота с которой взаимодействовал юзер
        ];

        $data = array_merge($data, self::$config->get('modules.user.data', []));

        Database::table('users')->insert($data);

        self::$userData = (array) self::getDataById(self::$currentUserId);
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        if (!self::$config->get('modules.user.enable')) {
            return;
        }

        if (!self::$userData['active']) {
            self::update([
                'active' => 1,
                'last_message' => time(),
            ]);
        } else {
            self::update([
                'last_message' => time(),
            ]);
        }
    }

    /**
     * @param int|string $userId
     * @param array $data
     * @return void
     */
    public static function updateById($userId, array $data = []): void
    {
        Database::table('users')->where('user_id', $userId)->update($data);
    }

    /**
     * @param array $data
     * @return void
     */
    public static function update(array $data = []): void
    {
        Database::table('users')->where('user_id', self::$currentUserId)->update($data);
    }

    /**
     * @param int|string $userId
     * @return bool
     */
    public static function exists($userId): bool
    {
        return Database::table('users')->where('user_id', $userId)->exists();
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

    /**
     * Get user data from database by id.
     *
     * @param int|string $userId
     * @return \stdClass
     */
    public static function getDataById($userId)
    {
        return Database::table('users')->where('user_id', $userId)->first();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value = null): void
    {
        self::$userData[$key] = $value;
        Database::table('users')->where('user_id', self::$currentUserId)->update([$key => $value]);
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

    public function __set($key, $value)
    {
        self::$userData[$key] = $value;
    }

    public static function isFlood(): bool
    {
        $timeout = self::$bot->config('user.flood_time');
        $diffMessageTime = time() - self::$userData['last_message'];

        $timeout = self::$bot->config('user.flood_time');

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
        return self::$userData['banned'] == 1;
    }

    public static function isAdmin(): bool
    {
        $adminList = (array) self::$bot->config('admin.list', []);
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
}