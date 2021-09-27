<?php

use Litegram\Bot;
use Litegram\Keyboard;
use Litegram\Debug\Debug;
use Litegram\Payload;
use Litegram\Support\Util;
use Litegram\Support\Validate;
use Litegram\Support\Collection;
use Litegram\Plugins\User;
use Litegram\Plugins\Cache;
use Litegram\Plugins\Storage;
use Litegram\Plugins\Logger;
use Litegram\Plugins\Session;
use Litegram\Plugins\Database;
use Litegram\Plugins\Localization;
use Litegram\Plugins\State;

if (!function_exists('bot')) {
    /**
     * Если передать параметры будет вызван метод Bot::auth().
     * Пустые параметры возвращают объект Bot.
     *
     * @param string|array $token Pass token/array for auth, or NULL for just return Bot object.
     * @param array $config
     * @return Bot
     */
    function bot($token = null, $config = [])
    {
        return $token === null
            ? Bot::getInstance()
            : Bot::getInstance()->auth($token, $config);
    }
}

if (!function_exists('method')) {
    /**
     * A universal executor of Telegram methods.
     *
     * @param string $method
     * @param array|null $parameters
     * @return Collection
     * @throws \Exception
     */
    function method(string $method, ?array $parameters = [])
    {
        return Bot::getInstance()->method($method, $parameters);
    }
}

if (!function_exists('forceReply')) {
    /**
     * @param string|null $placeholder Текст плейсхолдера в поле ввода
     * @param boolean $selective
     * @return void
     */
    function forceReply(?string $placeholder = null, bool $selective = false)
    {
        Keyboard::forceReply($placeholder, $selective);
    }
}

if (!function_exists('keyboard')) {
    /**
     * Если передать параметры будет вызван метод Keyboard::show().
     * Пустые параметры возвращают объект Keyboard.
     *
     * @param boolean $keyboard
     * @param string|null $placeholder Текст плейсхолдера в поле ввода
     * @param boolean $oneTime
     * @param boolean $resize
     * @param boolean $selective
     * @return string|Keyboard
     */
    function keyboard($keyboard = false, ?string $placeholder = null, $oneTime = false, $resize = true, $selective = false)
    {
        if (!func_num_args()) {
            return new Keyboard;
        }

        return Keyboard::show($keyboard, $placeholder, $oneTime, $resize, $selective);
    }
}

if (!function_exists('keyboard_hide')) {
    /**
     * @param boolean $selective
     * @return string
     */
    function keyboard_hide($selective = false)
    {
        return Keyboard::hide($selective);
    }
}

if (!function_exists('keyboard_add')) {
    /**
     * @param array $keyboards
     * @return void
     */
    function keyboard_add($keyboards = [])
    {
        Keyboard::add($keyboards);
    }
}

if (!function_exists('keyboard_set')) {
    /**
     * @param array $keyboards
     * @return void
     */
    function keyboard_set($keyboards = [])
    {
        Keyboard::set($keyboards);
    }
}

if (!function_exists('say')) {
    /**
     * @param string $text
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function say($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->say($text, $keyboard, $extra);
    }
}

if (!function_exists('reply')) {
    /**
     * @param string $text
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function reply($text, $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->reply($text, $keyboard, $extra);
    }
}

if (!function_exists('notify')) {
    /**
     * @param string $text
     * @param boolean $showAlert
     * @param array $extra
     * @return Collection
     */
    function notify($text = '', $showAlert = false, $extra = [])
    {
        return Bot::getInstance()->notify($text, $showAlert, $extra);
    }
}

if (!function_exists('action')) {
    /**
     * @param string $action
     * @param array $extra
     * @return Collection
     */
    function action($action = 'typing', $extra = [])
    {
        return Bot::getInstance()->action($action, $extra);
    }
}

if (!function_exists('dice')) {
    /**
     * @param string $emoji
     * @param string|array $keyboard
     * @param array $extra
     * @return Collection
     */
    function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return Bot::getInstance()->dice($emoji, $keyboard, $extra);
    }
}

