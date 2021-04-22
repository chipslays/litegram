<?php

namespace Litegram\Traits;

use Litegram\Bot;
use Chipslays\Event\EventTrait;

trait Events
{
    use EventTrait;

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

        // $this->loadComponents();

        if ($this->beforeCallbacks !== []) {
            foreach ($this->beforeCallbacks as $callback) {
                $this->callController($callback);
            }
        }

        if (!$this->runAllEvents()) {
            $this->executeDefaultAnswers();
        }

        if ($this->afterCallbacks !== []) {
            foreach ($this->afterCallbacks as $callback) {
                $this->callController($callback);
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
}
