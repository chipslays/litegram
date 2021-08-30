<?php

namespace Litegram\Database\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'data' => 'array',
    ];
}