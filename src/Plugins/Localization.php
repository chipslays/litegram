<?php

namespace Litegram\Plugins;

use Litegram\Bot;
use Litegram\Exceptions\LitegramPluginException;
use Chipslays\Phrase\Engine\JsonEngine;
use Chipslays\Phrase\Engine\YamlEngine;
use Chipslays\Phrase\Phrase;

/**
 * Localization plugin based on Phrase library.
 *
 * @see https://github.com/chipslays/phrase
 * @see https://github.com/chipslays/phrase#usage
 */
class Localization extends Phrase
{
    /**
     * @var string
     */
    public static $alias = 'locale';

    /**
     * @var array
     */
    public static $depends = [];

    public function __construct()
    {
    }

    /**
     * @param string locale - Force set current locale on boot.
     * @return void
     *
     * @throws LitegramPluginException
     */
    public static function boot($locale = null): void
    {
        $bot = Bot::getInstance();

        // TODO: брать язык из сохраненного юзера

        $driver = $bot->config('plugins.localization.driver');
        $fallback = $bot->config('plugins.localization.fallback');
        $locales = $bot->config("plugins.localization.drivers.{$driver}.path");
        $locale = $locale ?? $bot->payload('*.from.language_code', $fallback);

        switch ($driver) {
            case 'yaml':
                self::setEngine(new YamlEngine($locales, $locale, $fallback));
                break;

            case 'json':
                self::setEngine(new JsonEngine($locales, $locale, $fallback));

            default:
                throw new LitegramPluginException("Please, provide correct Localization driver [plugins.localization.driver].", 1);
                break;
        }
    }
}
