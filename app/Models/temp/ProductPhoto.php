<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    public function getFileNameAttribute($value)
    {

        return asset('img_product/'.$value);
    }
    
    public $incrementing = false;
}
