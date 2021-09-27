<?php

namespace Litegram\Traits;

use Litegram\Payload;
use Litegram\Plugins\State as StatePlugin;

trait State
{
    /**
     * Продолжить выполнение если хотя бы один стейт совпадает
     *
     * @param string|array $states
     * @param array $excepts Stop-words
     * @return Bot
     */
    public function state($states, $except = [], $simple = true)
    {
        if ($except !== [] && $simple && in_array(Payload::getText(), $except)) {
            $this->canContinueEvent = false;
            return $this;
        }

        if ($except !== [] && !$simple && $this->in($except, Payload::toArray())) {
            $this->canContinueEvent = false;
            return $this;
        }

        if ($this->canContinueEvent === false) {
            return $this;
        }

        foreach ((array) $states as $stateName) {
            $this->canContinueEvent = StatePlugin::$name == $stateName;
            if ($this->canContinueEvent === true) {
                break;
            }
        }

        return $this;
    }
}