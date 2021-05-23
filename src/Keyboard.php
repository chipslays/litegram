<?php

namespace Litegram;

class Keyboard
{
    /**
     * @var array
     */
    protected static $keyboards = [];

    /**
     * Универсальный конструктор клавиатуры.
     * Автоматически определяет обычная клавиатура или инлайн.
     * Может залупонькаться на ключ text, например для кнопки запроса гео или контакта.
     * В этом случае лучше юзать markup метод который именно для обычной клавы.
     * TODO: сделать тру универсал и учесть эту херню выше.
     *
     * @param array|string $keyboard Массив с клавиатурой или ключ ранее добалвенной клавиатуры
     * @param boolean $oneTime True - показать один раз, False - не прятать клавиатуру после нажатия
     * @param boolean $resize True - vаленькая клавиатура, False - большая клавиатура
     * @param boolean $selective True - персольная клавиатура для юзера, False - для всех юзеров
     * @return string
     */
    public static function show($keyboard, bool $oneTime = false, bool $resize = true, bool $selective = false)
    {
        if ($keyboard === false) {
            return self::hide();
        }

        if (!is_array($keyboard)) {
            $keyboard = self::$keyboards[$keyboard];
        }

        // для дедекта инлайн клавиатуры
        $inlineKeys = [
            'text', 'callback_data', 'url', 'login_url',
            'switch_inline_query', 'switch_inline_query_current_chat',
            'callback_game', 'pay'
        ];

        if (isset($keyboard[0][0]) && is_array($keyboard[0][0]) && in_array(key($keyboard[0][0]), $inlineKeys)) {
            return self::inline($keyboard);
        }

        $markup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime,
            'selective' => $selective,
        ];

        return json_encode($markup);
    }

    /**
     * Обычная клавиатура.
     *
     * @param array|string $keyboard
     * @param boolean $oneTime
     * @param boolean $resize
     * @param boolean $selective
     * @return void
     */
    public static function markup($keyboard, bool $oneTime = false, bool $resize = true, bool $selective = false)
    {
        if (!is_array($keyboard)) {
            $keyboard = self::$keyboards[$keyboard];
        }

        $markup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime,
            'selective' => $selective,
        ];

        return json_encode($markup);
    }

    /**
     * Спрятать клавиатуру.
     *
     * @param boolean $selective True - персольная клавиатура для юзера, False - для всех юзеров
     *
     * @return string
     */
    public static function hide($selective = false)
    {
        $markup = [
            'hide_keyboard' => true,
            'selective' => $selective,
        ];

        return json_encode($markup);
    }

    /**
     * Инлайн клавиатура.
     *
     * @param string|array $keyboard Массив с клавиатурой или ключ ранее добалвенной клавиатуры
     * @return string
     */
    public static function inline($keyboard)
    {
        if (!is_array($keyboard)) {
            $keyboard = self::$keyboards[$keyboard];
        }

        if (Bot::getInstance()->config('telegram.safe_callback')) {
            foreach ($keyboard as &$item) {
                $item = array_map(function ($value) {
                    if (isset($value['callback_data'])) {
                        $value['callback_data'] = base64_encode(gzdeflate($value['callback_data'], 9));
                    }
                    return $value;
                }, $item);
            }
        }

        return json_encode(['inline_keyboard' => $keyboard]);
    }

    /**
     * Отправляет клавиатуру с запросом контакта.
     *
     * @param string $text
     * @param boolean $resize
     * @param boolean $oneTime
     * @param boolean $selective
     * @return string
     */
    public static function contact(string $text = 'Contact', $resize = true, $oneTime = false, $selective = false)
    {
        $keyboard = [
            [
                'text' => $text,
                'request_contact' => true,
            ]
        ];

        $markup = [
            'keyboard' => [$keyboard],
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime,
            'selective' => $selective,
        ];

        return json_encode($markup);
    }

    /**
     * Отправляет клавиатуру с запросом местоположения.
     *
     * @param string $text
     * @param boolean $resize
     * @param boolean $oneTime
     * @param boolean $selective
     * @return string
     */
    public static function location(string $text = 'Location', $resize = true, $oneTime = false, $selective = false)
    {
        $keyboard = [
            [
                'text' => $text,
                'request_location' => true,
            ]
        ];

        $markup = [
            'keyboard' => [$keyboard],
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $oneTime,
            'selective' => $selective,
        ];

        return json_encode($markup);
    }

    /**
     * Установить/перезаписать массив с клавиатурами (string 'название_ключа' => array [клавиатура])
     *
     * @param array $keyboards Массив массивов
     * @return void
     */
    public static function set(array $keyboards = [])
    {
        self::$keyboards = $keyboards;
    }

    /**
     * Слить (merge) текущие клавиатуры с новыми (string 'название_ключа' => array [клавиатура])
     *
     * @param array $keyboards Массив массивов
     * @return void
     */
    public static function add(array $keyboards = [])
    {
        self::$keyboards = array_merge(self::$keyboards, $keyboards);
    }

    /**
     * Удалить все сохраненные клавиатуры.
     *
     * @return void
     */
    public static function clear()
    {
        self::$keyboards = [];
    }
}
