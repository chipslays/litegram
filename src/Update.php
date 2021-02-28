<?php

namespace Litegram;

use Litegram\Modules\Module;
use Chipslays\Collection\Collection;

/**
 * @method static mixed get(string $key, $default = null, string $separator = '.')
 * @method static Collection set(string $key, $value = null, string $separator = '.')
 * @method static boolean has(string $key, string $separator = '.')
 * @method static self each(callable $callback)
 * @method static self map(callable $callback)
 * @method static self mapWithKeys(callable $callback)
 * @method static self filter(callable $callback = null)
 * @method static self where($key, $operator = null, $value = null)
 * @method static array all()
 * @method static array toArray()
 * @method static string toJson($flags = JSON_PRETTY_PRINT)
 * @method static \stdClass toObject()
 */
class Update extends Module
{
    /**
     * @var string
     */
    private static $alias = 'update';

    /**
     * @return string
     */
    public static function getAlias(): string
    {
        return self::$alias;
    }

    /**
     * @return void
     */
    public static function boot(): void
    {
    }

    /**
     * @return boolean
     */
    public static function isMessage(): bool
    {
        return self::$update->has('message');
    }

    public function __call($method, $params)
    {
        if (method_exists(self::$update, $method)) {
            return call_user_func_array([self::$update, $method], $params);
        }
        throw new \Exception("Method `{$method}` not exists in Update module.");
    }

    public static function __callStatic($method, $params)
    {
        if (method_exists(self::$update, $method)) {
            return call_user_func_array([self::$update, $method], $params);
        }
        throw new \Exception("Method `{$method}` not exists in Update module.");
    }

    /**
     * @return Collection
     */
    public static function getMessage()
    {
        return new Collection(self::$update->get('message'));
    }

    public static function isEditedMessage(): bool
    {
        return self::$update->has('edited_message');
    }

    /**
     * @return Collection
     */
    public static function getEditedMessage()
    {
        return new Collection(self::$update->get('edited_message'));
    }

    public static function isChannelPost(): bool
    {
        return self::$update->has('channel_post');
    }

    /**
     * @return Collection
     */
    public static function getChannelPost()
    {
        return new Collection(self::$update->get('channel_post'));
    }

    public static function isEditedChannelPost(): bool
    {
        return self::$update->has('edited_channel_post');
    }

    /**
     * @return Collection
     */
    public static function getEditedChannelPost()
    {
        return new Collection(self::$update->get('edited_channel_post'));
    }

    /**
     * @return boolean
     */
    public static function isInlineQuery(): bool
    {
        return self::$update->has('inline_query');
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
        return new Collection(self::$update->get('inline_query'));
    }

    /**
     * @return boolean
     */
    public static function isChosenInlineResult(): bool
    {
        return self::$update->has('chosen_inline_result');
    }

    /**
     * @return Collection
     */
    public static function getChosenInlineResult()
    {
        return new Collection(self::$update->get('chosen_inline_result'));
    }

    /**
     * @return boolean
     */
    public static function isCallbackQuery(): bool
    {
        return self::$update->has('callback_query');
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
        return new Collection(self::$update->get('callback_query'));
    }

    public static function isShippingQuery(): bool
    {
        return self::$update->has('shipping_query');
    }

    /**
     * @return Collection
     */
    public static function getShippingQuery()
    {
        return new Collection(self::$update->get('shipping_query'));
    }

    /**
     * @return boolean
     */
    public static function isPreCheckoutQuery(): bool
    {
        return self::$update->has('pre_checkout_query');
    }

    /**
     * @return Collection
     */
    public static function getPreCheckoutQuery()
    {
        return new Collection(self::$update->get('pre_checkout_query'));
    }

    /**
     * @return boolean
     */
    public static function isPoll(): bool
    {
        return self::$update->has('poll');
    }

    /**
     * @return Collection
     */
    public static function getPoll()
    {
        return new Collection(self::$update->get('poll'));
    }

    /**
     * @return boolean
     */
    public static function isPollAnswer(): bool
    {
        return self::$update->has('poll_answer');
    }

    /**
     * @return Collection
     */
    public static function getPollAnswer()
    {
        return new Collection(self::$update->get('poll_answer'));
    }

    /**
     * @return boolean
     */
    public static function isCommand(): bool
    {
        if (!self::isMessage() && !self::isEditedMessage()) {
            return false;
        }

        if (!$text = self::$update->get('*.text', false)) {
            return false;
        }

        return in_array(mb_substr($text, 0, 1, 'utf-8'), self::$bot->getCommandTags());
    }

    /**
     * @return int|string
     */
    public static function getCommand()
    {
        return self::$update->get('*.text');
    }

    /**
     * @return boolean
     */
    public static function isBot(): bool
    {
        return self::$update->has('*.from.is_bot');
    }

