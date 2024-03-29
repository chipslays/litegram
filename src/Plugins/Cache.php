<?php

namespace Litegram\Plugins;

use Litegram\Exceptions\LitegramPluginException;
use Litegram\Plugins\AbstractPlugin;
use Memcached;
use Redis;

define('LITEGRAM_DEFAULT_CACHE_TIME', 300);

/**
 * Plugin for work with Memcached or Redis.
 */
class Cache extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'cache';

    /**
     * @var Redis|Memcached
     */
    private static $cache;

    /**
     * @var string
     */
    private static $driver;

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (!$config = self::$config->get('plugins.cache')) {
            return;
        }

        self::$driver = $config['driver'];

        switch (strtolower(self::$driver)) {
            case 'memcached':
                if (!class_exists('Memcached')) {
                    throw new LitegramPluginException("Class `Memcached` not exists, please first install Memcached or change cache driver.");
                }

                self::$cache = new Memcached;
                self::$cache->addServer($config['drivers'][self::$driver]['host'], $config['drivers'][self::$driver]['port']);

                break;

            case 'redis':
                if (!class_exists('Redis')) {
                    throw new LitegramPluginException("Class `Redis` not exists, please first install Redis or change cache driver.");
                }

                self::$cache = new Redis;
                self::$cache->connect($config['drivers'][self::$driver]['host'], $config['drivers'][self::$driver]['port']);

                break;

            default:
                throw new LitegramPluginException("Invalid Cache plugin driver: " . self::$driver);
                break;
        }
    }

    public static function set(string $name, $value, ?int $time = null)
    {
        switch (self::$driver) {
            case 'memcached':
                $time = $time ?? time() + LITEGRAM_DEFAULT_CACHE_TIME;
                self::$cache->set($name, $value, $time);
                break;

            case 'redis':
                $time = $time ?? LITEGRAM_DEFAULT_CACHE_TIME;
                self::$cache->set($name, $value, $time);
                break;

            default:
                # code...
                break;
        }
    }

    public static function get(string $name, $default = null)
    {
        switch (self::$driver) {
            case 'memcached':
                $value = self::$cache->get($name);
                return $value !== false ? $value : $default;
                break;

            case 'redis':
                $value = self::$cache->get($name);
                return $value !== false ? $value : $default;
                break;

            default:
                # code...
                break;
        }
    }

    public static function has(string $name): bool
    {
        switch (self::$driver) {
            case 'memcached':
                $cache = self::$cache;
                $cache->get($name);
                return $cache->getResultCode() !== $cache::RES_NOTFOUND;
                break;

            case 'redis':
                return self::$cache->exists($name) > 0;
                break;

            default:
                return false;
                break;
        }
    }

    public static function delete(string $name): bool
    {
        switch (self::$driver) {
            case 'memcached':
                $deleted = self::$cache->delete($name);
                return $deleted > 0;
                break;

            case 'redis':
                $deleted = self::$cache->del($name);
                return $deleted > 0;
                break;

            default:
                return false;
                break;
        }
    }

    /**
     * Get the value of driver
     *
     * @return string
     */
    public static function getDriver()
    {
        return self::$driver;
    }

    /**
     * @return Memcached|Redis
     */
    public static function getCore()
    {
        return self::$cache;
    }

    /**
     * @var Memcached|Redis $cache
     * @return void
     */
    public static function setCore($cache): void
    {
        self::$cache = $cache;
    }
}