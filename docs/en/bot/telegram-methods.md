# Bot: Telegram Methods

All bot methods from [this page](https://core.telegram.org/bots/api#available-methods) are available as class methods (case sensitive).

```php
$bot->setWebhook($url = null, $extra = []);
```

```php
$bot->deleteWebhook($dropPendingUpdates = false);
```

```php
$bot->getWebhookInfo();
```

```php
$bot->logOut();
```

```php
$bot->close();
```

```php
$bot->getUpdates($offset = 0, $limit = 100, $extra = []);
```

> **List:** `typing` for tex    t messages, `upload_photo` for photos, `record_video` or `upload_video` for videos, `record_voice` or `upload_voice` for voice notes, `upload_document` for general files, `find_location` for location data, `record_video_note` or `upload_video_note` for video notes.

```php
$bot->sendChatAction($chatId, $action = 'typing');
```

```php
$bot->sendMessage($chatId, $text, $keyboard = null, $extra = []);
```

```php
$bot->forwardMessage($chatId, $fromChatId, $messageId, $extra = []);
```

```php
$bot->copyMessage($chatId, $fromChatId, $messageId, $extra = []);
```

```php
$bot->getMe();
```

```php
$bot->sendPhoto($chatId, $photo, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendAudio($chatId, $audio, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendDocument($chatId, $document, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendAnimation($chatId, $animation, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendVideo($chatId, $video, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendVideoNote($chatId, $videoNote, $keyboard = null, $extra = []);
```

```php
$bot->sendSticker($chatId, $sticker, $keyboard = null, $extra = []);
```

```php
$bot->sendVoice($chatId, $voice, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->sendMediaGroup($chatId, array $media, $extra = []);
```

```php
$bot->sendLocation($chatId, $latitude, $longitude, $keyboard = null, $extra = []);
```

> Short codes: `dice` for 🎲, `darts` for 🎯, `basketball` for 🏀, `football` for ⚽️, `777`, `slot`, `slots`, `casino` for 🎰.
>
```php
$bot->sendDice($chatId, $emoji = '🎲', $keyboard = null, $extra = []);
```

```php
$bot->getUserProfilePhotos($userId, $offset = 0, $limit = 100);
```

```php
$bot->getFile($fileId);
```

```php
$bot->banChatMember($chatId, $userId, $untilDate);
```

```php
$bot->unbanChatMember($chatId, $userId);
```

```php
$bot->restrictChatMember($chatId, $userId, $permissions, $untilDate = false);
```

```php
$bot->promoteChatMember($chatId, $userId, $extra = []);
```

```php
$bot->setChatAdministratorCustomTitle($chatId, $userId, string $title = '');
```

```php
$bot->setChatPermissions($chatId, $permissions);
```

```php
$bot->exportChatInviteLink($chatId);
```

```php
$bot->createChatInviteLink($chatId, ?int $expireDate = null, ?int $memberLimit = null);
```

```php
$bot->editChatInviteLink($chatId, string $inviteLink, ?int $expireDate = null, ?int $memberLimit = null);
```

```php
$bot->revokeChatInviteLink($chatId, string $inviteLink);
```

```php
$bot->setChatPhoto($chatId, $photo);
```

```php
$bot->deleteChatPhoto($chatId);
```

```php
$bot->setChatTitle($chatId, string $title = '');
```

```php
$bot->setChatDescription($chatId, string $description = '');
```

```php
$bot->pinChatMessage($chatId, $messageId, $disableNotification = false);
```

```php
$bot->unpinChatMessage($chatId, $messageId);
```

```php
$bot->unpinAllChatMessages($chatId);
```

```php
$bot->leaveChat($chatId);
```

```php
$bot->getChat($chatId);
```

```php
$bot->getChatAdministrators($chatId);
```

```php
$bot->getChatMemberCount($chatId);
```

```php
$bot->getChatMember($chatId, $userId);
```

```php
$bot->setChatStickerSet($chatId, $stickerSetName);
```

```php
$bot->deleteChatStickerSet($chatId);
```

```php
$bot->editMessageText($messageId, $chatId, $text = '', $keyboard = null, $extra = []);
```

```php
$bot->editMessageCaption($messageId, $chatId, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->editMessageMedia($messageId, $chatId, $media, $keyboard = null, $extra = []);
```

```php
$bot->editMessageReplyMarkup($messageId, $chatId, $keyboard = null, $extra = []);
```

```php
$bot->deleteMessage($messageId, $chatId);
```

```php
$bot->getStickerSet($name);
```

```php
$bot->deleteStickerFromSet($sticker);
```

```php
$bot->uploadStickerFile($userId, $pngSticker);
```

```php
$bot->createNewStickerSet($userId, $name, $title, $extra = []);
```

```php
$bot->addStickerToSet($userId, $name, $extra = []);
```

```php
$bot->sendGame($chatId, $gameShortName, $keyboard = null, $extra = []);
```

```php
$bot->answerCallbackQuery($extra = []);
```

```php
$bot->answerInlineQuery(array $results = [], $extra = []);
```

```php
$bot->setMyCommands(array $commands);
```

```php
$bot->getMyCommands();
```