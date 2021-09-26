# Bot: Short Aliases Methods

These magical methods are designed to make life as easy as possible.

Reply to message by chat or user ID.

```php
$bot->sendReply($chatId, $messageId, $text = '', $keyboard = null, $extra = []);
```

Just send message for icoming chat or user.

```php
$bot->say($text, $keyboard = null, $extra = []);
```

Reply to incoming message.

```php
$bot->reply($text, $keyboard = null, $extra = []);
```

Send notification or alert (works only for callback update).

```php
$bot->notify($text = '', $showAlert = false, $extra = []);
```

Send caht action: `typing` and etc...

```php
$bot->action($action = 'typing', $extra = []);
```

Send dice and other emojis.

```php
$bot->dice($emoji = '🎲', $keyboard = null, $extra = []);
```

Check user blocked bot or not.

```php
$bot->isActive($chatId, $action = 'typing', $extra = []);
```

Get a url like `api.telegram.org/file/bot123/file_123`.

```php
$bot->getFileUrl(string $fileId);
```

Download file by `file_id`.

Returns full path to saved file.

```php
$bot->download($fileId, $savePath): string;
```

Fun method to change the text by delay.

```php
$bot->loading(array $elements = [], $delay = 1);

// example
$bot->loading(['🌕', '🌖', '🌗', '🌘', '🌑', '🌒', '🌓', '🌔'], 1);
```

Auto detect caption or text callback and edit this text.

```php
$bot->editCallbackMessage(string $text, $keyboard = null, $extra = []);
```

Edit source message received from callback.

```php
$bot->editCallbackText(string $text, $keyboard = null, $extra = []);
```

Edit source message with photo received from callback.

```php
$bot->editCallbackCaption(string $text, $keyboard = null, $extra = []);
```

Send `var_export` message.

```php
$bot->dump($data, $userId = null);
```

Send `json` message.

```php
$bot->json(array|string|int $data, $userId = null);
```