    /**
     * @return boolean
     */
    public static function isSticker(): bool
    {
        return self::$update->has('*.sticker');
    }

    /**
     * @return Collection
     */
    public static function getSticker()
    {
        return new Collection(self::$update->get('*.sticker'));
    }
    
    /**
     * @return boolean
     */
    public static function isVoice(): bool
    {
        return self::$update->has('*.voice');
    }

    /**
     * @return Collection
     */
    public static function getVoice()
    {
        return new Collection(self::$update->get('*.voice'));
    }

    /**
     * @return boolean
     */
    public static function isAnimation(): bool
    {
        return self::$update->has('*.animation');
    }

    /**
     * @return Collection
     */
    public static function getAnimation()
    {
        return new Collection(self::$update->get('*.animation'));
    }

    /**
     * @return boolean
     */
    public static function isDocument(): bool
    {
        return self::$update->has('*.document');
    }

    /**
     * @return Collection
     */
    public static function getDocument()
    {
        return new Collection(self::$update->get('*.document'));
    }

    /**
     * @return boolean
     */
    public static function isAudio(): bool
    {
        return self::$update->has('*.audio');
    }

    /**
     * @return Collection
     */
    public static function getAudio()
    {
        return new Collection(self::$update->get('*.audio'));
    }

    /**
     * @return boolean
     */
    public static function isPhoto(): bool
    {
        return self::$update->has('*.photo');
    }

    /**
     * @return Collection
     */
    public static function getPhoto()
    {
        return new Collection(self::$update->get('*.photo'));
    }

    /**
     * @return boolean
     */
    public static function isVideo(): bool
    {
        return self::$update->has('*.video');
    }

    /**
     * @return Collection
     */
    public static function getVideo()
    {
        return new Collection(self::$update->get('*.video'));
    }

    /**
     * @return boolean
     */
    public static function isVideoNote(): bool
    {
        return self::$update->has('*.video_note');
    }

    /**
     * @return Collection
     */
    public static function getVideoNote()
    {
        return new Collection(self::$update->get('*.video_note'));
    }

    /**
     * @return boolean
     */
    public static function isContact(): bool
    {
        return self::$update->has('*.contact');
    }

    /**
     * @return Collection
     */
    public static function getContact()
    {
        return new Collection(self::$update->get('*.contact'));
    }

    /**
     * @return boolean
     */
    public static function isLocation(): bool
    {
        return self::$update->has('*.location');
    }

    /**
     * @return Collection
     */
    public static function getLocation()
    {
        return new Collection(self::$update->get('*.location'));
    }

    /**
     * @return boolean
     */
    public static function isVenue(): bool
    {
        return self::$update->has('*.venue');
    }

    /**
     * @return Collection
     */
    public static function getVenue()
    {
        return new Collection(self::$update->get('*.venue'));
    }

    /**
     * @return boolean
     */
    public static function isDice(): bool
    {
        return self::$update->has('*.dice');
    }

    /**
     * @return Collection
     */
    public static function getDice()
    {
        return new Collection(self::$update->get('*.dice'));
    }

    /**
     * @return boolean
     */
    public static function isNewChatMembers(): bool
    {
        return self::$update->has('*.new_chat_members');
    }

    /**
     * @return Collection
     */
    public static function getNewChatMembers()
    {
        return new Collection(self::$update->get('*.new_chat_members'));
    }

    /**
     * @return boolean
     */
    public static function isLeftChatMember(): bool
    {
        return self::$update->has('*.left_chat_member');
    }

    /**
     * @return Collection
     */
    public static function getLeftChatMember()
    {
        return new Collection(self::$update->get('*.left_chat_member'));
    }

    /**
     * @return boolean
     */
    public static function isNewChatTitle(): bool
    {
        return self::$update->has('*.new_chat_title');
    }

    /**
     * @return int|string
     */
    public static function getNewChatTitle()
    {
        return self::$update->get('*.new_chat_title');
    }

    /**
     * @return boolean
     */
    public static function isNewChatPhoto(): bool
    {
        return self::$update->has('*.new_chat_photo');
    }

    /**
     * @return Collection
     */
    public static function getNewChatPhoto()
    {
        return new Collection(self::$update->get('*.new_chat_photo'));
    }

    /**
     * @return boolean
     */
    public static function isDeleteChatPhoto(): bool
    {
        return self::$update->has('*.delete_chat_photo');
    }

    /**
     * @return boolean
     */
    public static function isChannelChatCreated(): bool
    {
        return self::$update->has('*.channel_chat_created');
    }

