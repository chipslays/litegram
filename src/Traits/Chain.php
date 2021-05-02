<?php

namespace Litegram\Traits;

use Litegram\Update;
use Litegram\Modules\Session;

trait Chain
{
     /**
     * Set current chain name.
     *
     * @param string|null $name
     * @return Bot
     * @throws \Exception
     */
    public function setChain(?string $name)
    {
        if (!$this->isModuleExists('session')) {
            throw new \Exception("Please add `session` module before start chain.");
        }

        Session::set('__chain', $name);

        return $this;
    }

    /**
     * Chain dialog flow.
     *
     * @param string $current
     * @param string|null $next
     * @param callable|string $func Function must return `false` for prevent `$next` step.
     * @param array $excepts Array of `path=value` for skip chain. E.g. `['message.text' => '/cancel']`.
     * @return Bot
     */
    public function chain(string $current, ?string $next, $func, array $excepts = [])
    {
        if ($excepts !== []) {
            if ($this->in($excepts, Update::toArray())) {
                return true;
            }
        }

        if (Session::get('__chain') == $current && !$this->skipped()) {
            if ($this->call($func) !== false) {
                if ($next === null || $next === false) {
                    Session::delete('__chain');
                } else {
                    Session::set('__chain', $next);
                }
            }
            $this->skip(true);
        }

        return $this;
    }

    /**
     * Get current name of chain.
     *
     * @return string|int
     */
    public function currentChain()
    {
        return Session::get('__chain');
    }
}