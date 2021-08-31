<?php

namespace Litegram\Database\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = [
        'id',
        'fullname',
        'firstname',
        'lastname',
        'username',
        'locale',
        'phone',
        'nickname',
        'emoji',
        'role',
        'blocked',
        'banned',
        'ban_comment',
        'ban_start',
        'ban_end',
        'source',
        'version',
        'first_message',
        'last_message',
        'json',
        'note',
    ];
}