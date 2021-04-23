<?php

namespace Litegram\Modules;

class Logger extends Module
{
    /**
     * @var string
     */
    public static $alias = 'logger';

    /**
     * @var string
     */
    private static $dir;

    /**
     * @return void
     */
    public static function boot(): void
    {
        if (!self::$config->get('modules.logger.enable')) {
            return;
        }

        $dir = self::$config->get('modules.logger.dir');

        if (!file_exists($dir)) {
            throw new \Exception("Log directory `{$dir}` not exists.");
        }

        self::$dir = rtrim($dir, '/');
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        if (!self::$config->get('modules.logger.auto')) {
            return;
        }

        self::put(self::$update->toArray(), 'auto', 'update');
    }

    /**
     * @param string|array $data
     * @param string $type Log type
     * @param string $postfix File postfix
     * @return void
     */
    public static function put($data = false, $type = 'auto', $postfix = 'bot'): void
    {
        if (!$data) {
            return;
        }

        $date = date("d.m.Y, H:i:s");
        $data = is_array($data) ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : trim($data);
        // $log = "[{$date}] [".bot()->getExecutedTime(6)."] [{$type}]\n{$data}";
        $log = "[{$date}] [{$type}]\n{$data}";

        $filename = date("d-m-Y") . "_{$postfix}.log";

        file_put_contents(self::$dir . "/{$filename}", $log . PHP_EOL, FILE_APPEND);
    }
}