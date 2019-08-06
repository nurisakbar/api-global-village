<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Photo extends Model
{
    use Uuid;

    protected $visible = ['id', 'file_name'];

    public function getFileNameAttribute($value)
    {

        return asset('img_product/'.$value);
    }

    public $incrementing = false;
}
