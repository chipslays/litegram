<?php

namespace Litegram\Debug;

use Litegram\Bot;
use Litegram\Support\Collection;

class Debug
{
    /**
     * Send `json` message to developer.
     *
     * @param array|\stdClass|string|int $data
     * @return Collection|null
     */
    public static function json($data)
    {
        $bot = Bot::getInstance();

        if (!$bot->config('debug.enable')) {
            return;
        }

        return $bot->json($data, $bot->config('debug.developer'));
    }

    /**
     * Send `print_r` message to developer.
     *
     * @param mixed $data
     * @return Collection|null
     */
    public static function print($data)
    {
        $bot = Bot::getInstance();

        if (!$bot->config('debug.enable')) {
            return;
        }

        return $bot->print($data, $bot->config('debug.developer'));
    }

    /**
     * Send `var_export` message to developer.
     *
     * @param mixed $data
     * @return Collection|null
     */
    public static function dump($data)
    {
        $bot = Bot::getInstance();

        if (!$bot->config('debug.enable')) {
            return;
        }

        return $bot->dump($data, $bot->config('debug.developer'));
    }
}
