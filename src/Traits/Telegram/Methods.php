<?php

namespace Litegram\Traits\Telegram;

use Litegram\Support\Util;

trait Methods
{
    public function setWebhook($url = null, $extra = [])
    {
        if (!$url) {
            $url = $this->config('bot.handler');
        }

        return $this->method(__FUNCTION__, array_merge(['url' => $url, 'max_connections' => 100], $extra));
    }

    public function deleteWebhook($dropPendingUpdates = false)
    {
        return $this->method(__FUNCTION__, [
            'drop_pending_updates' => $dropPendingUpdates,
        ]);
    }

    public function getWebhookInfo()
    {
        return $this->method(__FUNCTION__);
    }

    public function logOut()
    {
        return $this->method(__FUNCTION__);
    }

    public function close()
    {
        return $this->method(__FUNCTION__);
    }

    public function getUpdates($offset = 0, $limit = 100, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'offset' => $offset,
            'limit' => $limit,
        ], null, $extra));
    }

    public function sendChatAction($chatId, $action = 'typing')
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    /**
     * Use this method to send text messages.
     *
     * On success, the sent Message is returned.
     *
     * @param int|string $chatId
     * @param string $text
     * @param string|null $keyboard
     * @param array $extra
     * @return Collection
     */
    public function sendMessage($chatId, $text, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'text' => $text,
        ], $keyboard, $extra));
    }

    public function forwardMessage($chatId, $fromChatId, $messageId, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ], null, $extra));
    }

    public function copyMessage($chatId, $fromChatId, $messageId, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
        ], null, $extra));
    }

    public function getMe()
    {
        return $this->method(__FUNCTION__);
    }

    public function sendPhoto($chatId, $photo, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'photo' => $photo,
        ], $keyboard, $extra), true);
    }

    public function sendAudio($chatId, $audio, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'audio' => $audio,
        ], $keyboard, $extra), true);
    }

    public function sendDocument($chatId, $document, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'document' => $document,
        ], $keyboard, $extra), true);
    }

    public function sendAnimation($chatId, $animation, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'animation' => $animation,
        ], $keyboard, $extra), true);
    }

    public function sendVideo($chatId, $video, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'video' => $video,
        ], $keyboard, $extra), true);
    }

    public function sendVideoNote($chatId, $videoNote, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'video_note' => $videoNote,
        ], $keyboard, $extra), true);
    }

    public function sendSticker($chatId, $sticker, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'sticker' => $sticker,
        ], $keyboard, $extra), true);
    }

    public function sendVoice($chatId, $voice, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'caption' => $caption,
            'voice' => $voice,
        ], $keyboard, $extra), true);
    }

    public function sendMediaGroup($chatId, array $media, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'media' => json_encode($media),
        ], null, $extra), true);
    }

    public function sendLocation($chatId, $latitude, $longitude, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], $keyboard, $extra));
    }

    public function sendDice($chatId, $emoji = '🎲', $keyboard = null, $extra = [])
    {
        $emoji = str_ireplace(['dice', 'кубик'], '🎲', $emoji);
        $emoji = str_ireplace(['darts', 'dart', 'дротик', 'дартс'], '🎯', $emoji);
        $emoji = str_ireplace(['basketball', 'баскетбол'], '🏀', $emoji);
        $emoji = str_ireplace(['football', 'футбол'], '⚽️', $emoji);
        $emoji = str_ireplace(['777', 'slot', 'slots', 'casino', 'слоты', 'слот', 'казино'], '🎰', $emoji);

        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'emoji' => $emoji,
        ], $keyboard, $extra));
    }

    public function getUserProfilePhotos($userId, $offset = 0, $limit = 100)
    {
        return $this->method(__FUNCTION__, [
            'user_id' => $userId,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    public function getFile($fileId)
    {
        return $this->method(__FUNCTION__, [
            'file_id' => $fileId,
        ]);
    }

    public function kickChatMember($chatId, $userId, $untilDate)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'until_date' => $untilDate,
        ]);
    }

    public function unbanChatMember($chatId, $userId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function restrictChatMember($chatId, $userId, $permissions, $untilDate = false)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'permissions' => $permissions,
            'until_date' => $untilDate,
        ]);
    }

    public function promoteChatMember($chatId, $userId, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ], null, $extra));
    }

    public function setChatAdministratorCustomTitle($chatId, $userId, string $title = '')
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'user_id' => $userId,
            'custom_title' => $title,
        ]);
    }

    public function setChatPermissions($chatId, $permissions)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'permissions' => $permissions,
        ]);
    }

    public function exportChatInviteLink($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    /**
     * Use this method to create an additional invite link for a chat.
     *
     * The bot must be an administrator in the chat for this to work
     * and must have the appropriate admin rights.
     *
     * The link can be revoked using the method revokeChatInviteLink.
     *
     * Returns the new invite link as ChatInviteLink object.
     *
     * @param int|string    $chatId Unique identifier for the target chat or username of
     *                      the target channel (in the format @channelusername)
     * @param integer|null  $expireDate Point in time (Unix timestamp) when the link will expire
     * @param integer|null  $memberLimit Maximum number of users that can be members
     *                      of the chat simultaneously after joining the chat via
     *                      this invite link; 1-99999
     * @return Collection
     */
    public function createChatInviteLink($chatId, ?int $expireDate = null, ?int $memberLimit = null)
    {
        $parameters = [
            'chat_id' => $chatId,
            'expire_date' => $expireDate,
            'member_limit' => $memberLimit,
        ];

        $parameters = Util::trimArray($parameters);

        return $this->method(__FUNCTION__, $parameters);
    }

    /**
     * Use this method to edit a non-primary invite link created by the bot.
     *
     * The bot must be an administrator in the chat for this to work and
     * must have the appropriate admin rights.
     *
     * Returns the edited invite link as a ChatInviteLink object.
     *
     * @param int|string    $chatId Unique identifier for the target chat or username
     *                      of the target channel (in the format @channelusername)
     * @param string        $inviteLink The invite link to edit.
     * @param integer|null  $expireDate Point in time (Unix timestamp) when the link will expire
     * @param integer|null  $memberLimit Maximum number of users that can be members
     *                      of the chat simultaneously after joining the chat via
     *                      this invite link; 1-99999
     * @return Collection
     */
    public function editChatInviteLink($chatId, string $inviteLink, ?int $expireDate = null, ?int $memberLimit = null)
    {
        $parameters = [
            'chat_id' => $chatId,
            'invite_link' => $inviteLink,
            'expire_date' => $expireDate,
            'member_limit' => $memberLimit,
        ];

        $parameters = Util::trimArray($parameters);

        return $this->method(__FUNCTION__, $parameters);
    }

    /**
     * Use this method to revoke an invite link created by the bot.
     *
     * If the primary link is revoked, a new link is automatically generated.
     *
     * The bot must be an administrator in the chat for this to work and
     * must have the appropriate admin rights.
     *
     * Returns the revoked invite link as ChatInviteLink object.
     *
     * @param int|string    $chatId Unique identifier for the target chat or username
     *                      of the target channel (in the format @channelusername)
     * @param string        $inviteLink The invite link to revoke.

     * @return Collection
     */
    public function revokeChatInviteLink($chatId, string $inviteLink)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'invite_link' => $inviteLink,
        ]);
    }

    /**
     * Use this method to set a new profile photo for the chat.
     *
     * Photos can't be changed for private chats.
     *
     * The bot must be an administrator in the chat for this
     * to work and must have the appropriate admin rights.
     *
     * Returns True on success.
     *
     * @param int|string    $chatId Unique identifier for the target chat or username
     *                      of the target channel (in the format @channelusername)
     * @param \CURLFile     $photo
     * @return Collection
     */
    public function setChatPhoto($chatId, $photo)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'photo' => $photo,
        ]);
    }

    public function deleteChatPhoto($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function setChatTitle($chatId, string $title = '')
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'title' => $title,
        ]);
    }

    public function setChatDescription($chatId, string $description = '')
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'description' => $description,
        ]);
    }

    public function pinChatMessage($chatId, $messageId, $disableNotification = false)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'disable_notification' => $disableNotification,
        ]);
    }

    public function unpinChatMessage($chatId, $messageId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    public function unpinAllChatMessages($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function leaveChat($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function getChat($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function getChatAdministrators($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function getChatMembersCount($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function getChatMember($chatId, $userId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    public function setChatStickerSet($chatId, $stickerSetName)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'sticker_set_name' => $stickerSetName,
        ]);
    }

    public function deleteChatStickerSet($chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
        ]);
    }

    public function editMessageText($messageId, $chatId, $text = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
        ], $keyboard, $extra));
    }

    public function editMessageCaption($messageId, $chatId, $caption = '', $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $caption,
        ], $keyboard, $extra));
    }

    public function editMessageMedia($messageId, $chatId, $media, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'media' => $media,
        ], $keyboard, $extra));
    }

    public function editMessageReplyMarkup($messageId, $chatId, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ], $keyboard, $extra));
    }

    public function deleteMessage($messageId, $chatId)
    {
        return $this->method(__FUNCTION__, [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    public function getStickerSet($name)
    {
        return $this->method(__FUNCTION__, [
            'name' => $name,
        ]);
    }

    public function deleteStickerFromSet($sticker)
    {
        return $this->method(__FUNCTION__, [
            'sticker' => $sticker,
        ]);
    }

    public function uploadStickerFile($userId, $pngSticker)
    {
        return $this->method(__FUNCTION__, [
            'user_id' => $userId,
            'png_sticker' => $pngSticker,
        ], true);
    }

    public function createNewStickerSet($userId, $name, $title, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'user_id' => $userId,
            'name' => $name,
            'title' => $title,
        ], null, $extra), isset($extra['tgs_sticker']));
    }

    public function addStickerToSet($userId, $name, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'user_id' => $userId,
            'name' => $name,
        ], null, $extra), isset($extra['tgs_sticker']));
    }

    public function sendGame($chatId, $gameShortName, $keyboard = null, $extra = [])
    {
        return $this->method(__FUNCTION__, $this->buildRequestParams([
            'chat_id' => $chatId,
            'game_short_name' => $gameShortName,
        ], $keyboard, $extra));
    }

    public function answerCallbackQuery($extra = [])
    {
        return $this->method(__FUNCTION__, $extra);
    }

    public function answerInlineQuery(array $results = [], $extra = [])
    {
        return $this->method(__FUNCTION__, array_merge([
            'inline_query_id' => $this->update('inline_query.id'),
            'results' => json_encode($results),
        ], $extra));
    }

    public function setMyCommands($commands)
    {
        return $this->method(__FUNCTION__, [
            'commands' => json_encode($commands),
        ]);
    }

    public function getMyCommands()
    {
        return $this->method(__FUNCTION__);
    }
}
