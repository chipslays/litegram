<?php

namespace Litegram;

use Litegram\Traits\Http\Request;
use Litegram\Traits\Telegram\Methods;
use Litegram\Traits\Telegram\Replies;
use Litegram\Traits\Telegram\Aliases as TelegramAliases;
use Litegram\Traits\Telegram\Events;
use Litegram\Traits\Middleware;

use Container\Container;
use Chipslays\Collection\Collection;
use Chipslays\Event\EventTrait as BaseEvent;
use Litegram\Modules\User;

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
    use Middleware;
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
    private $config = [];

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

    public function hasUpdate()
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
