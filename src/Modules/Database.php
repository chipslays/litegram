<?php

namespace Litegram\Modules;

use Litegram\Bot;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @method mixed setAsGlobal()
 */
class Database extends Capsule
{
    /**
     * @var string
     */
    private static $alias = 'db';

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
    public function boot(): void
    {

        $config = Bot::getInstance()->config();

        if (!$config->get('modules.database.enable')) {
            return;
        }

        $driver = $config->get('modules.database.driver');
        $config = $config->get("modules.database.{$driver}");
        $config['driver'] = $driver;

        $this->addConnection($config);
        $this->setAsGlobal();
    }
}
