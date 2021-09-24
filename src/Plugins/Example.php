<?php

namespace Litegram\Plugins;

class Example extends AbstractPlugin
{
    /**
     * Имя метода для обращения, например, $bot->example()
     * @var string
     */
    public static $alias = 'example';

    /**
     * Array with aliases of plugins that should already be loaded.
     *
     * @var array
     */
    public static $depends = ['first', 'second'];

    /**
     * Executed once when adding a module in the `with` method.
     *
     * @return void
     */
    public static function boot(): void
    {
        if (!self::enabled()) {
            return;
        }

        // Do something awesome here...
    }

    /**
     * @return void
     */
    public static function beforeRun(): void
    {
        // do something
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        // do something
    }
}
