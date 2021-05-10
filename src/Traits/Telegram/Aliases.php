<?php

namespace Litegram\Traits\Telegram;

use Litegram\Bot;
use Litegram\Update;
use Litegram\Support\Util;
use Litegram\Support\Collection;

trait Aliases
{
    /**
     * Reply to message by chat or user ID.
     *
     * @param string|int $chatId
     * @param string|int $messageId
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function sendReply($chatId, $messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->method('sendMessage', $this->buildRequestParams([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_to_message_id' => $messageId,
        ], $keyboard, $extra));
    }

    /**
     * Just send message for icoming chat or user.
     *
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function say($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->defaultIdForReply,
            Util::shuffle($text),
            $keyboard,
            $extra
        );
    }

    /**
     * Reply to incoming message.
     *
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function reply($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage(
            $this->defaultIdForReply,
            Util::shuffle($text),
            $keyboard,
            array_merge($extra, ['reply_to_message_id' => $this->update('*.message_id')])
        );
    }

    /**
     * Send notification or alert.
     *
     * Works only for callback.
     *
     * @param string $text
     * @param boolean $showAlert
     * @param array $extra
     * @return Collection
     */
    public function notify($text = '', $showAlert = false, $extra = [])
    {
        return $this->method('answerCallbackQuery', $this->buildRequestParams([
            'callback_query_id' => $this->update('callback_query.id'),
            'text' => Util::shuffle($text),
            'show_alert' => $showAlert,
        ], null, $extra));
    }

    /**
     * Send caht action: typing and etc...
     *
     * @param string $action
     * @param array $extra
     * @return Bot
     */
    public function action($action = 'typing', $extra = [])
    {
        return $this->method('sendChatAction', $this->buildRequestParams([
            'chat_id' => $this->defaultIdForReply,
            'action' => $action,
        ], null, $extra));

        return $this;
    }

    /**
     * Send dice and other emojis.
     *
     * @param string $emoji
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function dice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->defaultIdForReply, $emoji, $keyboard, $extra);
    }

    /**
     * Check user blocked bot or not.
     *
     * @param int|string $chatId
     * @param string $action
     * @param array $extra
     * @return boolean
     */
    public function isActive($chatId, $action = 'typing', $extra = [])
    {
        $response = $this->method('sendChatAction', $this->buildRequestParams([
            'chat_id' => $chatId,
            'action' => $action,
        ], null, $extra));

        return !is_null($response) ? $response->get('ok') : false;
    }

    /**
     * Build `api.telegram.org/file/bot123/file_123` url
     *
     * @param string|null $fileId
     * @return Collection
     */
    public function getFileUrl(string $fileId)
    {
        $response = $this->getFile($fileId);
        return $response->get('ok')
            ? 'https://api.telegram.org/file/bot' . $this->config('bot.token') . '/' . $response->get('result.file_path')
            : null;
    }

    /**
     * Download file by `file_id`.
     * @param string $fileId
     * @param string $savePath
     * @return string Full path to saved file
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

        return $savePath;
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
     * Auto detect caption or text callback.
     *
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function editCallbackMessage(string $text, $keyboard = null, $extra = [])
    {
        if (Update::isCaption()) {
            return $this->editMessageCaption(
                $this->update('callback_query.message.message_id'),
                $this->update('callback_query.from.id'),
                $text,
                $keyboard,
                $extra
            );
        } else {
            return $this->editMessageText(
                $this->update('callback_query.message.message_id'),
                $this->update('callback_query.from.id'),
                $text,
                $keyboard,
                $extra
            );
        }

    }

    /**
     * Edit source message received from callback.
     *
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function editCallbackText(string $text, $keyboard = null, $extra = [])
    {
        return $this->editMessageText(
            $this->update('callback_query.message.message_id'),
            $this->update('callback_query.from.id'),
            $text,
            $keyboard,
            $extra
        );
    }

    /**
     * Edit source message with photo received from callback.
     *
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
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
     * Send `print_r` message.
     *
     * @param mixed $text
     * @param string|int|null $userId
     * @return Collection
     */
    public function print($data, $userId = null)
    {
        return $this->method('sendMessage', [
            'chat_id' => $userId ?? $this->defaultIdForReply,
            'text' => '<code>' . print_r($data, true) . '</code>',
            'parse_mode' => 'html',
        ]);
    }

    /**
     * Send `var_export` message.
     *
     * @param mixed $text
     * @param string|int|null $userId
     * @return Collection
     */
    public function dump($data, $userId = null)
    {
        return $this->method('sendMessage', [
            'chat_id' => $userId ?? $this->defaultIdForReply,
            'text' => '<code>' . var_export($data, true) . '</code>',
            'parse_mode' => 'html',
        ]);
    }

    /**
     * Send `json` message.
     *
     * @param array|string|int $data
     * @param string|int|null $userId
     * @return Collection
     */
    public function json($data, $userId = null)
    {
        return $this->method('sendMessage', [
            'chat_id' => $userId ?? $this->defaultIdForReply,
            'text' => '<code>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</code>',
            'parse_mode' => 'html',
        ]);
    }
}
