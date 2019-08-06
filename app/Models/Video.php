<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable=['title','description','slug','file','category_id','img_thumbnail','view','like','dislike','tags'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }
    
    public function comments()
    {
        return $this->hasMany('App\Models\VideoComment')->select('id','comment','name','created_at','photo')->orderBy('created_at','DESC');
    }


    function category()
    {
        return $this->belongsTo('App\Models\Category')->select('id','name');
    }
}
