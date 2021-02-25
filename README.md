# Litegram

Simple, flexible, modular library for Telegram Bot Api.

# Use Cases

1. Like a simple notification from your app:
   
```php
// Auth once and use bot() anywhere without token in your application;
bot('1234567890:BOT_TOKEN');
bot()->sendMessage('%chatId%', 'Hello World!');
```

2. Like a simple webhook bot without modules:
   
```php
$bot = bot('1234567890:BOT_TOKEN');

$bot->on('message.photo', function () {
    reply('Wow, nice photo!');
});

$bot->command('ban {user} {time?}', function ($user, $time = null) {
    echo "Banned: {$user}, time: " . $time ?? strtotime('+7 days');
});

$bot->hear('My name is {name}', function ($name) {
    reply("Hello {$name}!");
});

$bot->callback('callbackEvent', function () {
    notify("Wow, this is notification?");
});

$bot->query('{query}', function ($query) {
    // handle inline query
});

$bot->run();
```

3. Like a power combine with pre built modules:
   
```php
use Litegram\Modules\Logger;
use Litegram\Modules\Cache;
use Litegram\Modules\Store;
use Litegram\Modules\Database;
use Litegram\Modules\Localization;
use Litegram\Modules\State;
use Litegram\Modules\User;
use Litegram\Modules\Update;

$bot = bot('1234567890:BOT_TOKEN', require 'config.php');

$bot->addModule(Logger::class)
    ->addModule(Cache::class)
    ->addModule(Database::class)
    ->addModule(Store::class)
    ->addModule(State::class)
    ->addModule(Localization::class)
    ->addModule(User::class)
    ->addModule(Update::class);

$bot->on('message.photo', function () {
    reply('Wow, nice photo!');
});

$bot->run();
```