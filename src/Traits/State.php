<?php 

namespace Litegram\Traits;

use Litegram\Modules\State as StateModule;
use Litegram\Update;

trait State 
{
    /**
     * Продолжить выполнение если хотя бы один стейт совпадает
     *
     * @param string|array $states
     * @return Bot
     */
    public function state($states, $stopWords = [])
    {
        if ($stopWords !== [] && in_array(Update::getText(), $stopWords)) {
            $this->canContinueEvent = false;
            return $this;
        }

        if ($this->canContinueEvent === false) {
            return $this;
        } 

        foreach ((array) $states as $stateName) {
            $this->canContinueEvent = StateModule::$name == $stateName;

            if ($this->canContinueEvent === true) {
                break;
            }
        }
        
        return $this;
    }
}