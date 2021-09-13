<?php

use Litegram\Plugins\Talk;

require __DIR__ . '/../vendor/autoload.php';

$bot = bot(require 'config.php');

// just set debug data for example
$bot->webhook(['message' => ['text' => 'hmmm. i want a coffee now...']]);

// default data take from payload `message.text`
$bot->plugins([Talk::class]);

Talk::train([
    'How much is coffee',
    'I would like to know how much coffee costs.',
    'hear how much coffee costs.',
    'where to see the price of coffee?',
    'find out the price of coffee.',
    'How can I find out the price of coffee?',
    'Where are your prices for coffee?',
], function () {
    say("Coffee costs $3.");
});

Talk::train([
    'I want to buy coffee.',
    'I would like to get some coffee.',
    'where can i buy your coffee.',
    'where could I buy coffee.',
    'could i buy some coffee?',
    'how to buy coffee?',
    'How to get coffee?',
    'to buy coffee.',
    'get some coffee.',
], function () {
    say("You can buy coffee on our website.");
});

Talk::train([
    'I want to talk with the operator.',
    'connect me with the operator.',
    'I want to talk with the operator.',
    'I need help.',
    'help me.',
    'I have a problem.',
    'I can not solve the problem.',
], function () {
    say("Okay. I can help you.");
});

// If an equal number of matches is found for different answers,
// then the answer will be taken with more elements of the lines matched.

// For example:
// We have two answer with score 5.

// This answer will win
// [score] => 5
// [matches] => Array
//     (
//         [0] => 1
//         [1] => 1
//         [2] => 1
//         [3] => 1
//         [4] => 1
//     )

// This answer will lose
// [score] => 5
// [matches] => Array
//     (
//         [5] => 5
//     )

// "Matches" this is elements in array:
// Talk::train([
//     'match text 1',
//     'match text 2',
//     'match text 3',
//     '...',
// ], function () {});

// If user we receive text like "hmmm. i want a coffee now...",
// then bot will say: "You can buy coffee on our website."
$bot->run();