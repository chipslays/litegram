<?php

namespace Litegram\Traits;

use Litegram\Support\Collection;
use Litegram\Plugins\Localization;

trait Utility
{
    /**
     * Декодирует входящий параметр data у callback_query
     *
     * @return void
     */
    private function decodeCallback()
    {
        if (!$this->config('telegram.safe_callback')) {
            return;
        }

        if (!$data = $this->payload('callback_query.data')) {
            return;
        }

        $data = @gzinflate(base64_decode($data));

        if (!$data) {
            return;
        }

        $this->payload()->set('callback_query.data', $data);
    }

    /**
     * Before call a massive operations, use this method for non-block bot answers.
     *
     * @param integer $timeLimit Execution script time limit in seconds.
     * @return Bot
     */
    public function finishRequest($timelimit = 1800)
    {
        set_time_limit($timelimit);
        ignore_user_abort(true);

        $response = json_encode(['ok']);

        header('Connection: close');
        header('Content-Length: ' . strlen($response));
        header("Content-type:application/json");

        echo $response;

        flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * Like 'on' method, but returns `bool` if items in array `$hasystack` default is current update.
     *
     * @param string|array $needles
     * @param array $haystack
     * @return bool
     */
    public function in($needles, array $haystack = null)
    {
        $haystack = is_array($haystack) ? new Collection($haystack) : $this->payload();
        foreach ($needles as $item) {
            foreach ((array) $item as $key => $value) {
                /**
                 * Force execute event
                 * on(true, ..., ...)
                 */
                if ($value === true) {
                    return true;
                    break;
                }

                /**
                 * [['key' => 'value'], ...]
                 */
                if (is_array($value)) {
                    $key = key($value);
                    $value = $value[$key];
                }

                /**
                 * ['key'] or 'key'
                 */
                if (is_numeric($key) && $haystack->has($value)) {
                    return true;
                    break;
                }

                /**
                 * Get value by key, if not exists then skip iteration.
                 * ['key' => 'value']
                 */
                if (!$received = $haystack->get($key)) {
                    continue;
                }

                /**
                 * ['key' => 'value']
                 */
                if ($received == $value) {
                    return true;
                    break;
                }

                /**
                 * ['key' => 'my name is {name}']
                 *
                 * command(?: (.*?))?(?: (.*?))?$
                 *
                 * {text} - required text
                 * {:text?} - optional text
                 */
                $value = preg_replace('~.?{:(.*?)\?}~', '(?: (.*?))?', $value);
                $pattern = '~^' . preg_replace('/{(.*?)}/', '(.*?)', $value) . '$~';

                if (@preg_match_all($pattern, $received, $matches)) {
                    return true;
                    break;
                }

                /**
                 * ['key' => '/regex/i]
                 */
                if (@preg_match_all($value, $received, $matches)) {
                    return true;
                    break;
                }
            }
        }

        return false;
    }

    /**
     * Локализовать строку если содержит символы разметки локализации.
     *
     * @param string $text {{ locale_message_text }}
     * @return string
     */
    public function localify(string $text)
    {
        if (
            mb_substr($text, 0, 2) == '{{'
            && mb_substr($text, -2) == '}}'
            && substr_count($text, ' ') <= 2
        ) {
            preg_match('~{{(.+?)}}~u', $text, $matches);
            return Localization::get(trim($matches[1]));
        }

        return $text;
    }
}
