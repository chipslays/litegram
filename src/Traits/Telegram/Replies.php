<?php

namespace Litegram\Traits\Telegram;

trait Replies
{
    /**
     * @TODO
     * @return Collection
     */
    public function replyWithChatAction($action = 'typing', $extra = [])
    {
        return $this->sendChatAction($this->update, $action, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithReply($messageId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->sendReply($this->defaultIdForReply, $messageId, $text, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithText($text, $keyboard = null, $extra = [])
    {
        return $this->sendMessage($this->defaultIdForReply, $text, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithForwardMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->forwardMessage($this->defaultIdForReply, $fromChatId, $messageId, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithCopyMessage($fromChatId, $messageId, $extra = [])
    {
        return $this->copyMessage($this->defaultIdForReply, $fromChatId, $messageId, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithPhoto($photo, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendPhoto($this->defaultIdForReply, $photo, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithAudio($audio, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAudio($this->defaultIdForReply, $audio, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithDocument($document, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendDocument($this->defaultIdForReply, $document, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithAnimation($animation, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendAnimation($this->defaultIdForReply, $animation, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithVideo($video, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVideo($this->defaultIdForReply, $video, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithVideoNote($videoNote, $keyboard = null, $extra = [])
    {
        return $this->sendVideoNote($this->defaultIdForReply, $videoNote, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithSticker($sticker, $keyboard = null, $extra = [])
    {
        return $this->sendSticker($this->defaultIdForReply, $sticker, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithVoice($voice, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->sendVoice($this->defaultIdForReply, $voice, $caption, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithMediaGroup(array $media, $extra = [])
    {
        return $this->sendMediaGroup($this->defaultIdForReply, $media, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithLocation($latitude, $longitude, $keyboard = null, $extra = [])
    {
        return $this->sendLocation($this->defaultIdForReply, $latitude, $longitude, $keyboard, $extra);
    }

    /**
     * @TODO
     * @return Collection
     */
    public function replyWithDice($emoji = '🎲', $keyboard = null, $extra = [])
    {
        return $this->sendDice($this->defaultIdForReply, $emoji, $keyboard, $extra);
    }
}