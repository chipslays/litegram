<?php

namespace Litegram;

use Litegram\Modules\Session;
use Litegram\Traits\Events;
use Litegram\Traits\Modules;
use Litegram\Traits\Webhook;
use Litegram\Traits\Http\Request;
use Litegram\Traits\Filter;
use Litegram\Traits\State;
use Litegram\Traits\Middleware;
use Litegram\Traits\Components;
use Litegram\Traits\Telegram\Aliases;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;
use Litegram\Support\Collection;

class Bot
{
    use Request;
    use Events;
    use Methods;
    use Aliases;
    use Replies;
    use Modules;
    use Webhook;
    use State;
    use Filter;
    use Middleware;
    use Components;

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
            'session' => [
                'enable' => true,
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
                'driver' => 'php', // php, serialize
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
     * Contains '*.chat.id' or '*.from.id' from Telegram update.
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
    private $defaultAnswers = [];

    /**
     * @var int
     */
    private $__startAt;

    /**
     * @var array
     */
    protected static $mapped = [];

    public static function map(string $method, $func) : void
    {
        if (method_exists(self::class, $method) || array_key_exists($method, self::$mapped)) {
            throw new \Exception("Cannot override an existing `{$method}` method.");
        }

        self::$mapped[$method] = $func;
    }

    public static function mapOnce(string $method, $func) : void
    {
        if (method_exists(self::class, $method) || array_key_exists($method, self::$mapped)) {
            throw new \Exception("Cannot override an existing `{$method}` method.");
        }

        self::$mapped[$method] = self::getInstance()->call($func);
    }

    public function __call($method, $args)
    {
        $fn = self::$mapped[$method];
        return is_callable($fn) || class_exists($fn) ? $this->call($fn, $args) : $fn;
    }

    public static function __callStatic($method, $args)
    {
        $fn = self::$mapped[$method];
        return is_callable($fn) || class_exists($fn) ? self::getInstance()->call($fn, $args) : $fn;
    }

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
        $this->__startAt = microtime(true);

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
     * Декодирует входящий параметр data у callback_query
     *
     * @return void
     */
    private function decodeCallback()
    {
        if (!$this->config('telegram.safe_callback')) {
            return;
        }

        if (!$data = $this->update('callback_query.data')) {
            return;
        }

        $data = @gzinflate(base64_decode($data));

        if (!$data) {
            return;
        }

        $this->update()->set('callback_query.data', $data);
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
     * Получить время выполнения скрипта.
     *
     * @param integer $lenght
     * @return int|float
     */
    public function getExecutionTime(int $lenght = 6)
    {
        return round(microtime(true) - $this->__startAt, $lenght);
    }

    /**
     * Call anonymous function or class string (className@doSomething)
     *
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

    /**
     * Set current chain name.
     *
     * @param string|null $name
     * @return void
     * @throws \Exception
     */
    public function setChain(?string $name)
    {
        if (!$this->isModuleExists('session')) {
            throw new \Exception("Please add `session` module before start chain.");
        }

        Session::set('__chain', $name);
    }

    /**
     * Chain dialog flow.
     *
     * @param string $current
     * @param string|null $next
     * @param callable|string $func Return `false` for prevent next step.
     * @return Bot
     */
    public function chain(string $current, ?string $next, $func)
    {
        if (Session::get('__chain') == $current && !$this->skipped()) {
            if ($this->call($func) !== false) {
                Session::set('__chain', $next);
            }
            $this->skip(true);
        }

        return $this;
    }

    /**
     * Skip only run events and defaults answers,
     * but `berforeRun`, `afterRun` & `beforeCallbacks`, `afterCallbacks`
     * methods will be executed.
     *
     * @param boolean $status
     * @return void
     */
    public function skip(bool $status)
    {
        $this->skipRunEvents = $status;
    }

    /**
     * Will there be skipped run events?
     *
     * @return void
     */
    public function skipped()
    {
        return $this->skipRunEvents;
    }
}
