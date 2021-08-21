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
     * @return void
     */
    public static function set(string $name, $value = null): void
    {
        $name = self::buildName($name);
        Storage::set($name, $value);
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
        $data = Storage::get($name, []);
        $data[$key] = $value;
        Storage::set($name, $data);
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
        return Storage::get($name, $default);
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
        return Storage::pull($name, $default);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function has(string $name): bool
    {
        $name = self::buildName($name);
        return Storage::has($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public static function delete(string $name): void
    {
        $name = self::buildName($name);
        Storage::delete($name);
    }

    protected static function buildName($name)
    {
        return self::$userId . '_' . md5($name);
    }
}