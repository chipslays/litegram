<?php

namespace Litegram\Traits;

use Litegram\Plugins\Session;

trait Ask
{
    /**
     *     [
     *       'text' => fn () => $bot->reply(strtoupper('Are you sure to delete this post?')),
     *       'text' => 'Are you sure to delete this post?',
     *       'accept' => [['message.text' => '/yes|no/i']],
     *       'except' => [['message.text' => '/stop']],
     *       'callback' => function ($answer, $bot) {
     *           $bot->reply('Post was deleted.');
     *       },
     *       'fallback' => function ($answer, $bot) {
     *           $bot->say('Please, say YES or NO.');
     *       },
     *    ]
     *
     * @param string|array $text
     * @param callable $callback
     * @param array $except
     * @return void
     */
    public function ask($text, $callback = null, $except = [])
    {
        if (is_string($text)) {
            $this->say($text);
            Session::set('litegram:question', [
                'accept' => ['*'],
                'except' => (array) $except,
                'callback' => $callback,
                'fallback' => null,
            ]);
            return;
        }

        $question = $text;

        if (is_string($question['text'])) {
            $this->say($question['text']);
        } else {
            $this->call($question['text']);
        }

        print_r((array) $question['accept']);
        Session::set('litegram:question', [
            'accept' => (array) $question['accept'],
            'except' => (array) $question['except'],
            'callback' => $question['callback'],
            'fallback' => $question['fallback'],
        ]);
    }

    private function checkAnswer()
    {
        // check answer for our question
        $question = Session::pull('litegram:question');

        if ($question && !$this->in($question['except'], $this->payload()->toArray())) {

            // callback
            if ($this->in($question['accept'], $this->payload()->toArray())) {
                if ($this->call($question['callback'], [$this->payload()]) === false) {
                    // if we not accept this answer, reqeustion
                    Session::set('litegram:question', $question);
                }
            }

            // fallback
            else {
                $this->call($question['fallback'], [$this->payload()]);
                Session::set('litegram:question', $question);
            }

            $this->skip();
        }
    }
}