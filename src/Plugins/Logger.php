<?php

namespace Litegram\Plugins;

use Litegram\Payload;
use Pastly\Client;
use Pastly\Expiration;
use Pastly\Types\Paste;
use stdClass;

class Logger extends AbstractPlugin
{
    /**
     * @var string
     */
    public static $alias = 'logger';

    /**
     * @var Client
     */
    private static $pastly;

    /**
     * @var string
     */
    private static $token;
    /**
     * @return void
     */
    public static function boot(): void
    {
        self::$pastly = new Client;
        self::$token = self::$config->get('plugins.logger.pastly.token');
    }

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        if (self::$config->get('plugins.logger.payload_log')) {
            self::log(self::$payload->toArray(), 'payload', 'auto');
        }

        if (self::$config->get('plugins.logger.collect_messages') && self::$bot->isPluginExists('database')) {
            self::collectMessages();
        }
    }

    private static function collectMessages()
    {
        if (!self::$bot->hasPayload()) {
            return;
        }

        $type = null;
        $text = null;
        $fileId = null;

        if (Payload::isMessage() || Payload::isEditedMessage() || Payload::isCommand()) {
            $type = 'message';
            $text = Payload::get('*.text');
        }

        if (Payload::isInlineQuery()) {
            $type = 'inline';
            $text = Payload::get('inline_query.query');
        }

        if (Payload::isCallbackQuery()) {
            $type = 'callback';
            $text = Payload::get('callback_query.data');
        }

        if (Payload::isChannelPost() || Payload::isEditedChannelPost()) {
            $type = 'post';
            $text = Payload::get('*.text');
        }

        if (Payload::isPhoto()) {
            $type = 'photo';

            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.photo');
            $fileId = end($media)['file_id'] ?? null;
        }

        if (Payload::isAudio()) {
            $type = 'audio';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.audio');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isVideo()) {
            $type = 'video';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.video');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isVideoNote()) {
            $type = 'videonote';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.video_note');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isVoice()) {
            $type = 'voice';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.voice');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isDocument()) {
            $type = 'document';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.document');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isAnimation()) {
            $type = 'gif';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.animation');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isSticker()) {
            $type = 'sticker';
            $text = Payload::get('*.caption');

            $media = (array) Payload::get('*.sticker');
            $fileId = $media['file_id'] ?? null;
        }

        if (Payload::isContact()) {
            $type = 'contact';
            $phone = Payload::get('*.contact.phone_number');
            $fname = Payload::get('*.contact.first_name');
            $lname = Payload::get('*.contact.last_name');
            $text = "{$phone} - {$fname} {$lname}";
        }

        if (Payload::isLocation()) {
            $type = 'location';
            $longitude = Payload::get('*.location.longitude');
            $latitude = Payload::get('*.location.latitude');
            $accuracy = Payload::get('*.location.horizontal_accuracy');
            $text = "{$longitude}, {$latitude} {$accuracy}";
        }

        if (Payload::isVenue()) {
            $type = 'venue';
            $title = Payload::get('*.venue.title');
            $address = Payload::get('*.venue.address');
            $text = "{$title} - {$address}";
        }

        if (Payload::isDice()) {
            $type = 'dice';
            $emoji = Payload::get('*.dice.emoji');
            $value = Payload::get('*.dice.value');
            $text = "{$emoji}: {$value}";
        }

        $insert = [
            'date' => time(),
            'user_id' => self::$bot->payload('*.from.id'),
            'type' => $type,
            'text' => trim($text),
            'file_id' => $fileId,
        ];

        Database::table('messages')->insert($insert);
    }

    /**
     * Put data to log file.
     *
     * @param string|array|stdClass $text
     * @param string $type
     * @param string $postfix
     * @return void
     */
    public static function log($text, $type = 'auto', $postfix = 'bot')
    {
        $currentYear = date('Y');
        $currentMonth = date('F');

        $path = self::$config->get('plugins.logger.path');
        $path = "{$path}/{$currentYear}/{$currentMonth}";

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $data = is_array($text) ? json_encode($text, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : trim($text);
        $date = date("d.m.Y, H:i:s");
        $log = "[{$date}] [{$type}]\n{$data}";

        $filename = date("Y-m-d") . "_{$postfix}.log";

        file_put_contents($path . "/{$filename}", $log . PHP_EOL, FILE_APPEND);
    }

    /**
     * Create cloud based log (Pastly).
     *
     * @param string|array|stdClass $text
     * @param array $extra
     * @return Paste
     */
    public static function upload($text, $extra = [])
    {
        $text = is_array($text) || is_object($text)
            ? json_encode(
                $text,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE
            )
            : $text;

        $response = self::$pastly->create(self::$token, $text, array_merge([
            'title' => self::$config->get('plugins.logger.pastly.title', 'Litegram Log'),
            'syntax' => self::$config->get('plugins.logger.pastly.syntax'),
            'slug' => null,
            'type' => self::$config->get('plugins.logger.pastly.type', 'private'),
            'password' => self::$config->get('plugins.logger.pastly.password'),
            'expiration' => Expiration::NEVER,
        ], $extra));

        return $response;
    }
}
