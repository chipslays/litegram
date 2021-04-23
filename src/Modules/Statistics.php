<?php

namespace Litegram\Modules;

use Litegram\Update;
use Litegram\Support\Util;
use Litegram\Modules\User;

class Statistics extends Module
{
    /**
     * @var string
     */
    public static $alias = 'statistics';

    /**
     * @var array
     */
    public static $depends = [
        'database',
        'user',
    ];

    /**
     * @return void
     */
    public static function afterRun(): void
    {
        $date = Util::midnight();

        // messages stats
        if (self::$bot->config('modules.statistics.messages')) {
            $isNewDate = Database::table('stats_messages')->where('date', $date)->count() == 0;
            if ($isNewDate) {
                Database::table('stats_messages')->insert([
                    'date' => $date,
                    'count' => 1,
                ]);
            } else {
                Database::table('stats_messages')->where('date', $date)->increment('count', 1);
            }
        }

        // new users stats
        if (self::$bot->config('modules.statistics.users') && self::$bot->config('modules.user.enable')) {
            $isNewDate = Database::table('stats_users')->where('date', $date)->count() == 0;
            if (User::firstTime()) {
                if ($isNewDate) {
                    Database::table('stats_users')->insert([
                        'date' => $date,
                        'count' => 1,
                    ]);
                } else {
                    Database::table('stats_users')->where('date', $date)->increment('count', 1);
                }
            } else {
                if ($isNewDate) {
                    Database::table('stats_users')->insert([
                        'date' => $date,
                        'count' => 0,
                    ]);
                }
            }
        }

        // incoming updates
        if (self::$bot->config('modules.statistics.updates')) {
            if (!self::$bot->hasUpdate()) {
                return;
            }

            $type = null;
            $text = null;
            $fileId = null;

            if (Update::isMessage() || Update::isEditedMessage() || Update::isCommand()) {
                $type = 'message';
                $text = Update::get('*.text');
            }

            if (Update::isInlineQuery()) {
                $type = 'inline';
                $text = Update::get('inline_query.query');
            }

            if (Update::isCallbackQuery()) {
                $type = 'callback';
                $text = Update::get('callback_query.data');
            }

            if (Update::isChannelPost() || Update::isEditedChannelPost()) {
                $type = 'post';
                $text = Update::get('*.text');
            }

            if (Update::isPhoto()) {
                $type = 'photo';

                $text = Update::get('*.caption');

                $media = (array) Update::get('*.photo');
                $fileId = end($media)['file_id'] ?? null;
            }

            if (Update::isAudio()) {
                $type = 'audio';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.audio');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isVideo()) {
                $type = 'video';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.video');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isVideoNote()) {
                $type = 'videonote';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.video_note');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isVoice()) {
                $type = 'voice';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.voice');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isDocument()) {
                $type = 'document';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.document');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isAnimation()) {
                $type = 'gif';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.animation');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isSticker()) {
                $type = 'sticker';
                $text = Update::get('*.caption');

                $media = (array) Update::get('*.sticker');
                $fileId = $media['file_id'] ?? null;
            }

            if (Update::isContact()) {
                $type = 'contact';
                $phone = Update::get('*.contact.phone_number');
                $fname = Update::get('*.contact.first_name');
                $lname = Update::get('*.contact.last_name');
                $text = "{$phone} - {$fname} {$lname}";
            }

            if (Update::isLocation()) {
                $type = 'location';
                $longitude = Update::get('*.location.longitude');
                $latitude = Update::get('*.location.latitude');
                $accuracy = Update::get('*.location.horizontal_accuracy');
                $text = "{$longitude}, {$latitude} {$accuracy}";
            }

            if (Update::isVenue()) {
                $type = 'venue';
                $title = Update::get('*.venue.title');
                $address = Update::get('*.venue.address');
                $text = "{$title} - {$address}";
            }

            if (Update::isDice()) {
                $type = 'dice';
                $emoji = Update::get('*.dice.emoji');
                $value = Update::get('*.dice.value');
                $text = "{$emoji}: {$value}";
            }

            $insert = [
                'date' => time(),
                'user_id' => self::$bot->update('*.from.id'),
                'type' => $type,
                'text' => $text,
                'file_id' => $fileId,
            ];

            Database::table('messages')->insert($insert);
        }
    }
}