if (!function_exists('payload')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|Payload
     */
    function payload($path = null, $default = null)
    {
        return $path ? Payload::get($path, $default) : new Payload;
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|Collection
     */
    function config($path = null, $default = null)
    {
        return Bot::getInstance()->config($path, $default);
    }
}

if (!function_exists('plural')) {
    /**
     * @param string|int $count
     * @param array $forms
     * @return string
     */
    function plural($count, array $forms)
    {
        return Util::plural($count, $forms);
    }
}

if (!function_exists('lang')) {
    /**
     * @param string|int $key
     * @param array|null $replacement
     * @param string|null $language
     * @return mixed Можно вернуть строку, массив и прочее.
     */
    function lang(string $key, array $replacement = null, string $language = null)
    {
        return Localization::get($key, $replacement, $language);
    }
}

if (!function_exists('util')) {
    /**
     * @return \Telegram\Support\Util
     */
    function util()
    {
        return new Util;
    }
}

if (!function_exists('cache')) {
    /**
     * @return Cache
     */
    function cache()
    {
        return new Cache;
    }
}

if (!function_exists('storage')) {
    /**
     * @return Storage
     */
    function storage()
    {
        return new Storage;
    }
}

if (!function_exists('state')) {
    /**
     * @return State
     */
    function state()
    {
        return new State;
    }
}

if (!function_exists('user')) {
    /**
     * @return \stdClass|User
     */
    function user($userId = null)
    {
        return $userId ? User::getDataById($userId) : new User;
    }
}

if (!function_exists('logger')) {
    /**
     * @return Logger
     */
    function logger()
    {
        return new Logger;
    }
}

if (!function_exists('session')) {
    /**
     * @return Session
     */
    function session()
    {
        return new Session;
    }
}

if (!function_exists('db')) {
    /**
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Capsule\Manager
     */
    function db($table = null)
    {
        return $table ? Database::table($table) : Database::class;
    }
}

if (!function_exists('bot_print')) {
    /**
     * @param mixed $data
     * @param string|int|null $userId
     * @return Collection
     */
    function bot_print($data, $userId = null)
    {
        return Bot::getInstance()->print($data, $userId);
    }
}

if (!function_exists('bot_dump')) {
    /**
     * @param mixed $data
     * @param string|int|null $userId
     * @return Collection
     */
    function bot_dump($data, $userId = null)
    {
        return Bot::getInstance()->dump($data, $userId);
    }
}

if (!function_exists('bot_json')) {
    /**
     * @param array|string|int $data
     * @param string|int|null $userId
     * @return Collection
     */
    function bot_json($data, $userId = null)
    {
        return Bot::getInstance()->json($data, $userId);
    }
}

if (!function_exists('wait')) {
    /**
     * Ждать определенное время в секундах (поддерживает float).
     *
     * @param integer|float $seconds
     * @return boolean
     */
    function wait($seconds = 1)
    {
        Util::wait($seconds);
    }
}

if (!function_exists('debug_print')) {
    /**
     * @param mixed $text
     * @return Collection
     */
    function debug_print($data)
    {
        return Debug::print($data);
    }
}

if (!function_exists('debug_json')) {
    /**
     * @param array|string|int $data
     * @return Collection
     */
    function debug_json($data)
    {
        return Debug::json($data);
    }
}

if (!function_exists('random_code')) {
    /**
     * Генерирует случайную строку.
     *
     * @param integer $lenght
     * @param array $chars
     * @return string
     */
    function random_code(int $lenght = 6, array $chars = null)
    {
        return Util::getRandomCode($lenght, $chars);
    }
}

if (!function_exists('chain')) {
    /**
     * Set current chain name.
     *
     * @param string|null $name
     * @return Bot
     * @throws \Exception
     */
    function chain(?string $name) {
        return Bot::getInstance()->setChain($name);
    }
}

if (!function_exists('on')) {
    /**
     * @param array|string $event
     * @param callable|string|array $callback
     * @param integer $sort
     * @return Bot
     */
    function on($event, $callback, int $sort = LITEGRAM_DEFAULT_EVENT_SORT) {
        return Bot::getInstance()->on($event, $callback, $sort);
    }
}

if (!function_exists('is')) {
    /**
     * This is short alias for `Validate` class.
     *
     * Use cases:
     *
     * `is()->email()->validate('example@email.com')`
     *
     * `is('email')->validate('example@email.com')`
     *
     * `is('contains', 'crab')->validate('chips with crab flavor')`
     *
     * `is()->contains('crab')->validate('chips with crab flavor')`
     *
     * @see https://respect-validation.readthedocs.io/en/latest/
     *
     * @param string|null $method
     * @param string|array|null $arguments
     * @return Validate
     */
    function is(?string $rule = null, $arguments = null) {
        return $rule
            ? Validate::create()->__call($rule, (array) $arguments)
            : Validate::create();
    }
}

if (!function_exists('validate')) {
    /**
     * Simple direct validate.
     *
     * Use cases:
     *
     * `validate('email', 'example@email.com')`
     *
     * `validate('contains', 'crab, 'chips with crab flavor')`
     *
     * @param string $rule
     * @param array|string $data
     * @return bool
     */
    function validate(string $rule, $value1, $value2 = null) {
        if ($value2 !== null) {
            return Validate::create()->__call($rule, (array) $value1)->validate($value2);
        } else {
            return Validate::create()->__call($rule, [])->validate($value1);
        }
    }
}

if (!function_exists('cli')) {
    function cli() {
        return Bot::getInstance()->cli;
    }
}


