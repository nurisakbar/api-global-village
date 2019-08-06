<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Land extends Model
{
    use SoftDeletes;

    protected $fillable=['name','large','description','image_1','image_2','image_3','image_4','user_id','slug','address','village_id'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    protected $dates = ['deleted_at'];
}
