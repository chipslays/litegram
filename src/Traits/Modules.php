<?php

namespace Litegram\Traits;

trait Modules
{
    /**
     * @var array
     */
    private $modules = [];

    /**
     * @param string $class
     * @param array $parameters Parameters for `boot` method.
     * @return static
     */
    public function addModule($module, array $parameters = [])
    {
        foreach ($module::$depends as $needModule) {
            if (!$this->isModuleExists($needModule)) {
                throw new \Exception("Please, add `{$needModule}` module before add `{$module}` module.");
            }
        }

        $class = new $module;
        if (method_exists($class, 'boot')) {
            call_user_func_array([$class, 'boot'], $parameters);
        }

        if (!property_exists($class, 'alias')) {
            throw new \Exception("Missed required `alias` property in module {$module}", 1);
        }

        $alias = $class::$alias;

        if (property_exists($this, $alias) || in_array($alias, $this->modules)) {
            throw new \Exception("Cannot overide exists property `{$alias}`.");
        }

        $this->$alias = $class;
        $this->modules[] = $alias;

        return $this;
    }

    public function isModuleExists($module)
    {
        return in_array($module, $this->modules);
    }

    /**
     * Call module like $bot->module('db')->table(...)
     *
     * @param string $name
     * @return object
     */
    public function module(string $alias)
    {
        if (!in_array($alias, $this->modules)) {
            throw new \Exception("Module `{$alias}` not exists.");
        }

        return $this->$alias;
    }
}