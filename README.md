# 🍃 Litegram

Simple, flexible, modular library based on events for Telegram Bot Api.

Litegram can be used as a regular lightweight library or as a framework with added of modules.

## Features
* Events based;
* Database (Laravel);
* Support Cache (Redis, Memcached);
* Middlewares;
* Localization;
* States;
* Sessions;
* Store (Flat files, Database);
* Collect Statistics;
* Manage users;
* Modules and Components;
* Talk (Chain Conversation);
* Validation;
* Stemming;

## Installation

### Library
Install as simple library:

```bash
$ composer require chipslays/litegram
```

### [Litegram Skeleton](https://github.com/chipslays/litegram-skeleton)

Create ready for use skeleton project (framework?🤔):

```bash
$ composer create-project chipslays/litegram-skeleton MySuperDuperBot
```

Or deploy to [Heroku](https://heroku.com):

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/chipslays/litegram-skeleton)

Now, in `/MySuperDuperBot/config` folder edit the configs.

Finally type in Terminal:

```bash
$ php lite webhook:set
```

```bash
$ php lite migration:up
```

🎉 Congratulation, bot project was set up.

## Examples

Pretty simple Echo Bot:

```php
require 'vendor/autoload.php';

$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->hear('{text}', function ($text) {
    reply($text);
});

$bot->run();
```

**Need just notification for your app?**

Auth once and use anywhere in your project.

```php
// bootstrap.php
require 'vendor/autoload.php';
bot('1234567890:BOT_TOKEN');

// payment.php
bot()->sendMessage($chatId, 'User on checkout page...');
```

**Or... Maybe you needed powerful framework?**

```php
require 'vendor/autoload.php';

$bot = bot($config)->webhook();

$bot->addModule(Database::class)
    ->addModule(Store::class)
    ->addModule(User::class)
    ->addModule(Session::class)
    ->addModule(State::class)
    ->addModule(Localization::class)
    ->addModule(Statistics::class)
    ->addModule(Logger::class)
    ->addModule(Talk::class);

// Do something...

$bot->run();
```

More examples you can see [`here`](https://github.com/chipslays/litegram/tree/master/examples).

## Documentation
Documentation can be found [`here`](https://github.com/chipslays/litegram/tree/master/docs).

## License
Released under the MIT public license. See the enclosed [`LICENSE`](https://github.com/chipslays/litegram/tree/master/LICENSE.md) for details.
