# Litegram

Simple, flexible, modular library based on events for Telegram Bot Api.

## Installation

### Library
Install as simple library:

```bash
$ composer require chipslays/litegram
```

### Project

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

## Example

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
bot('1234567890:BOT_TOKEN');

// payment.php
bot()->sendMessage($chatId, 'User on checkout page...');
```

**Or... Maybe you needed powerful framework?**

```php
$bot = bot('1234567890:BOT_TOKEN')->webhook();

$bot->addModule(Litegram\Modules\Logger::class)
    ->addModule(Litegram\Modules\Cache::class)
    ->addModule(Litegram\Modules\Database::class)
    ->addModule(Litegram\Modules\Store::class)
    ->addModule(Litegram\Modules\User::class)
    ->addModule(Litegram\Modules\State::class)
    ->addModule(Litegram\Modules\Localization::class)
    ->addModule(Litegram\Modules\Statistics::class)
    ->addModule(Litegram\Modules\Session::class);
    
// do something...

$bot->run();
```


More examples you can see [`here`](https://github.com/chipslays/litegram/tree/master/examples).

## Documentation
Documentation can be found [`here`](https://github.com/chipslays/litegram/tree/master/docs).

## License
Released under the MIT public license. See the enclosed [`LICENSE`](https://github.com/chipslays/litegram/tree/master/LICENSE.md) for details.
