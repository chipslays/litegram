<?php

namespace Litegram\Modules;

class Example extends Module
{
    /**
     * Имя метода для обращения, например, $bot->example()
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
     * Выполняется один раз при добавлении модуля в методе addModule.
     * 
     * @return void
     */
    public static function boot(): void
    {
        if (!self::$config->get('modules.example.enable')) {
            return;
        }
    }

    /**
     * Выполняется один раз непосредственно перед run методом.
     * 
     * @return void
     */
    public static function beforeRun(): void
    {
        // code...
    }

    /**
     * Выполняется один раз после выполнения run метода. 
     * 
     * @return void
     */
    public static function afterRun(): void
    {
        // code...
    }
}
