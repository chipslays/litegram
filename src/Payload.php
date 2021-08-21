<?php

namespace Litegram;

use Litegram\Exceptions\LitegramException;
use Litegram\Support\Collection;

class Payload
{
    /**
     * @var Bot
     */
    private static $bot = null;

    /**
     * @var Collection
     */
    private static $payload;

    public static function make(array $payload)
    {
        self::$bot = Bot::getInstance();
        self::$payload = new Collection($payload);
    }

    /**
     * @return boolean
     */
    public static function isMessage(): bool
    {
        return self::$payload->has('message');
    }

    public function __call($method, $params)
    {
        if (method_exists(self::$payload, $method)) {
            return call_user_func_array([self::$payload, $method], $params);
        }

        throw new LitegramException("Method `{$method}` not exists in Update module.");
    }

    public static function __callStatic($method, $params)
    {
        if (method_exists(self::$payload, $method)) {
            return call_user_func_array([self::$payload, $method], $params);
        }

        throw new LitegramException("Method `{$method}` not exists in Payload class.");
    }

    /**
     * @return Collection
     */
    public static function getMessage()
    {
        return new Collection(self::$payload->get('message'));
    }

    public static function isEditedMessage(): bool
    {
        return self::$payload->has('edited_message');
    }

    /**
     * @return Collection
     */
    public static function getEditedMessage()
    {
        return new Collection(self::$payload->get('edited_message'));
    }

    public static function isChannelPost(): bool
    {
        return self::$payload->has('channel_post');
    }

    /**
     * @return Collection
     */
    public static function getChannelPost()
    {
        return new Collection(self::$payload->get('channel_post'));
    }

    public static function isEditedChannelPost(): bool
    {
        return self::$payload->has('edited_channel_post');
    }

    /**
     * @return Collection
     */
    public static function getEditedChannelPost()
    {
        return new Collection(self::$payload->get('edited_channel_post'));
    }

    /**
     * @return boolean
     */
    public static function isInlineQuery(): bool
    {
        return self::$payload->has('inline_query');
    }

    /**
     * @return boolean
     */
    public static function isInline(): bool
    {
        return self::isInlineQuery();
    }

    /**
     * @return Collection
     */
    public static function getInlineQuery()
    {
        return new Collection(self::$payload->get('inline_query'));
    }

    /**
     * @return boolean
     */
    public static function isChosenInlineResult(): bool
    {
        return self::$payload->has('chosen_inline_result');
    }

    /**
     * @return Collection
     */
    public static function getChosenInlineResult()
    {
        return new Collection(self::$payload->get('chosen_inline_result'));
    }

    /**
     * @return boolean
     */
    public static function isCallbackQuery(): bool
    {
        return self::$payload->has('callback_query');
    }

    /**
     * @return boolean
     */
    public static function isCallback(): bool
    {
        return self::isCallbackQuery();
    }

    /**
     * @return Collection
     */
    public static function getCallbackQuery()
    {
        return new Collection(self::$payload->get('callback_query'));
    }

    public static function isShippingQuery(): bool
    {
        return self::$payload->has('shipping_query');
    }

    /**
     * @return Collection
     */
    public static function getShippingQuery()
    {
        return new Collection(self::$payload->get('shipping_query'));
    }

    /**
     * @return boolean
     */
    public static function isPreCheckoutQuery(): bool
    {
        return self::$payload->has('pre_checkout_query');
    }

    /**
     * @return Collection
     */
    public static function getPreCheckoutQuery()
    {
        return new Collection(self::$payload->get('pre_checkout_query'));
    }

    /**
     * @return boolean
     */
    public static function isPoll(): bool
    {
        return self::$payload->has('poll');
    }

    /**
     * @return Collection
     */
    public static function getPoll()
    {
        return new Collection(self::$payload->get('poll'));
    }

    /**
     * @return boolean
     */
    public static function isPollAnswer(): bool
    {
        return self::$payload->has('poll_answer');
    }

    /**
     * @return Collection
     */
    public static function getPollAnswer()
    {
        return new Collection(self::$payload->get('poll_answer'));
    }

    /**
     * @return boolean
     */
    public static function isCommand(): bool
    {
        if (!self::isMessage() && !self::isEditedMessage()) {
            return false;
        }

        if (!$text = self::$payload->get('*.text', false)) {
            return false;
        }

        return in_array(mb_substr($text, 0, 1, 'utf-8'), self::$bot->getCommandTags());
    }

    /**
     * @return int|string
     */
    public static function getCommand()
    {
        return self::$payload->get('*.text');
    }

    /**
     * @return boolean
     */
    public static function isBot(): bool
    {
        return self::$payload->has('*.from.is_bot');
    }

    /**
     * @return boolean
     */
    public static function isSticker(): bool
    {
        return self::$payload->has('*.sticker');
    }

