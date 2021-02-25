<?php

namespace Litegram\Modules;

use Litegram\Modules\Database;
use Chipslays\Arr\Arr;

class Store extends Module
{
    /**
     * @var string
     */
    private static $alias = 'store';

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
        if (!self::$config->get('modules.store.enable')) {
            return;
        }

        self::$driver = self::$config->get('modules.store.driver');
        
        switch (self::$driver) {
            case 'file':
                self::$dir = rtrim(self::$config->get('modules.store.file.dir'), '\/');
                break;
            case 'database':
                break;
            
            default:
                break;
        }
    }

    public static function set(string $name, $value)
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

    public static function delete(string $name)
    {
        $name = md5($name);

        switch (self::$driver) {
            case 'file':
                unlink(self::$dir . "/{$name}");
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
