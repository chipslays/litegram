<?php

namespace Litegram\Modules;

class Session extends Module
{
    /**
     * @var string
     */
    public static $alias = 'session';

    /**
     * @var array
     */
    public static $depends = [
        'store',
    ];

    /**
     * @var string
     */
    private static $userId;

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (!self::$config->get('modules.session.enable')) {
            return;
        }

        if (self::$config->get('modules.user.enable') && !self::$bot->isModuleExists('user')) {

            throw new \Exception("Config `modules.user.enable=true`, but `user` module not loaded. Please load `user` module before `session`.");
        }

        if (self::$config->get('modules.user.enable')) {
            self::$userId = User::get('user_id');
        } else {
            self::$userId = (string) self::$update->get('*.from.id');
        }
    }

    /**
     * Set session data for current user.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function set(string $name, $value = null): void
    {
        $name = self::buildName($name);
        Store::set($name, $value);
    }

    /**
     * Push to array new item `key => value`.
     *
     * @param string $name
     * @param string|int $key
     * @param mixed $value
     * @return void
     */
    public static function push(string $name, $key, $value): void
    {
        $name = self::buildName($name);
        $data = Store::get($name, []);
        $data[$key] = $value;
        Store::set($name, $data);
    }

    /**
     * Get value from session of current user.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $name = self::buildName($name);
        return Store::get($name, $default);
    }

    /**
     * Get and delete value from session.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function pull(string $name, $default = null)
    {
        $name = self::buildName($name);
        return Store::pull($name, $default);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function has(string $name): bool
    {
        $name = self::buildName($name);
        return Store::has($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public static function delete(string $name): void
    {
        $name = self::buildName($name);
        Store::delete($name);
    }

    protected static function buildName($name)
    {
        return self::$userId . '_' . md5($name);
    }
}
