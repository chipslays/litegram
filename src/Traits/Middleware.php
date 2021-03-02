<?php

namespace Litegram\Traits;

trait Middleware
{
    /**
     * Массив с middlewares
     *
     * @var array
     */
    private $middlewares = [];

    public function middleware(...$args)
    {
        if ($this->canContinueEvent === false) {
            return $this;
        }

        /** 
         * Для цепочного вызова
         * 
         * Добавление:
         * $bot->addMiddleware('name', function () {...});
         * 
         * Использование:
         * $bot->middleware('name')->hear(...);
         * 
         * Поддержка нескольких middlewares
         * $bot->middleware(['first', 'second'])->hear(...);
         * 
         * NOTE: вызывать перед любым событием
         */
        if (count($args) == 1) {
            foreach ((array) $args[0] as $key) {
                $this->canContinueEvent = isset($this->middlewares[$key]) ? $this->callController($this->middlewares[$key]) : false;

                if ($this->canContinueEvent === false) {
                    break;
                }
            }

            return $this;
        }

        /** 
         * Для "обертки"
         * 
         * Добавление:
         * $bot->addMiddleware('name', function ($next) {
         *      // do something before
         *      $next()
         *      // do something after 
         * });
         * 
         * Использование:
         * $bot->middleware('name', function () {
         *      // your code here ...
         * });
         */
        if (count($args) == 2) {
            return isset($this->middlewares[$args[0]]) ? $this->callController($this->middlewares[$args[0]], [$args[1]]) : false;
        }
    }

    /**
     * @param string $name
     * @param callable $func
     * @return void
     */
    public function addMiddleware(string $name, $func): void
    {
        $this->middlewares[$name] = $func;
    }
}
