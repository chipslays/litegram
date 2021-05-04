# WIP:Modules

### Что такое модули?

Это дополнительный функционал разбитый на отдельные классы, модули могут зависить друг от друга и важно соблюдать последовательность подключения.

Например, модуль `Localization`, если в настройках включен модуль `User`, то последний должен быть подключен раньше `Localization`.

### Подключение модулей

```php
$bot->addModule(Litegram\Modules\Logger::class)
    ->addModule(Litegram\Modules\Cache::class)
    ->addModule(Litegram\Modules\Database::class)
    ...
```

Вот рабочий вариант подключения всех модулей, ненужные можно убрать.

```php
$bot->addModule(Litegram\Modules\Logger::class)
    ->addModule(Litegram\Modules\Cache::class)
    ->addModule(Litegram\Modules\Database::class)
    ->addModule(Litegram\Modules\Store::class)
    ->addModule(Litegram\Modules\User::class)
    ->addModule(Litegram\Modules\State::class)
    ->addModule(Litegram\Modules\Localization::class)
    ->addModule(Litegram\Modules\Statistics::class)
    ->addModule(Litegram\Modules\Session::class);
```

### Использование модулей

```php
// Как статический класс:
use Litegram\Modules\Store;
use Litegram\Modules\User;

Store::set('key', $value);
User::update(['fullname' => 'Litegram']);
```

```php
// Как алиас метод:
$bot->store()->set('key', $value);
$bot->user()->update(['fullname' => 'Litegram']);
```

```php
// Через метод module:
$bot->module('store')->set('key', $value);
$bot->module('user')->update(['fullname' => 'Litegram']);
```
