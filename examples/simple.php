<?php

// More examples for on() method see here:
// https://github.com/chipslays/event#usage

require __DIR__ . '/../vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->on('message.text', function () use ($bot) {
    // Universal executor for all Telegram methods
    $bot->api('sendMessage', [
        'chat_id' => payload('message.from.id'),
        'text' => 'Hello!',
    ]);

    $bot->sendMessage(payload('message.from.id'), 'Hello!');
    $bot->replyWithText('Hello!');

    // or short alias
    $bot->say('Hello!');
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

$bot->command('test:{id}', function ($id) {
    notify('Just test.'); // notification in caht
    notify('Just test.', true); // modal window
});

$bot->command('ban {user} {:time?}', function ($user, $time = null) {
    echo "Banned: {$user}, time: " . $time ?? strtotime('+7 days');
});

$bot->run();