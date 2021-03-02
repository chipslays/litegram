<?php

namespace Litegram\Debug;

use Litegram\Bot;

class Debug
{
    /**
     * @param array|string|int $data
     * @return Collection
     */
    public static function json($data)
    {
        $bot = Bot::getInstance();
        
        if (!$bot->config('debug.enable')) {
            return;
        }
        
        return $bot->api('sendMessage', [   
            'chat_id' => $bot->config('debug.developer'),
            'text' => '<code>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</code>',
            'parse_mode' => 'html',
        ]);
    }

    /**
     * @param mixed $text
     * @return Collection
     */
    public static function print($data)
    {
        $bot = Bot::getInstance();

        if (!$bot->config('debug.enable')) {
            return;
        }

        return $bot->api('sendMessage', [   
            'chat_id' => $bot->config('debug.developer'),
            'text' => print_r($data, true),
            'parse_mode' => 'html',
        ]);
    }
}