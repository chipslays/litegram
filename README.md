# 🍃 Litegram

![GitHub Workflow Status](https://img.shields.io/github/workflow/status/chipslays/litegram/tests)
![GitHub](https://img.shields.io/github/license/chipslays/litegram?color=blue)

Simple, flexible, modular library based on events for Telegram Bot Api.

Litegram can be used as a regular lightweight library or as a framework with added of plugins.

## Features
* Events based;
* Support Cache (Redis, Memcached);
* Middlewares;
* Localization;
* Sessions;
* Storage (Flat files, Database);
* Manage users;
* Database (Laravel);
* Talk (Chain Conversation);
* Validation;
* Stemming;
* Statistics;
* Plugins and Components;
* Support Webhooks & Simple long-polling;

## Installation

### Library
Download library via Composer:

```bash
$ composer require chipslays/litegram
```

### Litegram Project

1️⃣ Create project:

```bash
$ composer create-project chipslays/litegram-skeleton MySuperDuperBot
```

2️⃣ Change the parameters of the configs and finally type in Terminal:

```bash
$ php lite webhook:set
```

```bash
$ php lite migration:up
```

🎉 Congratulation, bot project was set up.

## Examples

```php
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->command('start', function () use ($bot) {
    $bot->ask('What is your name?', function () use ($bot) {
        $name = $bot->payload('message.text');
        $bot->reply("👋 Nice to meet you, {$name}!");
    });
});

// or
$bot->command('start', 'BotController@startConversation');

$bot->run();
```

More examples you can see [`here`](https://github.com/chipslays/litegram/tree/v3.x.x/examples).

## Documentation
Documentation can be found [`here`](https://github.com/chipslays/litegram/tree/v3.x.x/docs).

## [VS Code Extension](https://marketplace.visualstudio.com/items?itemName=chipslays.litegram-snippets)

Install [Litegram Snippets](https://marketplace.visualstudio.com/items?itemName=chipslays.litegram-snippets) extension for VS Code to increase productivity.

## License
Released under the MIT public license. See the enclosed [`LICENSE`](https://github.com/chipslays/litegram/tree/v3.x.x/LICENSE.md) for details.
