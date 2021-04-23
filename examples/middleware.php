<?php

require __DIR__ . 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->addMiddleware('test', function ($next) {
    // Do something berfore
    $next();
    // Do something after
});

$bot->middleware('test', function () use ($bot) {
    $bot->hear('/test/i', function () {
        say('middleware passed');
    });
});

// Return True for pass, False for prevent next chain
$bot->addMiddleware('simple', fn() => true);

$bot->middleware('simple')->hear('/test/i', function () {
    say('middleware passed');
});

// Simple support multiple middleware, pass his as array
// If any middleware return False all chain be prevent.
$bot->middleware(['first', 'second', 'etc'])->hear('/test/i', function () {
    say('middleware passed');
});

$bot->run();