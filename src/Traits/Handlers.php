<?php

namespace Litegram\Traits;

use Litegram\Payload;
use Litegram\Support\Collection;
use Litegram\Exceptions\LitegramException;

trait Handlers
{
   /**
     * Handle update from Telegram or set force update for somethere.
     *
     * @param array|string|\stdClass|Collection $payload
     * @return Bot
     * @throws LitegramException
     */
    public function webhook($payload = null)
    {
        if ($payload) {
            $this->setEventData($payload);
        } else {
            $payload = file_get_contents('php://input');
            if ($payload && $payload !== '') {
                $this->setEventData($payload);
            }
        }

        if (php_sapi_name() !== 'cli') {
            $this->finishRequest($this->config('bot.timelimit', 1800));
        }

        if (!$this->hasPayload()) {
            throw new LitegramException("All right, but not have payload from Telegram.", 1);
        }

        $this->decodeCallback();

        Payload::make($this->payload()->toArray());

        $this->defaultIdForReply = $this->payload('*.chat.id', $this->payload('*.from.id'));

        return $this;
    }

    /**
     * Handle updates (long-polling).
     *
     * @param callable|string $callback
     * @param array $extra
     * @return void
     */
    public function longpoll($callback = null, $extra = [])
    {
        $this->cli->log('Long-polling started...');

        $offset = 0;

        while (true) {
            $updates = $this->api('getUpdates', array_merge([
                'offset' => $offset,
                'limit' => '25',
                'timeout' => 0,
            ], $extra));

            foreach ($updates->get('result', []) as $update) {
                $this->setEventData(new Collection($update));

                $offset = $update['update_id'] + 1;

                $this->decodeCallback();

                Payload::make($this->payload()->toArray());

                $this->defaultIdForReply = $this->payload('*.chat.id', $this->payload('*.from.id'));

                if ($callback) {
                    $this->call($callback, [$this->payload(), $this]);
                }

                $this->run();

                // resets for new handle
                $this->events = [];
                $this->skip(false);
            }
        }
    }
}