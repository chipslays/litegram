<?php

namespace Litegram\Modules;

use Litegram\Bot;
use Litegram\Support\Collection;

abstract class Module
{
    /**
     * Array with aliases of modules that should already be loaded.
     *
     * @var array
     */
    public static $depends = [];

    /**
     * @var Bot
     */
    protected static $bot;

    /**
     * @var Collection
     */

    protected static $update;

    /**
     * @var Collection
     */
    protected static $config;



    public function __construct()
    {
        self::$bot = Bot::getInstance();
        self::$update = self::$bot->update();
        self::$config = self::$bot->config();
    }
}