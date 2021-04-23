<?php

return [
    'bot' => [
        'token' => '1234567890:BOT_TOKEN',
        'handler' => 'https://example.com/handler.php',
        'name' => 'Litegram Bot',
        'username' => 'LitegramBot',
        'version' => '1.0.0',
        'timezone' => 'Europe/Samara',
        'timelimit' => 120,
    ],
    'telegram' => [
        'parse_mode' => 'html',
        'safe_callback' => true,
    ],
    'debug' => [
        'enable' => true,
        'developer' => '436432850',
    ],
    'admin' => [
        'list' => [
            'chipslays' => 'password',
            '436432850' => 'password',
        ],
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
        'cache' => [
            'enable' => false,
            'driver' => 'memcached',
            'memcached' => [
                'host'  => 'localhost',
                'port' => '11211',
            ],
            'redis' => [
                'host'  => '127.0.0.1',
                'port' => '6379',
            ],
        ],
        'store' => [
            'enable' => false,
            'driver' => 'database',
            'file' => [
                'dir' => __DIR__ . '/storage/store',
            ],
        ],
        'session' => [
            'enable' => true,
        ],
        'user' => [
            'enable' => false,
            'flood_time' => 1,
        ],
        'state' => [
            'enable' => false,
        ],
        'localization' => [
            'enable' => false,
            'driver' => 'php', // php, serialize
            'default' => 'en',
            'dir' => __DIR__ . '/localization',
        ],
        'logger' => [
            'enable' => false,
            'dir' => __DIR__ . '/storage/logs',
            'auto' => true,
        ],
        'statistics' => [
            'updates' => false,
            'messages' => true,
            'users' => true,
        ],
    ],
    'components' => [
        'vendor.component' => [
            'enable' => false,
            'entrypoint' => __DIR__ . '/components/vendor/component/autoload.php',
        ],
    ],
];