<?php

namespace Litegram\Modules;

use Litegram\Modules\Store;

class State extends Module
{
    /**
     * @var string
     */
    public static $alias = 'state';

    /**
     * @var array
     */
    public static $depends = [
        'store',
    ];

    /**
     * @var int
     */
    private static $currentUserId;

    public static $name = null;
    public static $data = null;

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (!self::$config->get('modules.state.enable')) {
            return;
        }

        if (self::$config->get('modules.user.enable') && !self::$bot->isModuleExists('user')) {
            throw new \Exception("Config `modules.user.enable=true`, but `user` module not loaded. Please load `user` module before `state`.");
        }

        if (self::$config->get('modules.user.enable')) {
            self::$currentUserId = User::get('user_id');
        } else {
            self::$currentUserId = self::$update->get('*.from.id');
        }

        $state = self::get();
        self::$name = $state['name'] ?? null;
        self::$data = $state['data'] ?? null;
    }

    public static function get()
    {
        return self::$currentUserId ? self::getById(self::$currentUserId) : false;
    }

    /**
     * @param int|string $userId
     */
    public static function getById($userId)
    {
        return Store::get(self::getStateId($userId));
    }

    public static function set($name = null, $data = null): void
    {
        self::setById(self::$currentUserId, $name, $data);
    }

    public static function save(): void
    {
        self::setById(self::$currentUserId, self::$name, self::$data);
    }

    public static function setById($userId, $name = null, $data = null)
    {
        return Store::set(self::getStateId($userId), [
            'name' => $name,
            'data' => $data
        ]);
    }

    public static function clear(): void
    {
        self::clearById(self::$currentUserId);
    }

    public static function clearById($userId)
    {
        return Store::delete(self::getStateId($userId));
    }

    public static function setName($name)
    {
        return Store::set(self::getStateId(self::$currentUserId), [
            'name' => $name,
            'data' => self::$data,
        ]);
    }

    public static function setData($data)
    {
        return Store::set(self::getStateId(self::$currentUserId), [
            'name' => self::$name,
            'data' => $data,
        ]);
    }

    private static function getStateId($userId): string
    {
        return "{$userId}__USER__STATE__ID";
    }
}
