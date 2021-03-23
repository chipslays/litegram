# Bot::class

Получить объект класса можно следующим образом:

```php
use Litegram\Bot;

$bot = Bot::getInstance();
$bot->sendMessage(...);
```

```php
use Litegram\Bot;

Bot::getInstance()->sendMessage(...);
```

```php
$bot = bot();
$bot->sendMessage(...);
```

```php
bot()->sendMessage(...);
```



### `auth(?string $token, ?array $config)`
param|required|default|value
---|---|---|----
`$token`|yes|-|string
`$config`|no|-|array\|null

return|
---|
`Litegram\Bot::class`



### `webhook($update = null)`
param|required|default|value
---|---|---|----
$update|no|null|array\|string\|stdClass\|\Chipslays\Collection\Collection

return|
---|
`Litegram\Bot::class`



### `hasUpdate()`

return|
---|
`bool`


### `getToken()`

return|
---|
`string`

### `setToken(string $token)`

param|required|default|value
---|---|---|----
$token|yes|-|string


return|
---|
`Litegram\Bot::class`
