<?php

namespace Litegram\Modules;

class Example extends Module
{
    /**
     * Имя метода для обращения, например, $bot->example()
     * @var string
     */
    public static $alias = 'example';

    /**
     * Array with aliases of modules that should already be loaded.
     *
     * @var array
     */
    public static $depends = [
        'another',
        'modules',
    ];

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
