<?php

namespace Litegram\Traits\Telegram;

use Chipslays\Collection\Collection;
use Litegram\Support\Util;

trait Aliases
{
    public function sendReply($chatId, $messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->api('sendMessage', $this->buildRequestParams([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_to_message_id' => $messageId,
        ], $keyboard, $extra));
    }

    public function say($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->defaultIdForReply,
            $text,
            $keyboard,
            $extra
        );
    }

    public function reply($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->defaultIdForReply,
            $text,
            $keyboard,
            array_merge($extra, ['reply_to_message_id' => $this->update('*.message_id')])
        );
    }

    public function notify($text = '', $showAlert = false, $extra = [])
    {
        return $this->api('answerCallbackQuery', $this->buildRequestParams([
            'callback_query_id' => $this->update('callback_query.id'),
            'text' => $text,
            'show_alert' => $showAlert,
        ], null, $extra));
    }

    public function action($action = 'typing', $extra = [])
    {
        return $this->api('sendChatAction', $this->buildRequestParams([
            'chat_id' => $this->defaultIdForReply,
            'action' => $action,
        ], null, $extra));

        return $this;
    }

    public function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->defaultIdForReply, $emoji, $keyboard, $extra);
    }

    public function isActive($chatId, $action = 'typing', $extra = [])
    {
        $response = $this->api('sendChatAction', $this->buildRequestParams([
            'chat_id' => $chatId,
            'action' => $action,
        ], null, $extra));

        return !is_null($response) ? $response->get('ok') : false;
    }

    public function getFileUrl($fileId)
    {
        $response = $this->getFile($fileId);
        return $response->get('ok') ? 'https://api.telegram.org/file/bot' . $this->config('bot.token') . '/' . $response->get('result.file_path') : null;
    }

    /**
     * @param string $fileId
     * @param string $savePath
     * @return string Base name of saved file
     */
    public function download($fileId, $savePath): string
    {
        $fileUrl = $this->getFileUrl($fileId);

        $extension = '';
        if (strpos(basename($fileUrl), '.') !== false) {
            $filename = explode('.', basename($fileUrl));
            $extension = end($filename);
        }

        $savePath = str_ireplace(['{ext}', '{extension}', '{file_ext}'], $extension, $savePath);
        $savePath = str_ireplace(['{base}', '{basename}', '{base_name}', '{name}'], basename($fileUrl), $savePath);
        $savePath = str_ireplace(['{time}'], time(), $savePath);
        $savePath = str_ireplace(['{md5}'], md5(time() . mt_rand()), $savePath);
        $savePath = str_ireplace(['{rand}', '{random}', '{rand_name}', '{random_name}'], md5(time() . mt_rand()) . ".$extension", $savePath);

        file_put_contents($savePath, file_get_contents($fileUrl));

        return basename($savePath);
    }

    /**
     * Меняющееся сообщение с задержкой.
     * 
     * @param array $elements Массив с сообщениями
     * @param integer|float $delay Задержка
     * @return boolean
     */
    public function loading(array $elements = [], $delay = 1)
    {
        $messageId = false;
        while ($element = array_shift($elements)) {
            if (!$messageId) {
                $result = $this->say($element)->get('result');
                $messageId = $result['message_id'];
            } else {
                $this->editMessageText($messageId, $this->update('*.chat.id'), $element);
            }
            Util::wait($delay);
        }
        return true;
    }

    /**
     * Edit source message received from callback.
     * 
     * @param string $text
     * @param string $keyboard
     * @param array $extra
     * @return void
     */
    public function editCallbackMessage(string $text, $keyboard = null, $extra = [])
    {
        return $this->editMessageText(
            $this->update('callback_query.message.message_id'),
            $this->update('callback_query.from.id'),
            $text,
            $keyboard,
            $extra
        );
    }

    public function editCallbackCaption(string $text, $keyboard = null, $extra = [])
    {
        return $this->editMessageCaption(
            $this->update('callback_query.message.message_id'),
            $this->update('callback_query.from.id'),
            $text,
            $keyboard,
            $extra
        );
    }

    /**
     * @param mixed $text
     * @param string|int|null $userId
     * @return Collection
     */
    public function print($data, $userId = null)
    {
        return $this->api('sendMessage', [
            'chat_id' => $userId ?? $this->defaultIdForReply,
            'text' => print_r($data, true),
            'parse_mode' => 'html',
        ]);
    }

    /**
     * @param array|string|int $data
     * @param string|int|null $userId
     * @return Collection
     */
    public function json($data, $userId = null)
    {
        return $this->api('sendMessage', [
            'chat_id' => $userId ?? $this->defaultIdForReply,
            'text' => '<code>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</code>',
            'parse_mode' => 'html',
        ]);
    }
}
