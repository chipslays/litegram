# Helpers

List of available helpers:

```php
bot(string $token = null, array $config = null)
keyboard($keyboard = false, $oneTime = false, $resize = true, $selective = false)
keyboard_hide($selective = false)
keyboard_add($keyboards = [])
keyboard_set($keyboards = [])
say($text, $keyboard = null, $extra = [])
reply($text, $keyboard = null, $extra = [])
notify($text = '', $showAlert = false, $extra = [])
action($action = 'typing', $extra = [])
dice($emoji = '🎲', $keyboard = null, $extra = [])
update($key = null, $default = null)
config($key = null, $default = null)
plural($count, array $forms)
lang(string $key, array $replacement = null, string $language = null)
util()
cache()
store()
user()
state()
logger()
session()
db($table = null)
bot_print($data, $userId = null)
bot_json($data, $userId = null)
wait($seconds = 1)
debug_print($data)
debug_json($data)










```