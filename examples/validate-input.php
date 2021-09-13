<?php

require __DIR__ . '/../vendor/autoload.php';

// abstract use case
$bot->on('*', function () {
    $validator = is()->alnum()->noWhitespace()->length(1, 15);
    if (!$validator->validate($name = payload('message.text'))) {
        return reply('❌ Oops, not valid name!');
    }

    say("Nice to meet you, {$name}");
});

// more examples here - https://respect-validation.readthedocs.io/en/latest/
var_dump(is()->email()->validate('example@email.com'));
var_dump(is('email')->validate('example@email.com'));

var_dump(is('contains', 'crab')->validate('chips with crab flavor'));
var_dump(is()->contains('crab')->validate('chips with crab flavor'));

var_dump(validate('email', 'example@email.com'));

var_dump(validate('contains', 'crab', 'chips with crab flavor'));