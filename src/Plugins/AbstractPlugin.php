<?php

namespace Litegram\Plugins;

use Litegram\Bot;
use Litegram\Support\Collection;

abstract class AbstractPlugin
{
    /**
     * The name of the method to call, for example, $bot->example()
     * @var string
     */
    public static $alias = 'example';

    /**
     * Array with aliases of plugins that should already be loaded.
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

    protected static $payload;

    /**
     * @var Collection
     */
    protected static $config;

    public function __construct()
    {
        self::$bot = Bot::getInstance();
        self::$payload = self::$bot->payload();
        self::$config = self::$bot->config();
    }

    /**
     * Check enabled or disabled plugin.
     *
     * @return boolean True - enabled, False - disabled.
     */
    public static function enabled(): bool
    {
        return self::$config->get('plugins.' . self::$alias . '.enable', false);
    }
}
