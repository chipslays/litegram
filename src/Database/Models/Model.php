<?php

namespace Litegram\Database\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    public function page(int $count = 15, int $page = null)
    {
        return $this->paginate($count, ['*'], 'page', $page);
    }
}