<?php

namespace Litegram\Plugins;

use Litegram\Bot;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @method mixed setAsGlobal()
 * @method mixed bootEloquent();
 */
class Database extends Capsule
{
    /**
     * @var string
     */
    public static $alias = 'database';

    /**
     * Array with aliases of plugins that should already be loaded.
     *
     * @var array
     */
    public static $depends = [];

    /**
     * @return void
     */
    public function boot(): void
    {
        $config = Bot::getInstance()->config();

        $driver = $config->get('plugins.database.driver');
        $config = $config->get("plugins.database.drivers.{$driver}");
        $config['driver'] = $driver;

        $this->addConnection($config);
        $this->setAsGlobal();
        $this->bootEloquent();
    }
}