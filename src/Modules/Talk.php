<?php

namespace Litegram\Modules;

use Litegram\Support\Util;

class Talk extends Module
{
    /**
     * Имя метода для обращения, например, $bot->example()
     * @var string
     */
    public static $alias = 'talk';

    /**
     * @var array
     */
    private static $brain = [];

    /**
     * @var array
     */
    private static $text = [];

    /**
     * Выполняется один раз при добавлении модуля в методе addModule.
     *
     * @return void
     */
    public static function boot($text = null): void
    {
        self::$text = self::prepareText($text ?? self::$update->get('message.text'));
    }

    /**
     * Выполняется один раз непосредственно перед run методом.
     *
     * @return void
     */
    public static function beforeRun(): void
    {
        $results = [];

        foreach (self::$brain as $key => $item) {
            foreach ($item['variants'] as $variant) {
                $variant = self::prepareText($variant);
                $diff = array_diff($variant, self::$text);
                if (isset($results[$key])) {
                    $results[$key]['score'] += count($variant) - count($diff);
                    $results[$key]['matches'][] = count($variant) - count($diff);
                } else {
                    $results[$key]['score'] = count($variant) - count($diff);
                    $results[$key]['matches'][] = count($variant) - count($diff);
                }
            }

            $results[$key]['matches'] = array_filter($results[$key]['matches']);
        }

        arsort($results);

        $index = array_key_first($results);

        if ($results[$index]['score'] == 0) {
            return;
        }

        self::$bot->call(self::$brain[$index]['fn']);
        self::$bot->skip(true);
    }

    /**
     * @param array $variants
     * @param callable|string $fn
     * @return void
     */
    public static function train(array $variants, $fn)
    {
        self::$brain[] = compact('variants', 'fn');
    }

    /**
     * @param string $text
     * @return array
     */
    private static function prepareText(string $text)
    {
        $text = preg_replace("/[^a-zA-ZА-Яа-яЁё\s]/u", '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = mb_strtolower($text);
        $text = array_filter(array_map('trim', explode(' ', $text)), 'mb_strlen');

        foreach ($text as &$t) {
            $t = Util::stem($t, 'ru');
        }

        return $text;
    }
}
