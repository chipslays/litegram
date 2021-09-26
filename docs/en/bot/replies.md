# Bot: Replies Methods

These methods send a message to the chat where the message came from.

First of all, this is `*.chat.id`, if it is not there, then this `*.from.id`.

The `chat_id` is taken from `*.chat.id`, if not, then from `*.from.id`.

```php
$bot->replyWithChatAction($action = 'typing', $extra = []);
```

```php
$bot->replyWithReply($messageId, $text = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithText($text, $keyboard = null, $extra = []);
```

```php
$bot->replyWithForwardMessage($fromChatId, $messageId, $extra = []);
```

```php
$bot->replyWithCopyMessage($fromChatId, $messageId, $extra = []);
```

```php
$bot->replyWithPhoto($photo, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithAudio($audio, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithDocument($document, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithAnimation($animation, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithVideo($video, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithVideoNote($videoNote, $keyboard = null, $extra = []);
```

```php
$bot->replyWithSticker($sticker, $keyboard = null, $extra = []);
```

```php
$bot->replyWithVoice($voice, $caption = '', $keyboard = null, $extra = []);
```

```php
$bot->replyWithMediaGroup(array $media, $extra = []);
```

```php
$bot->replyWithLocation($latitude, $longitude, $keyboard = null, $extra = []);
```

```php
$bot->replyWithDice($emoji = '🎲', $keyboard = null, $extra = []);
```