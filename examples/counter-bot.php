<?php

use Litegram\Plugins\Session;
use Litegram\Plugins\Storage;

require __DIR__ . '/../vendor/autoload.php';

$bot = bot([
    'bot' => [
        'token' => '1234567890:BOT_TOKEN',
    ],
    'telegram' => [
        'safe_callback' => true,
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

// add storage & session plugins
$bot->plugins([
    Storage::class,
    Session::class,
]);

// start command
$bot->command('start', function () {
    $clicks = getClicks();

    say("Counter: {$clicks}", keyboard([
        [
            ['text' => '+1', 'callback_data' => 'clicked'],
        ],
        [
            ['text' => 'Reset', 'callback_data' => 'reset'],
        ],
    ]));
});

// click callback
$bot->callback('clicked', function () {
    $clicks = getClicks(true);
    refreshMessage($clicks);
});

// reset callback
$bot->callback('reset', function () {
    Session::set('clicks', 0);
    refreshMessage(0);
});

// utils
function getClicks($withIncrement = false) {
    $clicks = Session::get('clicks', 0);
    Session::set('clicks', $withIncrement ? ++$clicks : $clicks);
    return $clicks;
}

function refreshMessage($clicks) {
    bot()->editCallbackMessage("Counter: {$clicks}", keyboard(util()->getKeyboardFromCallback()));
}

// run bot
$bot->run();