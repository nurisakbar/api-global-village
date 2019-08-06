<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestPhoto extends Model
{
    public function getFileNameAttribute($value)
    {

        return secure_asset('img_harvest/'.$value);
    }

    public $incrementing = false;
}
