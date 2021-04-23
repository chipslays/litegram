<?php

require __DIR__ . '/../vendor/autoload.php';

$bot = bot();

// Call function everytime
$bot->map('sum', fn(...$args) => array_sum($args));

echo $bot->sum(...[1000, 300, 30, 5, 2]) . PHP_EOL; // 1337
echo $bot->sum(1000, 900, 90, 5, 2) . PHP_EOL; // 1997

// Call function once
$bot->mapOnce('timestamp', fn() => time());

echo $bot->timestamp() . PHP_EOL; // 1607881889
sleep(3);
echo $bot->timestamp() . PHP_EOL; // 1607881889