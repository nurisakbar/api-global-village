<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    
    protected $fillable=['id','name','price','category_id','description','slug','user_id','unit_id','publish','weight','view','stock','village_id','image_1','image_2','image_3','image_4'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }
    



    function category()
    {
        return $this->belongsTo('App\Models\Category')->select('id','name');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    function unit()
    {
        return $this->belongsTo('App\Models\Unit')->select('name','id');
    }

    function comments($limit=4)
    {
        return $this->hasMany('App\Models\ProductComment')
        ->select('id','comment','name','created_at','photo')
        ->where('comment_id',null)
        ->orderBy('created_at','DESC');
        // /->limit($limit);
    }

    protected $dates = ['deleted_at'];
    public $incrementing = false;
}
