<?php

namespace Litegram\Modules;

class Example extends Module
{
    /**
     * @var string
     */
    private static $alias = 'example';

    /**
     * @return string
     */
    public static function getAlias(): string
    {
        return self::$alias;
    }

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (!self::$config->get('modules.example.enable')) {
            return;
        }
    }

    /**
     * @return void
     */
    public static function beforeRun(): void
    {
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
    }
}
