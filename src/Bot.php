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
use Sauce\Traits\Call;
use Sauce\Traits\Mappable;
use Sauce\Traits\Singleton;

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
    use Singleton;
    use Mappable;
    use Call;

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

    protected function __construct()
    {
    }

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
     * @var int
     */
    private $__startAt;

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
     * @param array|string $parameters
     * @return void
     */
    public function call($fn, $parameters = [])
    {
        $this->__call_function($fn, (array) $parameters);
    }

    /**
     * Set current chain name.
     *
     * @param string|null $name
     * @return Bot
     * @throws \Exception
     */
    public function setChain(?string $name)
    {
        if (!$this->isModuleExists('session')) {
            throw new \Exception("Please add `session` module before start chain.");
        }

        Session::set('__chain', $name);

        return $this;
    }

    /**
     * Chain dialog flow.
     *
     * @param string $current
     * @param string|null $next
     * @param callable|string $func Function must return `false` for prevent `$next` step.
     * @param array $excepts Array of `path=value` for skip chain. E.g. `['message.text' => '/cancel']`.
     * @return Bot
     */
    public function chain(string $current, ?string $next, $func, array $excepts = [])
    {
        if ($excepts !== []) {
            foreach ($excepts as $path => $value) {
                if (Update::get($path) == $value) {
                    return $this;
                }
            }
        }

        if (Session::get('__chain') == $current && !$this->skipped()) {
            if ($this->call($func) !== false) {
                if ($next === null || $next === false) {
                    Session::delete('__chain');
                } else {
                    Session::set('__chain', $next);
                }
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