    /**
     * @return boolean
     */
    public static function isMigrateToChatId(): bool
    {
        return self::$update->has('*.migrate_to_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateToChatId()
    {
        return self::$update->get('*.migrate_to_chat_id');
    }

    /**
     * @return boolean
     */
    public static function isMigrateFromChatId(): bool
    {
        return self::$update->has('*.migrate_from_chat_id');
    }

    /**
     * @return int|string
     */
    public static function getMigrateFromChatId()
    {
        return self::$update->get('*.migrate_from_chat_id');
    }

    /**
     * @return boolean
     */
    public static function isPinnedMessage(): bool
    {
        return self::$update->has('*.pinned_message');
    }

    /**
     * @return Collection
     */
    public static function getPinnedMessage()
    {
        return new Collection(self::$update->get('*.pinned_message'));
    }

    /**
     * @return boolean
     */
    public static function isInvoice(): bool
    {
        return self::$update->has('*.invoice');
    }

    /**
     * @return Collection
     */
    public static function getInvoice()
    {
        return new Collection(self::$update->get('*.invoice'));
    }

    /**
     * @return boolean
     */
    public static function isSucessfulPayment(): bool
    {
        return self::$update->has('*.successful_payment');
    }

    /**
     * @return Collection
     */
    public static function getSucessfulPayment()
    {
        return new Collection(self::$update->get('*.successful_payment'));
    }

    /**
     * @return boolean
     */
    public static function isConnectedWebsite(): bool
    {
        return self::$update->has('*.connected_website');
    }

    /**
     * @return Collection|int|string
     */
    public static function getConnectedWebsite()
    {
        return self::$update->get('*.connected_website');
    }

    /**
     * @return boolean
     */
    public static function isPassportData(): bool
    {
        return self::$update->has('*.passport_data');
    }

    /**
     * @return Collection
     */
    public static function getPassportData()
    {
        return new Collection(self::$update->get('*.passport_data'));
    }

    /**
     * @return boolean
     */
    public static function isReplyMarkup(): bool
    {
        return self::$update->has('*.reply_markup');
    }

    /**
     * @return Collection
     */
    public static function getReplyMarkup()
    {
        return new Collection(self::$update->get('*.reply_markup'));
    }

    /**
     * @return boolean
     */
    public static function isReply(): bool
    {
        return self::$update->has('*.reply_to_message');
    }

    /**
     * @return Collection
     */
    public static function getReply()
    {
        return new Collection(self::$update->get('*.reply_to_message'));
    }

    /**
     * @return Collection
     */
    public static function getFrom()
    {
        return new Collection(self::$update->get('*.from'));
    }

    /**
     * @return Collection
     */
    public static function getChat()
    {
        return new Collection(self::$update->get('*.chat'));
    }

    /**
     * @return boolean
     */
    public static function isCaption(): bool
    {
        return self::$update->has('*.caption');
    }

    /**
     * @return int|string
     */
    public static function getCaption()
    {
        return self::$update->get('*.reply_to_message');
    }

    /**
     * @return int|string
     */
    public static function getText()
    {
        return self::$update->get('*.text');
    }

    /**
     * @return int|string
     */
    public static function getTextOrCaption()
    {
        return self::$update->get('*.text', self::$update->get('*.caption'));
    }

    /**
     * @return int|string
     */
    public static function getData()
    {
        return self::$update->get('callback_query.data');
    }

    /**
     * @return int|string
     */
    public static function getQuery()
    {
        return self::$update->get('inline_query.query');
    }

    /**
     * @return int|string
     */
    public static function getId()
    {
        return self::$update->get('update_id');
    }

    /**
     * @return int|string
     */
    public static function getMessageId()
    {
        return self::$update->get('*.message_id');
    }

    /**
     * @return int|string
     */
    public static function getCallbackId()
    {
        return self::$update->get('callback_query.id');
    }

    /**
     * @return int|string
     */
    public static function getPollId()
    {
        return self::$update->get('poll.id');
    }

    /**
     * @return int|string
     */
    public static function getPollAnswerId()
    {
        return self::$update->get('poll_answer.poll_id');
    }

    /**
     * @return int|string
     */
    public static function getInlineId()
    {
        return self::$update->get('inline_query.id');
    }

    /**
     * @return boolean
     */
    public static function isForward(): bool
    {
        return self::$update->has('*.forward_date') || self::$update->has('*.forward_from');
    }

    /**
     * @return boolean
     */
    public static function isSuperGroup(): bool
    {
        return self::$update->get('*.chat.type') == 'supergroup';
    }

    /**
     * @return boolean
     */
    public static function isGroup(): bool
    {
        return self::$update->get('*.chat.type') == 'group';
    }

    /**
     * @return boolean
     */
    public static function isChannel(): bool
    {
        return self::$update->get('*.chat.type') == 'channel';
    }

    /**
     * @return boolean
     */
    public static function isPrivate(): bool
    {
        return self::$update->get('*.chat.type') == 'private';
    }
}