    /**
     * @return Collection
     */
    public static function getSticker()
    {
        return new Collection(self::$payload->get('*.sticker'));
    }

    /**
     * @return boolean
     */
    public static function isVoice(): bool
    {
        return self::$payload->has('*.voice');
    }

    /**
     * @return Collection
     */
    public static function getVoice()
    {
        return new Collection(self::$payload->get('*.voice'));
    }

    /**
     * @return boolean
     */
    public static function isAnimation(): bool
    {
        return self::$payload->has('*.animation');
    }

    /**
     * @return Collection
     */
    public static function getAnimation()
    {
        return new Collection(self::$payload->get('*.animation'));
    }

    /**
     * @return boolean
     */
    public static function isDocument(): bool
    {
        return self::$payload->has('*.document');
    }

    /**
     * @return Collection
     */
    public static function getDocument()
    {
        return new Collection(self::$payload->get('*.document'));
    }

    /**
     * @return boolean
     */
    public static function isAudio(): bool
    {
        return self::$payload->has('*.audio');
    }

    /**
     * @return Collection
     */
    public static function getAudio()
    {
        return new Collection(self::$payload->get('*.audio'));
    }

    /**
     * @return boolean
     */
    public static function isPhoto(): bool
    {
        return self::$payload->has('*.photo');
    }

    /**
     * @return Collection
     */
    public static function getPhoto()
    {
        return new Collection(self::$payload->get('*.photo'));
    }

    /**
     * @return boolean
     */
    public static function isVideo(): bool
    {
        return self::$payload->has('*.video');
    }

    /**
     * @return Collection
     */
    public static function getVideo()
    {
        return new Collection(self::$payload->get('*.video'));
    }

    /**
     * @return boolean
     */
    public static function isVideoNote(): bool
    {
        return self::$payload->has('*.video_note');
    }

    /**
     * @return Collection
     */
    public static function getVideoNote()
    {
        return new Collection(self::$payload->get('*.video_note'));
    }

    /**
     * @return boolean
     */
    public static function isContact(): bool
    {
        return self::$payload->has('*.contact');
    }

    /**
     * @return Collection
     */
    public static function getContact()
    {
        return new Collection(self::$payload->get('*.contact'));
    }

    /**
     * @return boolean
     */
    public static function isLocation(): bool
    {
        return self::$payload->has('*.location');
    }

    /**
     * @return Collection
     */
    public static function getLocation()
    {
        return new Collection(self::$payload->get('*.location'));
    }

    /**
     * @return boolean
     */
    public static function isVenue(): bool
    {
        return self::$payload->has('*.venue');
    }

    /**
     * @return Collection
     */
    public static function getVenue()
    {
        return new Collection(self::$payload->get('*.venue'));
    }

    /**
     * @return boolean
     */
    public static function isDice(): bool
    {
        return self::$payload->has('*.dice');
    }

    /**
     * @return Collection
     */
    public static function getDice()
    {
        return new Collection(self::$payload->get('*.dice'));
    }

    /**
     * @return boolean
     */
    public static function isNewChatMembers(): bool
    {
        return self::$payload->has('*.new_chat_members');
    }

    /**
     * @return Collection
     */
    public static function getNewChatMembers()
    {
        return new Collection(self::$payload->get('*.new_chat_members'));
    }

    /**
     * @return boolean
     */
    public static function isLeftChatMember(): bool
    {
        return self::$payload->has('*.left_chat_member');
    }

    /**
     * @return Collection
     */
    public static function getLeftChatMember()
    {
        return new Collection(self::$payload->get('*.left_chat_member'));
    }

    /**
     * @return boolean
     */
    public static function isNewChatTitle(): bool
    {
        return self::$payload->has('*.new_chat_title');
    }

    /**
     * @return int|string
     */
    public static function getNewChatTitle()
    {
        return self::$payload->get('*.new_chat_title');
    }

    /**
     * @return boolean
     */
    public static function isNewChatPhoto(): bool
    {
        return self::$payload->has('*.new_chat_photo');
    }

    /**
     * @return Collection
     */
    public static function getNewChatPhoto()
    {
        return new Collection(self::$payload->get('*.new_chat_photo'));
    }

    /**
     * @return boolean
     */
    public static function isDeleteChatPhoto(): bool
    {
        return self::$payload->has('*.delete_chat_photo');
    }

    /**
     * @return boolean
     */
    public static function isChannelChatCreated(): bool
    {
        return self::$payload->has('*.channel_chat_created');
    }

