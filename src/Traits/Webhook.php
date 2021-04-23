<?php

namespace Litegram\Traits;

use Litegram\Update;

trait Webhook
{
    /**
     * Handle update from Telegram or set force update fro somethere.
     *
     * @param array|string|\stdClass|\Chipslays\Collection\Collection $update
     * @return Bot
     * @throws \Exception
     */
    public function webhook($update = null)
    {
        if ($update) {
            $this->setEventData($update);
        } else {
            $input = file_get_contents('php://input');
            if ($input && $input !== '') {
                $this->setEventData($input);
            }
        }

        if (!$this->hasUpdate()) {
            return $this;
        }

        if ($this->update()->has('callback_query')) {
            $this->decodeCallback();
        }

        $this->defaultIdForReply = $this->update('*.chat.id', $this->update('*.from.id'));

        $this->addModule(Update::class);

        // Отпускаем Telegram, чтобы он не ждал и не блокировал остальные запросы.
        if (php_sapi_name() !== 'cli') {
            $this->finishRequest($this->config('bot.timelimit', 1800));
        }

        return $this;
    }
}