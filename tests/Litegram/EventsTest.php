<?php

use Litegram\Debug\Update;
use PHPUnit\Framework\TestCase;

final class EventsTest extends TestCase
{
    public function testOnByDot()
    {
        bot()->webhook(Update::START);

        bot()->on('message.text', function () {
            echo 'catch';
        });

        ob_start();
        bot()->run();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('catch', $output);
    }
}