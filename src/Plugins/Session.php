<?php

namespace Litegram\Plugins;

/**
 * Plugin for manipulating user-bound data (where the payload came from)
 */
class Session extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'session';

    /**
     * @var array
     */
    public static $depends = [
        'storage',
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
        if (!self::enabled()) {
            return;
        }

        if (self::$bot->isPluginExists('user')) {
            self::$userId = User::get('user_id');
        } else {
            self::$userId = (string) self::$payload->get('*.from.id');
        }
    }

    /**
     * Set session data for current user.
     *
     * @param string $name
     * @param mixed $value
     * @param string $driver
     * @return void
     */
    public static function set(string $name, $value = null, string $driver = null): void
    {
        $name = self::buildName($name);
        Storage::set($name, $value, $driver);
    }

    /**
     * Push to array new item `key => value`.
     *
     * @param string $name
     * @param string|int $key
     * @param mixed $value
     * @param string $driver
     * @return void
     */
    public static function push(string $name, $key, $value, string $driver = null): void
    {
        $name = self::buildName($name);
        $data = Storage::get($name, []);
        $data[$key] = $value;
        Storage::set($name, $data, $driver);
    }

    /**
     * Get value from session of current user.
     *
     * @param string $name
     * @param mixed $default
     * @param string $driver
     * @return mixed
     */
    public static function get(string $name, $default = null, string $driver = null)
    {
        $name = self::buildName($name);
        return Storage::get($name, $default, $driver);
    }

    /**
     * Get and delete value from session.
     *
     * @param string $name
     * @param mixed $default
     * @param string $driver
     * @return mixed
     */
    public static function pull(string $name, $default = null, string $driver = null)
    {
        $name = self::buildName($name);
        return Storage::pull($name, $default, $driver);
    }

    /**
     * @param string $name
     * @param string $driver
     * @return boolean
     */
    public static function has(string $name, string $driver = null): bool
    {
        $name = self::buildName($name);
        return Storage::has($name, $driver);
    }

    /**
     * @param string $name
     * @param string $driver
     * @return void
     */
    public static function delete(string $name, string $driver = null): void
    {
        $name = self::buildName($name);
        Storage::delete($name, $driver);
    }

    protected static function buildName($name)
    {
        return self::$userId . '_' . md5($name);
    }
}