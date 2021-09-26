<?php

namespace Litegram;

use Litegram\Plugins\Session;
use Litegram\Plugins\Database;
use Litegram\Plugins\Localization;
use Litegram\Traits\Ask;
use Litegram\Traits\Chain;
use Litegram\Traits\Filter;
use Litegram\Traits\Events;
use Litegram\Traits\Plugins;
use Litegram\Traits\Utility;
use Litegram\Traits\Handlers;
use Litegram\Traits\Middleware;
use Litegram\Traits\Components;
use Litegram\Traits\Http\Request;
use Litegram\Traits\Telegram\Aliases;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;
use Litegram\Exceptions\LitegramException;
use Litegram\Support\Collection;
use Sauce\Traits\Call;
use Sauce\Traits\Mappable;
use Sauce\Traits\Singleton;

class Bot
{
    use Ask;
    use Filter;
    use Utility;
    use Events;
    use Plugins;
    use Aliases;
    use Replies;
    use Methods;
    use Request;
    use Handlers;
    use Mappable;
    use Singleton;
    use Middleware;
    use Components;
    use Call {
        Call::__call_function as call;
    }

    /**
     * @var string]
     */
    private $token;

    /**
     * @var Collection
     */
    private $config = [
        'bot' => [
            'token' => '1234567890:BOT_TOKEN',
            'handler' => 'https://example.com/webhook/handler.php',
            'name' => 'Litegram',
            'username' => 'litegram_bot',
            'timezone' => 'Europe/Samara',
            'timelimit' => 120,
            'version' => '1.0.0',
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
        'errors' => [
            'path' => '/logs/errors',
            'telegram' => false,
            'php' => false,
            'php_level' => E_ALL,
        ],
        'plugins' => [
            'storage' => [
                'driver' => null, // null - store data in RAM (useful for long-poll)
                'drivers' => [
                    'file' => [
                        'dir' => null,
                    ],
                    'database' => [],
                ],
            ],
            'cache' => [
                'driver' => 'memcached',
                'drivers' => [
                    'memcached' => [
                        'host'  => 'localhost',
                        'port' => '11211',
                    ],
                    'redis' => [
                        'host'  => '127.0.0.1',
                        'port' => '6379',
                    ],
                ],
            ],
            'database' => [
                'driver' => 'mysql',
                'drivers' => [
                    'sqlite' => [
                        'prefix' => 'litegram_',
                        'database' => 'path/to/database.sqlite',
                    ],
                    'mysql' => [
                        'host' => 'localhost',
                        'prefix' => 'litegram_',
                        'database' => 'litegram',
                        'username' => 'litegram',
                        'password' => 'litegram',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                    ],
                    'pgsql' => [
                        'host' => 'localhost',
                        'prefix' => 'litegram_',
                        'database' => 'litegram',
                        'username' => 'litegram',
                        'password' => 'litegram',
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                    ],
                ],
            ],
            'user' => [
                'floot_time' => 0,
                'data' => [],
            ],
            'localization' => [
                'driver' => 'yaml',
                'drivers' => [
                    'yaml' => [
                        'path' => '/path/to/locales/yaml',
                    ],
                    'json' => [
                        'path' => '/path/to/locales/json',
                    ],
                ],
                'fallback' => 'en',
            ],
            'logger' => [
                'path' => '/path/to/logs',
                'payload_log' => false,
                'pastly' => [
                    'token' => '1627406735:rO0jr-wMn5ZleI6hiKfKQ4aJZyYFaKN5TDoWmj-5V2',
                    'title' => 'Litegram Log',
                    'type' => 'private',
                    'expiration' => null,
                    'password' => '',
                    'syntax' => 'text',
                ],
            ],
        ],
        'components' => [
            'vendor.component' => [
                'enable' => false,
                'entrypoint' => 'components/vendor/component.php',
            ],
        ],
    ];

    /**
     * @var Collection
     */
    private $payload = null;

    /**
     * @var array
     */
    private $commandTags = ['/', '.', '!'];

    /**
     * @var Cli
     */
    public $cli;

    final protected function __construct()
    {
    }

    /**
     * @param string|array $token String - token, Array - config
     * @param array $config Array of config
     * @return Bot
     */
    public function auth($token, array $config = null)
    {
        // auth by config as array
        if (is_array($token)) {
            $config = array_replace_recursive((array) $this->config, $token);
            $this->config = new Collection($config);
            $this->token = $this->config->get('bot.token');
        }

        // auth by token and config
        else {
            $this->token = $token;
            $this->config = new Collection((array_replace_recursive((array) $this->config, $config)));
        }

        date_default_timezone_set($this->config('bot.timezone'));

        if ($this->config('errors.php') && $logPath = $this->config('errors.path')) {
            $logPath = rtrim($logPath, '/\\');
            error_reporting($this->config('errors.php_level', E_ALL));
            ini_set("log_errors", true);
            ini_set("error_log", "{$logPath}/php_errors.log");
        }

        $this->cli = new Cli;

        Payload::make([]);

        return $this;
    }

    /**
     * @param string|null $path
     * @param mixed $default
     * @return mixed|Collection Return Collection if nothing pass
     */
    public function payload(?string $path = null, $default = null)
    {
        return $path ? $this->data->get($path, $default) : $this->data;
    }

    /**
     * @param string|null $path
     * @param mixed $default
     * @return mixed|Collection Return Collection if nothing pass
     */
    public function config(?string $path = null, $default = null)
    {
        return $path ? $this->config->get($path, $default) : $this->config;
    }

    /**
     * Skip only run events and defaults answers,
     * but `berforeRun`, `afterRun` & `beforeCallbacks`, `afterCallbacks`
     * methods will be executed.
     *
     * @param boolean $status
     * @return void
     */
    public function skip(bool $status = true)
    {
        $this->skipRun = $status;
    }

    /**
     * Will there be skipped run events?
     *
     * @return void
     */
    public function skipped()
    {
        return $this->skipRun;
    }

    /**
     * Is there an payload from Telegram.
     *
     * @return boolean
     */
    public function hasPayload(): bool
    {
        return $this->data !== [];
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

    /**
     * Returns an array with command prefixes.
     *
     * @return array
     */
    public function getCommandTags()
    {
        return $this->commandTags;
    }
}
