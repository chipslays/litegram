<?php 

use Litegram\Bot;

require __DIR__ . '/../vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->on('message.text', function () use ($bot) {
    $bot->api('sendMessage', [
        'chat_id' => $bot->update()->get('message.from.id'),
        'text' => 'Hello!',
    ]);
});

$bot->on(['message.text' => 'hello'], function () use ($bot) {
    reply('Hello!');
});

$bot->command('ban {user} {time?}', function ($user, $time = null) {
    echo "Banned: {$user}, time: " . $time ?? strtotime('+7 day'); 
});

$bot->run();

