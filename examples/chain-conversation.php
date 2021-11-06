<?php

use Litegram\Plugins\Session;
use Litegram\Plugins\Storage;

require __DIR__ . '/../vendor/autoload.php';

$bot = bot([
    'bot' => [
        'token' => '1234567890:BOT_TOKEN',
    ],

    'plugins' => [
        'storage' => [
            'driver' => null, // null - store data in RAM (useful for long-poll)
            'drivers' => [
                'file' => [
                    'dir' => __DIR__ . '/storage',
                ],
                'database' => [],
            ],
        ],
        'database' => [
            'driver' => 'mysql',
            'drivers' => [
                'mysql' => [
                    'host'      => 'localhost',
                    'prefix'    => 'litegram_',
                    'database'  => 'litegram',
                    'username'  => 'litegram',
                    'password'  => 'litegram',
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                ],
            ],
        ],
    ],
]);

// handle update
$bot->webhook();

$bot->plugins([
    Storage::class,
    Session::class,

    // we can use `storage` plugin with `database` or `file` driver
    // Database::class
]);

// handle for cancel form
$bot->command('cancel', function () {
    say('Form was canceled.');
    chain(false);
});

// handle for start chain
$bot->command('form', function () {
    say('Whats ur name?');

    // start chain conversation
    chain('name');
    // or bot()->setChain('name')
});

// we wait `name`, after set `phone`
$bot->chain('name', 'phone', function () {
    $validator = is()->alnum()->noWhitespace()->length(1, 15);
    if (!$validator->validate($name = payload('message.text'))) {
        reply('❌ Oops, not valid name!');

        // tip: return `false` for prevent next step.
        // we return `false`, next step `email` not will be set
        return false;
    }

    say('✅ Ok, cool name! Now send me ur phone.');
    Session::push('form_data', 'name', $name);
}, ['message.text' => '/cancel']);

// we wait `phone`, after set `email`
$bot->chain('phone', 'email', function () {
    if (!validate('phone', $phone = payload('message.text'))) {
        say('❌ Oops, not valid phone!');
        return false;
    }

    say('✅ We save ur phone, now send me ur email.');

    Session::push('form_data', 'phone', $phone);
}, ['message.text' => '/cancel']);

// set next `false` or `null` for finish chain conversation
$bot->chain('email', false, function () {
    if (!validate('email', $email = payload('message.text'))) {
        say('❌ Oops, not valid email!');
        return false;
    }

    say('🎉 Thanks for submit ur data!');

    Session::push('form_data', 'email', $email);

    bot_json(Session::get('form_data'));
}, ['message.text' => '/cancel']);

$bot->run();