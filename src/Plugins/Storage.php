<?php

namespace Litegram\Plugins;

use Chipslays\Arr\Arr;
use Litegram\Exceptions\LitegramPluginException;

use function Opis\Closure\serialize;
use function Opis\Closure\unserialize;

/**
 * Plugin for store any data.
 */
class Storage extends AbstractPlugin
{
    /**
     * Имя метода для обращения, например, $bot->example()
     * @var string
     */
    public static $alias = 'storage';

    /**
     * @var string
     */
    private static $driver;

    /**
     * @var string
     */
    private static $dir;

    /**
     * @var array
     */
    private static $data = [];

    /**
     * Executed once when adding a module in the `with` method.
     *
     * @return void
     */
    public static function boot(): void
    {
        self::$driver = self::$config->get('plugins.storage.driver');

        switch (self::$driver) {
            case 'file':
                if (!self::$dir = self::$config->get('plugins.storage.drivers.file.dir')) {
                    throw new LitegramPluginException("Please, provide a storage directory [plugins.storage.drivers.file.dir], because selected driver is `file`.", 1);
                }
                self::$dir = rtrim(self::$dir, '\/');
                break;

            case 'database':
                if (!self::$bot->isModuleExists('database')) {
                    throw new LitegramPluginException("Please, add `database` plugin before add `storage` module.");
                }
                break;

            default:
                break;
        }
    }

    public static function set(string $name, $value = null): void
    {
        $origName = $name;
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                file_put_contents(self::$dir . "/{$name}", serialize($value));
                break;

            case 'database':
                self::has($origName)
                    ? Database::table('store')->where('name', $name)->update(['name' => $name, 'value' => base64_encode(serialize($value))])
                    : Database::table('store')->insert(['name' => $name, 'value' => base64_encode(serialize($value))]);
                break;

            default:
                self::$data[$name] = $value;
                break;
        }
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
        $name = md5($name);
        $array = self::get($name, []);
        $array[$key] = $value;
        self::set($name, $array);
    }

    /**
     * Get value from store data.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        $origName = $name;
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                return self::has($origName) ? unserialize(file_get_contents(self::$dir . "/{$name}")) : $default;
                break;

            case 'database':
                return self::has($origName) ? unserialize(base64_decode(Database::table('store')->select('value')->where('name', $name)->first()->value)) : $default;
                break;

            default:
                return self::has($origName) ? Arr::get(self::$data, $name, $default) : $default;
                break;
        }
    }

    /**
     * Get and delete value from store.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function pull(string $name, $default = null)
    {
        $origName = $name;
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                $value = self::has($origName) ? unserialize(file_get_contents(self::$dir . "/{$name}")) : $default;
                self::delete($origName);
                return $value;
                break;

            case 'database':
                $value = self::has($origName) ? unserialize(base64_decode(Database::table('store')->select('value')->where('name', $name)->first()->value)) : $default;
                self::delete($origName);
                return $value;
                break;

            default:
                $value = self::has($origName) ? Arr::get(self::$data, $name, $default) : $default;
                self::delete($origName);
                return $value;
                break;
        }
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function has(string $name): bool
    {
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                return file_exists(self::$dir . "/{$name}");
                break;

            case 'database':
                return Database::table('store')->where('name', $name)->exists();
                break;

            default:
                return Arr::has(self::$data, $name);
                break;
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public static function delete(string $name)
    {
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                $file = self::$dir . "/{$name}";
                if (file_exists($file)) {
                    unlink($file);
                }
                break;

            case 'database':
                Database::table('store')->where('name', $name)->delete();
                break;

            default:
                unset(self::$data[$name]);
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
}
