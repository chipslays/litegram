<?php

namespace Litegram\Modules;

class Session extends Module
{
    /**
     * @var string
     */
    private static $alias = 'session';

    /**
     * @var string
     */
    private static $userId;

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
        if (!self::$config->get('modules.session.enable')) {
            return;
        }

        self::$userId = (string) self::$update->get('*.from.id');
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
        $name = self::$userId . '_' . md5($name);
        Store::set($name, $value);
    }

    /**
     * Get value from session of current user.
     *
     * @param string $name
     * @param mixed $default
     * @return void
     */
    public static function get(string $name, $default = null)
    {
        $name = self::$userId . '_' . md5($name);
        return Store::get($name, $default);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function has(string $name): bool
    {
        $name = self::$userId . '_' . md5($name);
        return Store::has($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public static function delete(string $name): void
    {
        $name = self::$userId . '_' . md5($name);
        Store::delete($name);
    }
}