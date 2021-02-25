<?php

use Litegram\Bot;
use Litegram\Keyboard;

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
