<?php

// More examples for on() method see here:
// https://github.com/chipslays/event#usage

require __DIR__ . '/../vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->on('message.text', function () use ($bot) {
    // Universal executor for all Telegram methods
    $bot->method('sendMessage', [
        'chat_id' => update('message.from.id'),
        'text' => 'Hello!',
    ]);

    $bot->sendMessage(update('message.from.id'), 'Hello!');
    $bot->replyMessage('Hello!');
});

$bot->on(['message.text' => 'hello'], function () use ($bot) {
    reply('Hello!');
});

$bot->hear('hello', function () {
    say('Hello!');
});

$bot->hear(['hello', '/^holla$/i'], function () {
    say('Hello!');
});

$bot->command('ban {user} {time?}', function ($user, $time = null) {
    echo "Banned: {$user}, time: " . $time ?? strtotime('+7 days');
});

$bot->run();
