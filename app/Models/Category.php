<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=['name','entity','publish','image_web','image_mobile'];

    public function getImageWebAttribute($value)
    {
        return secure_asset('img_category/'.$value);
    }

    public function getImageMobileAttribute($value)
    {
        return secure_asset('img_category/'.$value);
    }
}
