<?php

use Litegram\Plugins\Session;
use Litegram\Plugins\Storage;

require __DIR__ . '/vendor/autoload.php';

$bot = bot([
    'bot' => [
        'token' => '1234567890:BOT_TOKEN',
    ],
    'plugins' => [
        'storage' => [
            'driver' => 'file',
            'drivers' => [
                'file' => [
                    'dir' => __DIR__ . '/storage',
                ],
                'database' => [],
            ],
        ],
    ],
])->webhook();

$bot->plugins([
    Storage::class,
    Session::class,
]);

// for convenience just set except array, property name can be any
$bot->exceptMessage = [['message.text' => '/cancel']];

$bot->default('message', fn () => $bot->reply('The message was not recognized.'));

$bot->command('form', function () {
    bot()->ask('What is your name?', function () {

        // some logic with name...

        bot()->ask('Send your e-mail.', function () {
            if (!is()->email()->validate(payload('message.text'))) {
                reply('Bad email, try again.');
                return false;
            }

            // some logic with email...

            say('Thanks for submitting data!');
        }, bot()->exceptMessage);
    }, bot()->exceptMessage);
},);

$bot->command('delete', function () {

    // low-level ask
    bot()->ask([
        'text' => 'Are you sure to delete this post?',
        'accept' => [['message.text' => '/yes|no/i'], /** etc arrays */],
        'except' => [['message.text' => '/stop'], /** etc arrays */],
        'callback' => function ($answer) {
            if (strtolower($answer->get('message.text')) == 'yes') {
                return reply('Post was deleted.');
            }

            reply('Delete canceled.');
        },
        'fallback' => function ($answer) {
            say('Please, say YES or NO.');
        },
    ]);
});

$bot->command('reset', function () {
    bot()->resetAsk();
    say('Form was canceled.');
});

$bot->run();