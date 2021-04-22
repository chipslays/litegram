<?php

use Litegram\Bot;

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
