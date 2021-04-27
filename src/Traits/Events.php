<?php

namespace Litegram\Traits;

use Litegram\Bot;
use Chipslays\Event\EventTrait;
use Litegram\Modules\User;
use Litegram\Update;

define('LITEGRAM_DEFAULT_EVENT_SORT', 500);

trait Events
{
    use EventTrait;

    /**
     * Skip run events.
     *
     * @var boolean
     */
    private $skipRunEvents = false;

    /**
     * @var boolean
     */
    private $canContinueEvent = true;

    /**
     * Массив с ответами по умолчанию
     *
     * @var array
     */
    private $defaultAnswers = [];

    /**
     * @var array
     */
    private $beforeCallbacks = [];

    /**
     * @var array
     */
    private $afterCallbacks = [];

    /**
     * @param array|string $event
     * @param callable|string|array $callback
     * @param integer $sort
     * @return Bot
     */
    public function on($event, $callback, int $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!$this->canContinueEvent()) {
            return $this;
        }

        $this->addEvent($event, $callback, $sort);

        return $this;
    }

    public function run()
    {
        foreach ($this->modules as $alias) {
            if (!method_exists($this->$alias, 'beforeRun')) {
                continue;
            }
            call_user_func_array([$this->$alias, 'beforeRun'], []);
        }

        $this->loadComponents();

        if ($this->beforeCallbacks !== []) {
            foreach ($this->beforeCallbacks as $callback) {
                $this->call($callback);
            }
        }

        if (!$this->skipRunEvents) {
            if (!$this->runAllEvents()) {
                $this->executeDefaultAnswers();
            }
        }

        if ($this->afterCallbacks !== []) {
            foreach ($this->afterCallbacks as $callback) {
                $this->call($callback);
            }
        }

        foreach ($this->modules as $alias) {
            if (!method_exists($this->$alias, 'afterRun')) {
                continue;
            }
            call_user_func_array([$this->$alias, 'afterRun'], []);
        }
    }

    /**
     * Add to before run (possible use multiple times)
     *
     * @param callable|string $callback
     * @return void
     */
    public function onBeforeRun($callback)
    {
        $this->beforeCallbacks[] = $callback;
    }

    /**
     * Add to after run (possible use multiple times)
     *
     * @param callable|string $callback
     * @return void
     */
    public function onAfterRun($callback)
    {
        $this->afterCallbacks[] = $callback;
    }

    /**
     * Выполнить функцию если не было поймано ниодно событие.
     *
     * @param string|array $data Ключ (массив значит "ИЛИ", хотя бы один ключ совпадает)
     * @param $func
     * @return void
     */
    public function default($data, $func)
    {
        $this->defaultAnswers[] = [
            'data' => (array) $data,
            'func' => $func,
        ];
    }

    /**
     * Выполнить функции по умолчанию.
     *
     * @return void
     */
    private function executeDefaultAnswers()
    {
        if (!$this->defaultAnswers) {
            return;
        }

        foreach ($this->defaultAnswers as $answer) {
            foreach ($answer['data'] as $key) {
                if ($this->update()->has($key)) {
                    $this->call($answer['func']);
                    return;
                }
            }
        }
    }

    private function canContinueEvent(): bool
    {
        if ($this->canContinueEvent === false) {
            $this->canContinueEvent = true;
            return false;
        }

        return true;
    }

    public function preventNextStep()
    {
        $this->canContinueEvent = true;
        return $this;
    }

    /**
     * Обработка входящих текстовых сообщений.
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function onText($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!$this->update()->has('message.text')) {
            return $this->preventNextStep();
        }

        $data = array_map(function ($item) {
            return ['message.text' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for onText() method;
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function hear($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        return $this->onText($data, $func, $sort);
    }

    /**
     * Обработка входящих команд.
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function onCommand($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!Update::isMessage() && !Update::isCommand()) {
            return $this->preventNextStep();
        }

        $data = array_map(function ($item) {
            if (in_array(mb_substr($item, 0, 1, 'utf-8'), $this->getCommandTags())) {
                // передан текст на отлов как "/команда", "!команда"
                return ['message.text' => $item];
            } else {
                // передан текст на отлов как "команда"
                return ['message.text' => mb_substr(Update::getCommand(), 0, 1, 'utf-8') . $item];
            }
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for onCommand() method;
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function command($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        return $this->onCommand($data, $func, $sort);
    }

    /**
     * Обработка входящего коллбэка.
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function onCallback($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!Update::isCallbackQuery()) {
            return $this->preventNextStep();
        }

        $data = array_map(function ($item) {
            return ['callback_query.data' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for onCallback() method;
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function callback($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        return $this->onCallback($data, $func, $sort);
    }

    /**
     * Обработка инлайн запроса.
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function onInline($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!Update::isInlineQuery()) {
            return $this->preventNextStep();
        }

        $data = array_map(function ($item) {
            return ['inline_query.query' => $item];
        }, (array) $data);

        return $this->on($data, $func, $sort);
    }

    /**
     * Short alias for onInline() method;
     *
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function inline($data, $func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        return $this->onInline($data, $func, $sort);
    }

    /**
     * @param string|array $data
     * @param callable|string $func
     * @param integer $sort
     *
     * @return \Litegram\Bot
     */
    public function onMessage($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!$this->update()->has('message')) {
            return $this->preventNextStep();
        }

        return $this->on('message', $func, $sort);
    }

    /**
     * @return void
     */
    public function onFlood($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!User::isFlood()) {
            return;
        }

        $this->addEvent(true, [$func, User::getFloodTime()], $sort);
    }

    /**
     * @return void
     */
    public function onAdmin($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!User::isAdmin()) {
            return;
        }

        $this->addEvent(true, $func, $sort);
    }

    /**
     * @return void
     */
    public function onFirstTime($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!User::firstTime()) {
            return;
        }

        $this->addEvent(true, $func, $sort);
    }

    /**
     * Callback function params: `$from`, `$to`, `$comment`.
     *
     * @return void
     */
    public function onBanned($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!User::isBanned()) {
            return;
        }

        $this->addEvent(true, [$func, User::get('ban_date_from'), User::get('ban_date_to'), User::get('ban_comment')], $sort);
    }

    /**
     * Callback function params: `$old`, `$new` version.
     *
     * @return void
     */
    public function onNewVersion($func, $sort = LITEGRAM_DEFAULT_EVENT_SORT)
    {
        if (!User::newVersion()) {
            return;
        }

        $this->addEvent(true, [$func, User::get('version'), $this->config('bot.version')], $sort); // old version, new version
    }
}
