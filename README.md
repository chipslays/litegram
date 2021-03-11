# WIP: Litegram

Simple, flexible, modular library based on events for Telegram Bot Api.

## Installation

```bash
$ composer require chipslays/litegram
```

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

More examples you can see [`here`](https://github.com/chipslays/litegram/tree/master/examples).

## Documentation
Documentation can be found [`here`](https://github.com/chipslays/litegram/tree/master/docs).

## License
Released under the MIT public license. See the enclosed [`LICENSE`](https://github.com/chipslays/litegram/tree/master/LICENSE.md) for details.