    /**
     * @return boolean
     */
    public static function isMigrateToChatId(): bool
    {
        return self::$payload->has('*.migrate_to_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateToChatId()
    {
        return self::$payload->get('*.migrate_to_chat_id');
    }

    /**
     * @return boolean
     */
    public static function isMigrateFromChatId(): bool
    {
        return self::$payload->has('*.migrate_from_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateFromChatId()
    {
        return self::$payload->get('*.migrate_from_chat_id');
    }

    /**
     * @return boolean
     */
    public static function isPinnedMessage(): bool
    {
        return self::$payload->has('*.pinned_message');
    }

    /**
     * @return Collection
     */
    public static function getPinnedMessage()
    {
        return new Collection(self::$payload->get('*.pinned_message'));
    }

    /**
     * @return boolean
     */
    public static function isInvoice(): bool
    {
        return self::$payload->has('*.invoice');
    }

    /**
     * @return Collection
     */
    public static function getInvoice()
    {
        return new Collection(self::$payload->get('*.invoice'));
    }

    /**
     * @return boolean
     */
    public static function isSucessfulPayment(): bool
    {
        return self::$payload->has('*.successful_payment');
    }

    /**
     * @return Collection
     */
    public static function getSucessfulPayment()
    {
        return new Collection(self::$payload->get('*.successful_payment'));
    }

    /**
     * @return boolean
     */
    public static function isConnectedWebsite(): bool
    {
        return self::$payload->has('*.connected_website');
    }

    /**
     * @return Collection|int|string
     */
    public static function getConnectedWebsite()
    {
        return self::$payload->get('*.connected_website');
    }

    /**
     * @return boolean
     */
    public static function isPassportData(): bool
    {
        return self::$payload->has('*.passport_data');
    }

    /**
     * @return Collection
     */
    public static function getPassportData()
    {
        return new Collection(self::$payload->get('*.passport_data'));
    }

    /**
     * @return boolean
     */
    public static function isReplyMarkup(): bool
    {
        return self::$payload->has('*.reply_markup');
    }

    /**
     * @return Collection
     */
    public static function getReplyMarkup()
    {
        return new Collection(self::$payload->get('*.reply_markup'));
    }

    /**
     * @return boolean
     */
    public static function isReply(): bool
    {
        return self::$payload->has('*.reply_to_message');
    }

    /**
     * @return Collection
     */
    public static function getReply()
    {
        return new Collection(self::$payload->get('*.reply_to_message'));
    }

    /**
     * @return Collection
     */
    public static function getFrom()
    {
        return new Collection(self::$payload->get('*.from'));
    }

    /**
     * @return Collection
     */
    public static function getChat()
    {
        return new Collection(self::$payload->get('*.chat'));
    }

    /**
     * @return boolean
     */
    public static function isCaption(): bool
    {
        return self::$payload->has('*.caption');
    }

    /**
     * @return int|string
     */
    public static function getCaption()
    {
        return self::$payload->get('*.reply_to_message');
    }

    /**
     * @return int|string
     */
    public static function getText()
    {
        return self::$payload->get('*.text');
    }

    /**
     * @return int|string
     */
    public static function getTextOrCaption()
    {
        return self::$payload->get('*.text', self::$payload->get('*.caption'));
    }

    /**
     * @return int|string
     */
    public static function getData()
    {
        return self::$payload->get('callback_query.data');
    }

    /**
     * @return int|string
     */
    public static function getQuery()
    {
        return self::$payload->get('inline_query.query');
    }

    /**
     * @return int|string
     */
    public static function getId()
    {
        return self::$payload->get('update_id');
    }

    /**
     * @return int|string
     */
    public static function getMessageId()
    {
        return self::$payload->get('*.message_id');
    }

    /**
     * @return int|string
     */
    public static function getCallbackId()
    {
        return self::$payload->get('callback_query.id');
    }

    /**
     * @return int|string
     */
    public static function getPollId()
    {
        return self::$payload->get('poll.id');
    }

    /**
     * @return int|string
     */
    public static function getPollAnswerId()
    {
        return self::$payload->get('poll_answer.poll_id');
    }

    /**
     * @return int|string
     */
    public static function getInlineId()
    {
        return self::$payload->get('inline_query.id');
    }

    /**
     * @return boolean
     */
    public static function isForward(): bool
    {
        return self::$payload->has('*.forward_date') || self::$payload->has('*.forward_from');
    }

    /**
     * @return boolean
     */
    public static function isSuperGroup(): bool
    {
        return self::$payload->get('*.chat.type') == 'supergroup';
    }

    /**
     * @return boolean
     */
    public static function isGroup(): bool
    {
        return self::$payload->get('*.chat.type') == 'group';
    }

    /**
     * @return boolean
     */
    public static function isChannel(): bool
    {
        return self::$payload->get('*.chat.type') == 'channel';
    }

    /**
     * @return boolean
     */
    public static function isPrivate(): bool
    {
        return self::$payload->get('*.chat.type') == 'private';
    }
}