<?php

namespace Litegram\Support;

use Litegram\Bot;
use Litegram\Update;
use Wamania\Snowball\StemmerManager;

/**
 * Вспомогательные функции
 */
class Util
{
    /**
     * @var \Wamania\Snowball\StemmerManager
     */
    private static $stemmer;

    /**
     * Перемешивает текст в {{двойных фигурных скобках}}.
     *
     * Например: shuffle('Сегодня {{лето|осень|зима|весна}}')
     * Вернет: случайное слово из фигурных скобок, например, "осень".
     *
     * @param string $message
     * @return string
     */
    public static function shuffle(string $message): string
    {
        preg_match_all('/{{(.+?)}}/mi', $message, $sentences);

        if (sizeof($sentences[1]) == 0) {
            return $message;
        }

        foreach ($sentences[1] as $words) {
            $words_array = explode('|', $words);
            $words_array = array_map('trim', $words_array);
            $select = $words_array[array_rand($words_array)];
            $message = str_ireplace('{{' . $words . '}}', $select, $message);
        }

        return $message;
    }

    /**
     * Проверяет является ли строка Json.
     *
     * @param string $string
     * @return boolean
     */
    public static function isRegEx(string $string)
    {
        return @preg_match($string, '') !== false;
    }

    /**
     * Проверяет является ли строка Json.
     *
     * @param string $string
     * @return boolean
     */
    public static function isJson(string $string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * Плюрализация (Русский, Украинский?, Белорусский?).
     *
     * Например: plural(10, ['арбуз', 'арбуза', 'арбузов'])
     * Вернет: арбузов
     *
     * @param string|int $n
     * @param array $forms
     * @return string
     */
    public static function plural($n, array $forms)
    {
        return is_float($n) ? $forms[1] : ($n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]));
    }

    /**
     * English pluralization.
     *
     * @param string|int $value
     * @param string $phrase
     * @return string
     */
    public static function pluralEng($value, string $phrase)
    {
        $plural = '';
        if ($value > 1) {
            for ($i = 0; $i < strlen($phrase); $i++) {
                if ($i == strlen($phrase) - 1) {
                    $plural .= ($phrase[$i] == 'y') ? 'ies' : (($phrase[$i] == 's' || $phrase[$i] == 'x' || $phrase[$i] == 'z' || $phrase[$i] == 'ch' || $phrase[$i] == 'sh') ? $phrase[$i] . 'es' : $phrase[$i] . 's');
                } else {
                    $plural .= $phrase[$i];
                }
            }
            return $plural;
        }
        return $phrase;
    }

    /**
     * Проверить наличие RTL символов (Arabic, Persian, Hebrew)
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isRtl($string)
    {
        $rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
        return preg_match($rtl_chars_pattern, $string);
    }

    /**
     * Выбрать случайный элемент из массива
     *
     * @param array $arr
     * @param boolean $shuffle
     * @return mixed
     */
    public static function random(array $array, bool $shuffle = true)
    {
        if ($shuffle) {
            shuffle($array);
        }

        return $array[array_rand($array)];
    }

    /**
     * Возвращает время (полночь)
     * Например: 2020-02-02 00:00:00
     *
     * @param boolean $timestamp
     *
     * @return string
     */
    public static function midnight($timestamp = null)
    {
        $timestamp = $timestamp ? $timestamp : time();
        return strtotime(date('Y-m-d', $timestamp) . ' midnight');
    }

    /**
     * Подготавливает файл для загрузки
     *
     * @param string $path
     * @return \CURLFile|bool
     */
    public static function upload(string $path = null, string $mimeType = null, string $postName = null)
    {
        return $path ? new \CURLFile($path, $mimeType, $postName) : false;
    }

    /**
     * Генерирует случайную строку.
     *
     * @param integer $lenght
     * @param array $chars
     * @return string
     */
    public static function getRandomCode(int $lenght = 6, array $chars = null)
    {
        $chars = $chars ?: array_merge(range('a', 'z'), range('A', 'Z'), range(0, 1));

        shuffle($chars);

        $code = '';
        for ($i = 0; $i < $lenght; $i++) {
            $code .= self::random($chars, false);
        }

        return $code;
    }

    /**
     * AAAA -> AAAB -> AAAC -> etc.
     * Инкрементируется с маленькой буквы до больной
     *
     * @param string $string
     * @param boolean $position
     * @return void
     */
    public static function incrementAphanumeric($string, $position = false)
    {
        if (false === $position) {
            $position = strlen($string) - 1;
        }
        $increment_str = substr($string, $position, 1);
        switch ($increment_str) {
            case '9':
                $string = substr_replace($string, 'a', $position, 1);
                break;
            case 'z':
                if (0 === $position) {
                    $string = substr_replace($string, '0', $position, 1);
                    $string .= '0';
                } else {
                    $inc_position = $position - 1;
                    $string = self::incrementAphanumeric($string, $inc_position);
                    $string = substr_replace($string, '0', $position, 1);
                }

                break;

            default:
                $increment_str++;
                $string = substr_replace($string, $increment_str, $position, 1);
                break;
        }
        return $string;
    }

    /**
     * Parser for nested entities.
     *
     * @param string $text
     * @param array $entities
     * @return string
     */
    public static function entitiesToHtml(string $text, array $entities)
    {
        $textToParse = mb_convert_encoding($text, 'UTF-16BE', 'UTF-8');

        foreach ($entities as $entity) {
            $href = false;
            switch ($entity['type']) {
                case 'bold':
                    $tag = 'b';
                    break;
                case 'italic':
                    $tag = 'i';
                    break;
                case 'underline':
                    $tag = 'ins';
                    break;
                case 'strikethrough':
                    $tag = 'strike';
                    break;
                case 'code':
                    $tag = 'code';
                    break;
                case 'pre':
                    $tag = 'pre';
                    break;
                case 'text_link':
                    $tag = '<a href="' . $entity['url'] . '">';
                    $href = true;
                    break;
                case 'text_mention':
                    $tag = '<a href="tg://user?id=' . $entity['user']['id'] . '">';
                    $href = true;
                    break;
                default:
                    continue 2;
            }

            if ($href) {
                $oTag = "\0{$tag}\0";
                $cTag = "\0</a>\0";
            } else {
                $oTag = "\0<{$tag}>\0";
                $cTag = "\0</{$tag}>\0";
            }
            $oTag = mb_convert_encoding($oTag, 'UTF-16BE', 'UTF-8');
            $cTag = mb_convert_encoding($cTag, 'UTF-16BE', 'UTF-8');

            $textToParse = self::parseTagOpen($textToParse, $entity, $oTag);
            $textToParse = self::parseTagClose($textToParse, $entity, $cTag);
        }

        if (isset($entity)) {
            $textToParse = mb_convert_encoding($textToParse, 'UTF-8', 'UTF-16BE');
            $textToParse = self::htmlEscape($textToParse);
            return str_replace("\0", '', $textToParse);
        }

        return htmlspecialchars($text);
    }

    public static function mbStringToArray($string, $encoding = 'UTF-8')
    {
        $array = [];
        $strlen = mb_strlen($string, $encoding);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, $encoding);
            $string = mb_substr($string, 1, $strlen, $encoding);
            $strlen = mb_strlen($string, $encoding);
        }
        return $array;
    }

    public static function parseTagOpen($textToParse, $entity, $oTag)
    {
        $i = 0;
        $textParsed = '';
        $nullControl = false;
        $string = self::mbStringToArray($textToParse, 'UTF-16LE');
        foreach ($string as $s) {
            if ($s === "\0\0") {
                $nullControl = !$nullControl;
            } elseif (!$nullControl) {
                if ($i == $entity['offset']) {
                    $textParsed = $textParsed . $oTag;
                }
                $i++;
            }
            $textParsed = $textParsed . $s;
        }
        return $textParsed;
    }

    public static function parseTagClose($textToParse, $entity, $cTag)
    {
        $i = 0;
        $textParsed = '';
        $nullControl = false;
        $string = self::mbStringToArray($textToParse, 'UTF-16LE');
        foreach ($string as $s) {
            $textParsed = $textParsed . $s;
            if ($s === "\0\0") {
                $nullControl = !$nullControl;
            } elseif (!$nullControl) {
                $i++;
                if ($i == ($entity['offset'] + $entity['length'])) {
                    $textParsed = $textParsed . $cTag;
                }
            }
        }
        return $textParsed;
    }

    public static function htmlEscape($textToParse)
    {
        $i = 0;
        $textParsed = '';
        $nullControl = false;
        $string = self::mbStringToArray($textToParse, 'UTF-8');
        foreach ($string as $s) {
            if ($s === "\0") {
                $nullControl = !$nullControl;
            } elseif (!$nullControl) {
                $i++;
                $textParsed = $textParsed . str_replace(['&', '"', '<', '>'], ["&amp;", "&quot;", "&lt;", "&gt;"], $s);
            } else {
                $textParsed = $textParsed . $s;
            }
        }
        return $textParsed;
    }

    /**
     * Раскодирует inline-клавиатуру (safe_calback)
     *
     * @param array $keyboard
     * @return void
     */
    public static function decodeInlineKeyboard($keyboard)
    {
        foreach ($keyboard as &$item) {
            $item = array_map(function ($value) {
                if (isset($value['callback_data'])) {
                    $value['callback_data'] = gzinflate(base64_decode($value['callback_data']));
                }
                return $value;
            }, $item);
        }

        return $keyboard;
    }

    /**
     * WIP: Разбирает страницу t.me/username и возвращает массив с данными.
     *
     * @todo оптимизировать/доделать стикеры и темы
     *
     * @param string $url
     * @return array
     */
    public static function fetchTelegramLink($url)
    {
        $result = [];

        $html = file_get_contents($url);

        preg_match('/<meta property="og:image" content="(.*?)">/', $html, $image);
        preg_match('/<meta property="og:title" content="(.*?)">/', $html, $title);
        preg_match('/<div class="tgme_page_description" dir="auto">(.*?)<\/div>/', $html, $description);
        preg_match('/<div class="tgme_page_extra">(.*?)<\/div>/', $html, $membersInfo);
        preg_match('/<title>Telegram: Contact @(.*?)<\/title>/', $html, $username);
        preg_match('/<a class="tgme_action_button_new" href="(.*?)">/', $html, $action);

        $members = explode(',', $membersInfo[1] ?? null)[0] ?? null;
        $online = array_map('trim', explode(',', $membersInfo[1] ?? null))[1] ?? null;

        $result['image'] = $image[1] ?? null;
        $result['title'] = $title[1] ?? null;
        $result['description'] = [
            'clean' => strip_tags($description[1] ?? null),
            'raw' => $description[1] ?? null,
            'markup' => strip_tags(preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n", "", str_replace("\r", "", htmlspecialchars_decode($description[1] ?? null))))),
        ];
        $result['members'] = [
            'clean' => (int) $members ?? null,
            'raw' => $members,
        ];
        $result['online'] = [
            'clean' => (int) $online ?? null,
            'raw' => $online,
        ];
        $result['username'] = $username[1] ?? null;
        $result['action'] = $action[1] ?? null;
        $result['link'] = 'https://t.me/' . $username[1] ?? null;

        $result['type'] = null;
        if (strpos($html, 'Send Message') !== false) {
            $result['type'] = 'user';
        }
        if (strpos($html, 'Preview channel') !== false) {
            $result['type'] = 'channel';
        }
        if (strpos($html, 'Join Group') !== false) {
            $result['type'] = 'super_group';
        }
        if (strpos($html, 'View in Telegram') !== false && strpos($html, 'member') !== false && strpos($html, 'Preview channel') === false) {
            $result['type'] = 'group';
        }
        if (strpos($html, 'Send Message') !== false && (strtolower(substr($result['username'], -4)) == "_bot" || strtolower(substr($result['username'], -3)) == "bot")) {
            $result['type'] = 'bot';
        }

        return $result;
    }

    /**
     * Ждать определенное время (поддерживает миллисекунды).
     *
     * @param integer|float $seconds
     * @return boolean
     */
    public static function wait($seconds = 1)
    {
        usleep(round($seconds * 1000000));
        return;
    }

    /**
     * Высчитывает дистанцию между двух позиций.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Радиус Земли

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $distance = 2 * $earthRadius * asin(sqrt(sin(($lat2 - $lat1) / 2) ** 2 + cos($lat1) * cos($lat2) * sin(($lon2 - $lon1) / 2) ** 2));

        return round($distance, 2);
    }

    /**
     * Change keyboard button in inline keyboard.
     * Default inline from: callback_query.message.reply_markup.inline_keyboard
     *
     * @param array $old ['needleKey' => 'needleValue'] (strpos method)
     * @param array $new New array inline button
     * @param array|null $inline Force inline keyboard
     * @return array Changed inline keyboard
     */
    public static function changeInlineButton(array $old, array $new, ?array $inline = null)
    {
        if (!$inline) {
            if (Bot::getInstance()->config('telegram.safe_callback')) {
                $inline = self::decodeInlineKeyboard(Update::get('callback_query.message.reply_markup.inline_keyboard'));
            } else {
                $inline = Update::get('callback_query.message.reply_markup.inline_keyboard');
            }
        }

        $needleKey = key($old);
        $needleValue = array_shift($old);

        foreach ($inline as &$row) {
            foreach ($row as &$btn) {
                if (strpos($btn[$needleKey], $needleValue) !== false) {
                    $btn = $new;
                    break;
                }
            }
        }

        return $inline;
    }

    public static function trimArray($array, $func = 'strlen')
    {
        return array_filter($array, $func);
    }

    /**
     * Snowball stemmer (https://snowballstem.org)
     *
     * @see https://github.com/wamania/php-stemmer
     *
     * @param string $word
     * @param string $isoCode Use ISO_639 (2 or 3 letters) or language name in english
     * @return void
     */
    public static function stem(string $word, string $isoCode)
    {
        self::$stemmer = self::$stemmer ?? new StemmerManager;
        return self::$stemmer->stem($word, $isoCode);
    }
}
