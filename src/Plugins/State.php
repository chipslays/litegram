<?php

namespace Litegram\Plugins;

class State extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'state';

    /**
     * @var array
     */
    public static $depends = [
        'session',
    ];

    public static $name = null;

    public static $data = null;

    /**
     * @return void
     */
    public static function boot(): void
    {
        $state = self::get();
        self::$name = $state['name'] ?? null;
        self::$data = $state['data'] ?? null;
    }

    /**
     * @return array
     */
    public static function get(): array
    {
        return Session::get('litegram:state', ['name' => null, 'data' => null]);
    }

    /**
     * @param string $name
     * @param mixed $data
     * @return void
     */
    public static function set($name = null, $data = null): void
    {
        Session::set('litegram:state', [
            'name' => $name,
            'data' => $data,
        ]);
    }

    /**
     * @return void
     */
    public static function save(): void
    {
        self::set(self::$name, self::$data);
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        Session::delete('litegram:state');
    }

    /**
     * @param string $name
     * @return void
     */
    public static function setName(string $name): void
    {
        Session::set('litegram:state', [
            'name' => $name,
            'data' => self::$data,
        ]);
    }

    /**
     * @param mixed $data
     * @return void
     */
    public static function setData($data): void
    {
        Session::set('litegram:state', [
            'name' => self::$name,
            'data' => $data,
        ]);
    }
}