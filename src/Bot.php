<?php

namespace Litegram;

use Litegram\Exceptions\LitegramException;
use Litegram\Plugins\Session;
use Litegram\Support\Collection;
use Litegram\Traits\Ask;
use Litegram\Traits\Events;
use Litegram\Traits\Http\Request;
use Litegram\Traits\Plugins;
use Litegram\Traits\Telegram\Aliases;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;
use Sauce\Traits\Call;
use Sauce\Traits\Mappable;
use Sauce\Traits\Singleton;
use stdClass;

/**
 * @property Cli $cli
 */
class Bot
{
    use Ask;
    use Plugins;
    use Aliases;
    use Replies;
    use Methods;
    use Events;
    use Request;
    use Singleton;
    use Mappable;
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
            'token' => null,
            'parse_mode' => 'html',
            'timezone' => 'Europe/Samara',
            'timelimit' => 1800,
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
                        'database' => 'path/to/database.sqlite',
                    ],
                    'mysql' => [
                        'host'      => 'localhost',
                        'prefix'    => 'litegram',
                        'database'  => 'litegram',
                        'username'  => 'litegram',
                        'password'  => 'litegram',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                    ],
                    'pgsql' => [
                        'host'      => 'localhost',
                        'prefix'    => 'litegram',
                        'database'  => 'litegram',
                        'username'  => 'litegram',
                        'password'  => 'litegram',
                        'charset'   => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                    ],
                ],
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
                'autolog' => false,
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
    ];

    /**
     * @var Collection
     */
    private $payload = null;

    /**
     * @var array
     */
    private $commandTags = ['/', '.', '!'];

    final protected function __construct()
    {
    }

    /**
     * @param string|array $token String - token, Array - config
     * @param array $config Array of config
     * @return Bot
     */
    public function make($token, array $config = null)
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
     * Handle update from Telegram or set force update for somethere.
     *
     * @param array|string|stdClass|Collection $payload
     * @return Bot
     * @throws LitegramException
     */
    public function webhook($payload = null)
    {
        if ($payload) {
            $this->setEventData($payload);
        } else {
            $payload = file_get_contents('php://input');
            if ($payload && $payload !== '') {
                $this->setEventData($payload);
            }
        }

        if (php_sapi_name() !== 'cli') {
            $this->finishRequest($this->config('bot.timelimit', 1800));
        }

        if (!$this->hasUpdate()) {
            throw new LitegramException("All right, but not have payload from Telegram.", 1);
        }

        Payload::make($this->payload()->toArray());

        $this->defaultIdForReply = $this->payload('*.chat.id', $this->payload('*.from.id'));

        return $this;
    }

    /**
     * Handle updates (long-polling).
     *
     * @param callable|string $callback
     * @param array $extra
     * @return void
     */
    public function longpoll($callback = null, $extra = [])
    {
        $this->cli->log('Long-polling started...');

        $offset = 0;

        while (true) {
            $updates = $this->api('getUpdates', array_merge([
                'offset' => $offset,
                'limit' => '25',
                'timeout' => 0,
            ], $extra));

            foreach ($updates->get('result') as $update) {
                $this->setEventData(new Collection($update));

                $offset = $update['update_id'] + 1;

                Payload::make($this->payload()->toArray());

                $this->defaultIdForReply = $this->payload('*.chat.id', $this->payload('*.from.id'));

                if ($callback) {
                    $this->call($callback, [$this->payload(), $this]);
                }

                // check answer for our question
                $question = Session::pull('litegram:question');
                if ($question && !$this->in($question['except'], $this->payload()->toArray())) {

                    // callback
                    if ($this->in($question['accept'], $this->payload()->toArray())) {
                        if ($this->call($question['callback'], [$this->payload(), $this]) === false) {
                            // if we not accept this answer, reqeustion
                            Session::set('litegram:question', $question);
                        }
                    }

                    // fallback
                    else {
                        $this->call($question['fallback'], [$this->payload(), $this]);
                        Session::set('litegram:question', $question);
                    }

                    $this->skip();
                }

                $this->run();

                // resets for new handle
                $this->events = [];
                $this->skip(false);
            }
        }
    }

    /**
     * Before call a massive operations, use this method for non-block bot answers.
     *
     * @param integer $timeLimit Execution script time limit in seconds.
     * @return Bot
     */
    public function finishRequest($timelimit = 1800)
    {
        set_time_limit($timelimit);
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
     * Is there an update from Telegram.
     *
     * @return boolean
     */
    public function hasUpdate(): bool
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

    public function getCommandTags()
    {
        return $this->commandTags;
    }

    /**
     * Like 'on' method, but returns `bool` if items in array `$hasystack` default is current update.
     *
     * @param string|array $needles
     * @param array $haystack
     * @return bool
     */
    public function in($needles, array $haystack = null)
    {
        $haystack = is_array($haystack) ? new Collection($haystack) : $this->payload();
        foreach ($needles as $item) {
            foreach ((array) $item as $key => $value) {
                /**
                 * Force execute event
                 * on(true, ..., ...)
                 */
                if ($value === true) {
                    return true;
                    break;
                }

                /**
                 * [['key' => 'value'], ...]
                 */
                if (is_array($value)) {
                    $key = key($value);
                    $value = $value[$key];
                }

                /**
                 * ['key'] or 'key'
                 */
                if (is_numeric($key) && $haystack->has($value)) {
                    return true;
                    break;
                }

                /**
                 * Get value by key, if not exists then skip iteration.
                 * ['key' => 'value']
                 */
                if (!$received = $haystack->get($key)) {
                    continue;
                }

                /**
                 * ['key' => 'value']
                 */
                if ($received == $value) {
                    return true;
                    break;
                }

                /**
                 * ['key' => 'my name is {name}']
                 *
                 * command(?: (.*?))?(?: (.*?))?$
                 *
                 * {text} - required text
                 * {:text?} - optional text
                 */
                $value = preg_replace('~.?{:(.*?)\?}~', '(?: (.*?))?', $value);
                $pattern = '~^' . preg_replace('/{(.*?)}/', '(.*?)', $value) . '$~';

                if (@preg_match_all($pattern, $received, $matches)) {
                    return true;
                    break;
                }

                /**
                 * ['key' => '/regex/i]
                 */
                if (@preg_match_all($value, $received, $matches)) {
                    return true;
                    break;
                }
            }
        }

        return false;
    }
}
