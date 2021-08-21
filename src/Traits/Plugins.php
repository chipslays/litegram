<?php

namespace Litegram\Traits;

use Litegram\Exceptions\LitegramPluginException;

trait Plugins
{
    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @param string $class
     * @param array $parameters Parameters for `boot` method.
     * @return static
     */
    public function plugins($plugins)
    {
        $this->plugins = [];

        foreach ($plugins as $item) {

            // [[First::class, ['param1', 'param2']], [Second::class, ['param1', 'param2']]]
            if (is_array($item)) {
                $plugin = $item[0];
                $bootParameters = $item[1] ?? [];
            }

            // [First::class, Second::class]
            else {
                $plugin = $item;
                $bootParameters = [];
            }

            foreach ($plugin::$depends as $needPlugin) {
                if (!$this->isPluginExists($needPlugin)) {
                    throw new LitegramPluginException("Please, add `{$needPlugin}` plugin before `{$plugin}` plugin.");
                }
            }

            $class = new $plugin;
            if (method_exists($class, 'boot')) {
                call_user_func_array([$class, 'boot'], (array) $bootParameters);
            }

            if (!property_exists($class, 'alias')) {
                throw new LitegramPluginException("Missed required `alias` property in plugin {$plugin}", 1);
            }

            $alias = $class::$alias;

            // if (property_exists($this, $alias) || in_array($alias, $this->plugins)) {
            //     throw new LitegramPluginException("Cannot override exists property `{$alias}`.");
            // }

            $this->$alias = $class;
            $this->plugins[] = $alias;
        }

        return $this;
    }

    public function isPluginExists($plugin)
    {
        return in_array($plugin, $this->plugins);
    }

    /**
     * Call plugin like $bot->plugin('db')->table(...)
     *
     * @param string $name
     * @return object
     */
    public function plugin(string $alias)
    {
        if (!in_array($alias, $this->plugins)) {
            throw new LitegramPluginException("Plugin `{$alias}` not exists.");
        }

        return $this->$alias;
    }
}