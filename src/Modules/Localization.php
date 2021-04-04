<?php

namespace Litegram\Modules;

use Chipslays\Collection\Collection;

class Localization extends Module
{
    /**
     * @var string
     */
    private static $alias = 'lang';

    /**
     * @return string
     */
    public static function getAlias(): string
    {
        return self::$alias;
    }

    /**
     * @var string
     */
    public static $language;

    /**
     * @var string
     */
    public static $default;

    /**
     * @var string
     */
    private static $path;

    /**
     * @var \Chipslays\Collection\Collection;
     */
    private static $data;

    /**
     * @return void
     */
    public static function boot($language = null): void
    {
        if (!self::$config->get('modules.localization.enable')) {
            return;
        }

        self::$default = self::$config->get('modules.localization.default');
        self::$path = rtrim(self::$config->get('modules.localization.dir'), '\/');
        self::$data = new Collection([]);

        if (self::$config->get('modules.user.enable')) {
            self::$language = User::get('lang', self::$default);
        } else {
            self::$language = $language ?: self::$update->get('*.from.language_code', self::$default);
        }
        
        self::load(self::$language);
    }

    /**
     * @param string $language
     * @param string|null $default
     * @return void
     */
    public static function load(string $language, ?string $default = null, $path = null): void
    {
        $path = $path ?? self::$path;

        self::$language = $language;

        $driver = self::$config->get('modules.localization.driver', 'php');
        switch ($driver) {
            case 'php':
                $file = $path . '/' . $language . '.php';
                break;

            case 'serialize':
                $file = $path . '/' . $language . '.txt';
                break;
        }

        if (file_exists($file)) {
            switch ($driver) {
                case 'php':
                    self::$data->set($language, array_merge(self::$data->get($language, []), require $file));
                    break;
    
                case 'serialize':
                    $langData = (array) unserialize(file_get_contents($file));
                    self::$data->set($language, array_merge(self::$data->get($language, []), $langData));
                    break;
            }

            return;
        }

        $default = $default ?: self::$default;

        self::$language = $default;

        switch ($driver) {
            case 'php':
                $file = $path . '/' . $default . '.php';
                break;

            case 'serialize':
                $file = $path . '/' . $default . '.txt';
                break;
        }

        if (file_exists($file)) {
            switch ($driver) {
                case 'php':
                    self::$data->set($default, array_merge(self::$data->get($default, []), require $file));
                    break;
    
                case 'serialize':
                    $langData = unserialize(file_get_contents($file));
                    self::$data->set($default, array_merge(self::$data->get($default, []), $langData));
                    break;
            }
        }
    }

    /**
     * @param string $key
     * @param array|null $replacement
     * @param string $language
     * @return string
     */
    public static function get(string $key, $replacement = null, $language = null)
    {
        $language = $language ?? self::$language;
        
        if (!self::$data->has($language)) {
            return;
        }

        $data = self::$data->get($language);

        if (!array_key_exists($key, $data)) {
            return;
        }

        $text = $data[$key];

        return $replacement ? strtr($text, $replacement) : $text;
    }
}
