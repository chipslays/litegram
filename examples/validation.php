<?php

require __DIR__ . '/../vendor/autoload.php';

var_dump(is()->email()->validate('example@email.com'));
var_dump(is('email')->validate('example@email.com'));

var_dump(is('contains', 'crab')->validate('chips with crab flavor'));
var_dump(is()->contains('crab')->validate('chips with crab flavor'));

var_dump(validate('email', 'example@email.com'));

var_dump(validate('contains', 'crab', 'chips with crab flavor'));