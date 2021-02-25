<?php

namespace Litegram\Traits\Telegram;

use Litegram\Modules\User;

trait Events
{
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
     * @param array|string $event
     * @param callable|string $callback
     * @param integer $sort
     * @return \Litegram\Bot
     */
    public function on($event, $callback, int $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!$this->canContinueEvent()) {
            return $this;
        }

        $this->addEvent($event, $callback, $sort);

        return $this;
    }

    /**
     * Execute events.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->modules as $alias) {
            if (!method_exists($this->$alias, 'beforeRun')) {
                continue;
            }
            call_user_func_array([$this->$alias, 'beforeRun'], []);
        }

        if (!$this->runAllEvents()) {
            $this->executeDefaultAnswers();
        }

        if (php_sapi_name() !== 'cli') {
            $this->sayGoodbyeTelegramAndContinueEvent(120);
        }

        foreach ($this->modules as $alias) {

            if (!method_exists($this->$alias, 'afterRun')) {
                continue;
            }
            call_user_func_array([$this->$alias, 'afterRun'], []);
        }
    }

    /**
     * Выполнить функцию если не было поймано ни одно событие.
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
                    $this->callFunction($answer['func']);
                    return;
                }
            }
        }
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
    public function onText($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
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
    public function hear($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
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
    public function onCommand($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if ($this->isMessage() && $this->isCommand()) {
            return $this->preventNextStep();
        }

        $data = array_map(function ($item) {
            if (in_array(mb_substr($item, 0, 1, 'utf-8'), $this->getCommandTags())) {
                // передан текст на отлов как "/команда", "!команда"
                return ['message.text' => $item];
            } else {
                // передан текст на отлов как "команда"
                return ['message.text' => mb_substr($this->getCommand(), 0, 1, 'utf-8') . $item];
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
    public function command($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
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
    public function onCallback($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!$this->isCallbackQuery()) {
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
    public function callback($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
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
    public function onInline($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!$this->isInlineQuery()) {
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
    public function query($data, $func, $sort = BOT_DEFAULT_SORT_VALUE)
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
    public function onMessage($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!$this->update()->has('message')) {
            return $this->preventNextStep();
        }

        return $this->on('message', $func, $sort);
    }

    /**
     * @return void
     */
    public function onFlood($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!User::isFlood()) {
            return;
        }

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [User::getFloodTime()],
        ];
    }

    /**
     * @return void
     */
    public function onAdmin($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!User::isAdmin()) {
            return;
        }

        $this->events[$sort][] = [
            'func' => $func,
        ];
    }

    /**
     * @return void
     */
    public function onFirstTime($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!User::firstTime()) {
            return;
        }

        $this->events[$sort][] = [
            'func' => $func,
        ];
    }

    /**
     * @return void
     */
    public function onBanned($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!User::isBanned()) {
            return;
        }

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [User::get('ban_date_from'), User::get('ban_date_to'), User::get('ban_comment')], // from, to, comment
        ];
    }

    /**
     * @return void
     */
    public function onNewVersion($func, $sort = BOT_DEFAULT_SORT_VALUE)
    {
        if (!User::newVersion()) {
            return;
        }

        $this->events[$sort][] = [
            'func' => $func,
            'args' => [User::get('version'), $this->config('bot.version')], // old version, new version
        ];
    }
}
