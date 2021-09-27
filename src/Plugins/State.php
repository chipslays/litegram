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
        'storage',
    ];

    /**
     * @var string
     */
    private static $userId;

    public static $name = null;

    public static $data = null;

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (self::$bot->isPluginExists('user')) {
            self::$userId = User::get('id');
        } else {
            self::$userId = self::$payload->get('*.from.id');
        }

        $state = self::get();
        self::$name = $state['name'] ?? null;
        self::$data = $state['data'] ?? null;
    }

    public static function get()
    {
        return self::$userId ? self::getById(self::$userId) : false;
    }

    /**
     * @param int|string $userId
     */
    public static function getById($userId)
    {
        return Storage::get(self::getStateId($userId));
    }

    public static function set($name = null, $data = null): void
    {
        self::setById(self::$userId, $name, $data);
    }

    public static function save(): void
    {
        self::setById(self::$userId, self::$name, self::$data);
    }

    public static function setById($userId, $name = null, $data = null)
    {
        // we not setting $name & $data directly, but it may error collision with current request...
        return Storage::set(self::getStateId($userId), [
            'name' => $name,
            'data' => $data
        ]);
    }

    public static function clear(): void
    {
        self::clearById(self::$userId);
    }

    public static function clearById($userId)
    {
        return Storage::delete(self::getStateId($userId));
    }

    public static function setName($name)
    {
        return Storage::set(self::getStateId(self::$userId), [
            'name' => $name,
            'data' => self::$data,
        ]);
    }

    public static function setData($data)
    {
        return Storage::set(self::getStateId(self::$userId), [
            'name' => self::$name,
            'data' => $data,
        ]);
    }

    private static function getStateId($userId): string
    {
        return "{$userId}__USER__STATE__ID";
    }
}