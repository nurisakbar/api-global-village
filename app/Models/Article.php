<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    public $fillable = ['id','tags','title','category_id','article','image','publish','view','slug'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }
    
    function category()
    {
        return $this->belongsTo('App\Models\Category')->select('id','name');
    }

    function comments()
    {
        return $this->hasMany('App\Models\ArticleComment')->select('id','comment','name','photo','created_at')->orderBy('created_at','DESC');
    }
}
