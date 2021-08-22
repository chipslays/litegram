<?php

namespace Litegram\Plugins;

use Pastly\Client;
use Pastly\Expiration;
use Pastly\Types\Paste;
use stdClass;

class Logger extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'logger';

    /**
     * @var Client
     */
    private static $pastly;

    /**
     * @var string
     */
    private static $token;
    /**
     * @return void
     */
    public static function boot(): void
    {
        self::$pastly = new Client;
        self::$token = self::$config->get('plugins.logger.pastly.token');
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        if (!self::$config->get('plugins.logger.autolog')) {
            return;
        }

        self::log(self::$payload->toArray(), 'payload', 'auto');
    }

    /**
     * Put data to log file.
     *
     * @param string|array|stdClass $text
     * @param string $type
     * @param string $postfix
     * @return void
     */
    public static function log($text, $type = 'auto', $postfix = 'bot')
    {
        $currentYear = date('Y');
        $currentMonth = date('F');

        $path = self::$config->get('plugins.logger.path');
        $path = "{$path}/{$currentYear}/{$currentMonth}";

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $data = is_array($text) ? json_encode($text, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : trim($text);
        $date = date("d.m.Y, H:i:s");
        $log = "[{$date}] [{$type}]\n{$data}";

        $filename = date("Y-m-d") . "_{$postfix}.log";

        file_put_contents($path . "/{$filename}", $log . PHP_EOL, FILE_APPEND);
    }

    /**
     * Create cloud based log (Pastly).
     *
     * @param string|array|stdClass $text
     * @param array $extra
     * @return Paste
     */
    public static function upload($text, $extra = [])
    {
        $text = is_array($text) || is_object($text)
            ? json_encode(
                $text,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE
            )
            : $text;

        $response = self::$pastly->create(self::$token, $text, array_merge([
            'title' => self::$config->get('plugins.logger.pastly.title', 'Litegram Log'),
            'syntax' => self::$config->get('plugins.logger.pastly.syntax'),
            'slug' => null,
            'type' => self::$config->get('plugins.logger.pastly.type', 'private'),
            'password' => self::$config->get('plugins.logger.pastly.password'),
            'expiration' => Expiration::NEVER,
        ], $extra));

        return $response;
    }
}
