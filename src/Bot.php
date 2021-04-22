<?php

namespace Litegram;

use Litegram\Support\Collection;
use Litegram\Traits\Http\Request;
use Litegram\Traits\Events;
use Litegram\Traits\Telegram\Aliases;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;

define('LITEGRAM_DEFAULT_EVENT_SORT', 500);

class Bot
{
    use Request;
    use Events;
    use Methods;
    use Aliases;
    use Replies;

    /**
     * @var array
     */
    private static $instances = [];

    /**
     *
     * @var string
     */
    protected $token;

    /**
     * @var Collection
     */
    protected $config = [
        'bot' => [
            'token' => '1234567890:BOT_TOKEN',
            'handler' => 'https://example.com/handler.php',
            'name' => 'Litegram Bot',
            'username' => 'LitegramBot',
            'version' => '1.0.0',
            'timezone' => 'Europe/Samara',
            'timelimit' => 120,
        ],
        'telegram' => [
            'parse_mode' => 'html',
            'safe_callback' => true,
        ],
        'debug' => [
            'enable' => true,
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
                'driver' => 'php', // php, serizalize
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
     * Contains '*.chat.id' or '*.from.id' if '*.chat.id' not exists
     * and NULL if '*.from.id' not exists.
     *
     * @var int|null
     */
    public $defaultIdForReply = null;

    /**
     * @var array
     */
    private $commandTags = ['/', '.', '!'];

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var array
     */
    private $defaultAnswers = [];

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
    }

    private function __sleep()
    {
    }

    /**
     * @return Bot
     */
    public static function getInstance(): Bot
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * @param string|array $token
     * @param array $config
     * @return Bot
     */
    public function auth($token, array $config = [])
    {
        if (is_array($token)) {
            $this->token = $token['bot']['token'] ?? null;
            $config = $token;
        } else {
            $this->token = $token;
        }

        $this->config = new Collection(array_merge($this->config, $config));

        date_default_timezone_set($this->config('bot.timezone'));

        return $this;
    }

    /**
     * Handle update from Telegram or set force update fro somethere.
     *
     * @param array|string|\stdClass|\Chipslays\Collection\Collection $update
     * @return Bot
     * @throws \Exception
     */
    public function webhook($update = null)
    {
        if ($update) {
            $this->setEventData($update);
        } else {
            $input = file_get_contents('php://input');
            if ($input && $input !== '') {
                $this->setEventData($input);
            }
        }

        if (!$this->hasUpdate()) {
            return $this;
        }

        $this->defaultIdForReply = $this->update('*.chat.id', $this->update('*.from.id'));

        $this->addModule(Update::class);

        // Отпускаем Telegram, чтобы он не ждал и не блокировал остальные запросы.
        if (php_sapi_name() !== 'cli') {
            $this->finishRequest($this->config('bot.timelimit', 1800));
        }

        return $this;
    }

    /**
     * Is there an update from Telegram.
     *
     * @return boolean
     */
    public function hasUpdate(): bool
    {
        return $this->data !== [];
    }

    /**
     * Получить значение из апдейта или получить объект коллекцию обновления.
     *
     * @param string|null $path
     * @param mixed $default
     * @return mixed|Collection
     */
    public function update(?string $path = null, $default = null)
    {
        return $path ? $this->data->get($path, $default) : $this->data;
    }

    /**
     * Получить значение из конфига или получить объект коллекцию конфига.
     *
     * @param string|null $path
     * @param mixed $default
     * @return mixed|Collection
     */
    public function config(?string $path = null, $default = null)
    {
        return $path ? $this->config->get($path, $default) : $this->config;
    }

    /**
     * Before call a massive operations, use this method for non-block bot answers.
     *
     * @param integer $timeLimit Execution script time limit in seconds.
     * @return Bot
     */
    public function finishRequest($timeLimit = 1800)
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

        return $this;
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
     * @param string $class
     * @param array $parameters Parameters for `boot` method.
     * @return static
     */
    public function addModule($module, array $parameters = [])
    {
        $class = new $module;
        if (method_exists($class, 'boot')) {
            call_user_func_array([$class, 'boot'], $parameters);
        }

        if (!property_exists($class, 'alias')) {
            throw new \Exception("Missed required `alias` property in module {$module}", 1);
        }

        $alias = $class::$alias;

        if (property_exists($this, $alias) || in_array($alias, $this->modules)) {
            throw new \Exception("Cannot overide exists property `{$alias}`.");
        }

        $this->$alias = $class;
        $this->modules[] = $alias;

        return $this;
    }

    /**
     * @param callable|string $fn
     * @param array $parameters
     * @return void
     */
    public function call($fn, $parameters = [])
    {
        if (is_callable($fn)) {
            return call_user_func_array($fn, $parameters);
        } elseif (stripos($fn, '@') !== false) {
            [$controller, $method] = explode('@', $fn);

            try {
                $reflectedMethod = new \ReflectionMethod($controller, $method);
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {
                    if ($reflectedMethod->isStatic()) {
                        return forward_static_call_array(array($controller, $method), $parameters);
                    } else {
                        if (is_string($controller)) {
                            $controller = new $controller();
                        }
                        return call_user_func_array(array($controller, $method), $parameters);
                    }
                }
            } catch (\ReflectionException $reflectionException) {
                // nothing...
            }
        }
    }
}
