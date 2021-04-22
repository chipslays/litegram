<?php

namespace Litegram\Traits\Telegram;

trait Replies
{
    public function replyChatAction($action = 'typing', $extra = [])
    {
        return $this->sendChatAction($this->update, $action, $extra);
    }

    public function replyToReply($messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->sendReply($this->defaultIdForReply, $messageId, $text, $keyboard, $extra);
    }

    public function replyMessage($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage($this->defaultIdForReply, $text, $keyboard, $extra);
    }

    public function replyForwardMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->forwardMessage($this->defaultIdForReply, $fromChatId, $messageId, $extra);
    }

    public function replyCopyMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->copyMessage($this->defaultIdForReply, $fromChatId, $messageId, $extra);
    }

    public function replyPhoto($photo, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendPhoto($this->defaultIdForReply, $photo, $caption, $keyboard, $extra);
    }

    public function replyAudio($audio, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAudio($this->defaultIdForReply, $audio, $caption, $keyboard, $extra);
    }

    public function replyDocument($document, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendDocument($this->defaultIdForReply, $document, $caption, $keyboard, $extra);
    }

    public function replyAnimation($animation, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAnimation($this->defaultIdForReply, $animation, $caption, $keyboard, $extra);
    }

    public function replyVideo($video, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVideo($this->defaultIdForReply, $video, $caption, $keyboard, $extra);
    }

    public function replyVideoNote($videoNote, $keyboard = null, $extra = [])
    {
        return $this->sendVideoNote($this->defaultIdForReply, $videoNote, $keyboard, $extra);
    }

    public function replySticker($sticker, $keyboard = null, $extra = [])
    {
        return $this->sendSticker($this->defaultIdForReply, $sticker, $keyboard, $extra);
    }

    public function replyVoice($voice, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVoice($this->defaultIdForReply, $voice, $caption, $keyboard, $extra);
    }

    public function replyMediaGroup($media, $extra = [])
    {
        return $this->sendMediaGroup($this->defaultIdForReply, $media, $extra);
    }

    public function replyLocation($latitude, $longitude, $keyboard = null, $extra = [])
    {
        return $this->sendLocation($this->defaultIdForReply, $latitude, $longitude, $keyboard, $extra);
    }

    public function replyDice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->defaultIdForReply, $emoji, $keyboard, $extra);
    }
}