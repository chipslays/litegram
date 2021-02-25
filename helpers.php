<?php

use Chipslays\Collection\Collection;
use Litegram\Bot;
use Litegram\Keyboard;
use Litegram\Debug\Debug;
use Litegram\Support\Util;
use Litegram\Modules\Logger;
use Litegram\Modules\Cache;
use Litegram\Modules\Store;
use Litegram\Modules\Database;
use Litegram\Modules\Localization;
use Litegram\Modules\Session;
use Litegram\Modules\State;
use Litegram\Modules\User;

if (!function_exists('bot')) {
    /**
     * Если передать параметры будет вызван метод Bot::auth().
     * Пустые параметры возвращают объект Bot.
     *
     * @param string|null $token
     * @param array|null $config
     * @return Bot
     */
    function bot(string $token = null, array $config = null)
    {
        return $token === null && $config === null ? Bot::getInstance() : Bot::getInstance()->auth($token, $config);
    }
}

if (!function_exists('keyboard')) {
    /**
     * Если передать параметры будет вызван метод Keyboard::show().
     * Пустые параметры возвращают объект Keyboard.
     *
     * @param boolean $keyboard
     * @param boolean $oneTime
     * @param boolean $resize
     * @param boolean $selective
     * @return Keyboard|string
     */
    function keyboard($keyboard = false, $oneTime = false, $resize = true, $selective = false)
    {
        if (!func_num_args()) {
            return new Keyboard;
        }

        return Keyboard::show($keyboard, $oneTime, $resize, $selective);
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

if (!function_exists('update')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return string|int|Collection
     */
    function update($key = null, $default = null)
    {
        return Bot::getInstance()->update()->get($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed $default
     * @return string|int|Collection
     */
    function config($key = null, $default = null)
    {
        return Bot::getInstance()->config($key, $default);
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
    function helper()
    {
        return new Util;
    }
}

if (!function_exists('cache')) {
    /**
     * @return \Telegram\Modules\Cache
     */
    function cache()
    {
        return new Cache;
    }
}

if (!function_exists('store')) {
    /**
     * @return \Telegram\Modules\Store
     */
    function store()
    {
        return new Store;
    }
}

if (!function_exists('user')) {
    /**
     * @return \Telegram\Modules\User
     */
    function user()
    {
        return new User;
    }
}

if (!function_exists('state')) {
    /**
     * @return \Telegram\Modules\State
     */
    function state()
    {
        return new State;
    }
}

if (!function_exists('logger')) {
    /**
     * @return \Telegram\Modules\Logger
     */
    function logger()
    {
        return new Logger;
    }
}

if (!function_exists('session')) {
    /**
     * @return \Telegram\Modules\Session
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
