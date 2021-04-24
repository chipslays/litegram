<?php

use Litegram\Modules\Database;
use Litegram\Modules\Session;
use Litegram\Modules\Store;

require __DIR__ . '/../vendor/autoload.php';

$bot = bot([
    'bot' => [
        'token' => '1234567890:BOT_TOKEN',
    ],
    'modules' => [
        'database' => [
            'enable' => false,
            'driver' => 'mysql',
            'sqlite' => [
                'database' => '/path/to/database.sqlite',
            ],
            'mysql' => [
                'host'      => 'localhost',
                'database'  => 'telegram_test',
                'username'  => 'mysql',
                'password'  => 'mysql',
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ],
        'store' => [
            'enable' => true,
            'driver' => 'file',
            'file' => [
                'dir' => __DIR__ . '/storage/store',
            ],
        ],
        'session' => [
            'enable' => true,
        ],
    ],

]);

// handle update
$bot->webhook();

// we can use `store` module with `database` or `file` driver
// $bot->addModule(Database::class);
$bot->addModule(Store::class);
$bot->addModule(Session::class);

// handle for start chain
$bot->command('form', function () {
    say('Whats ur name?');

    // start chain conversation
    chain('name');
    // or bot()->setChain('name')
});

// we wait `name`, after set `email`
$bot->chain('name', 'phone', function () {
    say('Ok, cool name! Now send me ur phone.');
    Session::add('form_data', 'name', update('message.text'));
});

// we wait `phone`, after set `email`
$bot->chain('phone', 'email', function () {
    $phone = update('message.text');

    if ($phone !== 'not valid phone') {
        say('ur phone not valid');

        // tip: return `false` for prevent next step.
        // we return `false`, next step `email` not will be set
        return false;
    }

    say('We save ur phone, now send me ur email.');

    Session::add('form_data', 'phone', $phone);
});

// set next `false` or `null` for finish chain conversation
$bot->chain('email', false, function () {
    $email = update('message.text');

    if ($email !== 'not valid email') {
        echo "ur email not valid";
        return false;
    }

    say('Thanks for submit ur data!');

    Session::add('form_data', 'email', $email);
    bot_json(Session::get('form'));
});

$bot->run();