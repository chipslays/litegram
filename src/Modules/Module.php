<?php

namespace Litegram\Modules;

use Litegram\Bot;

abstract class Module
{
    /**
     * @var \Litegram\Bot
     */
    protected static $bot;

    /** 
     * @var \Chipslays\Collection\Collection
     */

    protected static $update;

    /** 
     * @var \Chipslays\Collection\Collection
     */
    protected static $config;

    public function __construct()
    {
        self::$bot = Bot::getInstance();
        self::$update = self::$bot->update();
        self::$config = self::$bot->config();
    }
}
