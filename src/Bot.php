<?php

namespace Litegram;

use Litegram\Traits\Http\Request;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;
use Litegram\Traits\Telegram\Aliases as TelegramAliases;
use Litegram\Traits\Telegram\Events;
use Litegram\Traits\Filter;
use Litegram\Traits\Middleware;
use Litegram\Traits\State as StateTrait;
use Litegram\Modules\User;
use Container\Container;
use Chipslays\Collection\Collection;
use Chipslays\Event\EventTrait as BaseEvent;

define('BOT_DEFAULT_SORT_VALUE', 500);

/**
 * @method static self getInstance()
 */
class Bot extends Container
{
    use Request;
    use Methods;
    use Replies;
    use TelegramAliases;
    use Filter;
    use Middleware;
    use StateTrait;
    use Events, BaseEvent {
        Events::on insteadof BaseEvent;
        Events::run insteadof BaseEvent;
    }

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Chipslays\Collection\Collection
     */
    private $config = [
        'bot' => [
            'token' => '1234567890:BOT_TOKEN',
            'handler' => 'https://example.com/handler.php',
            'name' => 'MyBot',
            'username' => 'MyTelegram_bot',
            'version' => '1.0.0',
            'timezone' => 'UTC',
        ],
        'telegram' => [
            'parse_mode' => 'html',
            'safe_callback' => true,
        ],
        'debug' => [
            'enable' => false,
            'developer' => '436432850',
        ],
        'admin' => [
            'list' => [
                'chipslays' => 'password',
                '436432850' => 'password',
            ],
        ],
        'modules' => [
            'database' => [
                'enable' => false,
                'driver' => 'mysql',
                'sqlite' => [
                    'database' => '/path/to/database.sqlite',
                ],
                'mysql' => [
                    'host'      => 'localhost',
                    'database'  => 'telegram_test',
                    'username'  => 'mysql',
                    'password'  => 'mysql',
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ],
            'cache' => [
                'enable' => false,
                'driver' => 'memcached',
                'memcached' => [
                    'host'  => 'localhost',
                    'port' => '11211',
                ],
                'redis' => [
                    'host'  => '127.0.0.1',
                    'port' => '6379',
                ],
            ],
            'store' => [
                'enable' => false,
                'driver' => 'database',
                'file' => [
                    'dir' => __DIR__ . '/storage/store',
                ],
            ],
            'user' => [
                'enable' => false,
                'flood_time' => 1,
            ],
            'state' => [
                'enable' => false,
            ],
            'localization' => [
                'enable' => false,
                'default' => 'en',
                'dir' => __DIR__ . '/localization',
            ],
            'logger' => [
                'enable' => false,
                'dir' => __DIR__ . '/storage/logs',
                'auto' => true,
            ],
            'statistics' => [
                'updates' => false,
                'messages' => true,
                'users' => true,
            ],
        ],
        'components' => [
            'vendor.component' => [
                'enable' => false,
                'entrypoint' => __DIR__ . '/components/vendor/component/autoload.php',
            ],
        ],
    ];

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var array
     */
    private $defaultAnswers = [];

    /**
     * Track executed time
     *
     * @var float
     */
    private $startTime;

    /**
     * @var array
     */
    private $commandTags = ['/', '.', '!'];

    public function __construct()
    {
    }

    /**
     * @param string|null $token
     * @param array $config
     * @return static
     */
    public function auth(?string $token, ?array $config)
    {
        $this->startTime = microtime(true);

        if (!$token) {
            throw new \Exception("Missed requred parameter `token`");
        }

        $this->token = $token;

        $this->config = new Collection(array_merge($this->config, (array) $config));
        $this->config->set('bot.token', $token);

        if ($timezone = $this->config('bot.timezone')) {
            date_default_timezone_set($timezone);
        }

        return $this;
    }

    /**
     * @param array|string|\stdClass|\Chipslays\Collection\Collection $update
     * @return static
     */
    public function webhook($update = null)
    {
        if ($update) {
            $this->setEventData($update);
        } else {
            if ($input = file_get_contents('php://input')) {
                $this->setEventData($input);
            }
        }

        $this->addModule(Update::class);

        $this->defaultIdForReply = $this->update('*.chat.id', $this->update('*.from.id'));

        $this->decodeCallback();
        
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasUpdate(): bool
    {
        return $this->data !== null;
    }

    /**
     * Get the value of token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param string $token
     *
     * @return static
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Получить значение из апдейта или получить объект коллекцию обновления.
     *
     * @param string|null $keys
     * @param mixed $default
     * @return Collection|string|integer
     */
    public function update($key = null, $default = null)
    {
        return $key ? $this->data->get($key, $default) : $this->data;
    }

    /**
     * Получить значение из конфига или получить объект коллекцию конфига.
     *
     * @param string|null $keys
     * @param mixed $default
     * @return string|integer|Collection
     */
    public function config($keys = null, $default = null)
    {
        return $keys ? $this->config->get($keys, $default) : $this->config;
    }

    /**
     * Add module to Bot instance.
     * @param string $class
     * @param array $params
     * @return static
     */
    public function addModule($class, array $params = [])
    {
        $module = new $class;
        call_user_func_array([$module, 'boot'], $params);

        $alias =  call_user_func_array([$module, 'getAlias'], []);

        if (property_exists($this, $alias) || in_array($alias, $this->modules)) {
            throw new \Exception("Cannot overide exists property `{$alias}`.");
        }

        $this->$alias = $module;
        $this->modules[] = $alias;

        return $this;
    }

    /**
     * Call module like $bot->module('db')->table(...)
     * 
     * @param string $name
     * @return object
     */
    public function module(string $alias)
    {
        if (!in_array($alias, $this->modules)) {
            throw new \Exception("Module `{$alias}` not exists.");
        }

        return $this->$alias;
    }

    /**
     * Подключение компонентов
     *
     * @return void
     */
    private function loadComponents()
    {
        $components = $this->config()->get('components');

        if (!$components) {
            return;
        }

        foreach ($components as $component) {
            if (!$component['enable'] ?? null) {
                continue;
            }

            if (file_exists($component['entrypoint'] ?? null)) {
                try {
                    require_once $component['entrypoint'];
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                }
            }
        }
    }

    /**
     * @param callable|string $fn
     * @param array $params
     * @return void
     */
    public function callController($fn, $params = [])
    {
        if (is_callable($fn)) {
            return call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            [$controller, $method] = explode('@', $fn);

            try {
                $reflectedMethod = new \ReflectionMethod($controller, $method);
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {
                    if ($reflectedMethod->isStatic()) {
                        return forward_static_call_array(array($controller, $method), $params);
                    } else {
                        if (is_string($controller)) {
                            $controller = new $controller();
                        }
                        return call_user_func_array(array($controller, $method), $params);
                    }
                }
            } catch (\ReflectionException $reflectionException) {
                // poka nicho ne delaem
            }
        }
    }

    /**
     * Получить время выполнения.
     *
     * @param integer $lenght
     * @return int|float
     */
    public function getExecutedTime(int $lenght = 6)
    {
        return round(microtime(true) - $this->startTime, $lenght);
    }

    /**
     * Before call a massive operations, use this method for non-block bot answers.
     *
     * @param integer $timeLimit
     * @return void
     */
    public function sayGoodbyeTelegramAndContinueEvent($timeLimit = 900)
    {
        set_time_limit($timeLimit);
        ignore_user_abort(true);
        $response = json_encode(['ok']);

        header('Connection: close');
        header('Content-Length: ' . strlen($response));
        header("Content-type:application/json");

        echo $response;

        flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Установить символы с которых сообщение будет считаться командой
     * По умолчанию ['/', '.', '!']
     *
     * @param array $tags
     * @return void
     */
    public function setCommandTags(array $tags)
    {
        $this->commandTags = $tags;
    }

    public function getCommandTags()
    {
        return $this->commandTags;
    }

    /**
     * Сравнение пароля Администратора (условная авторизация).
     * Если $password корректный, вернет True, иначе False.
     *
     * @param string $password
     * @return bool
     */
    public function admin($password): bool
    {
        if (!User::isAdmin()) {
            return false;
        }

        $username = $this->update('*.from.username');
        $userId = $this->update('*.from.id');
        return $password == $this->config("admin.list.{$username}", $this->config("admin.list.{$userId}", false));
    }
}
