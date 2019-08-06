<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Harvest extends Model
{
    use SoftDeletes;

    protected $fillable=['id','title','slug','view','user_id','description','land_id','category_id','estimated_date','estimated_income','unit_id','image_1','image_2','image_3','image_4'];
    
    protected $dates = ['deleted_at'];
    
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    

    function category()
    {
        return $this->belongsTo('App\Models\Category')->select('id','name');
    }

    function unit()
    {
        return $this->belongsTo('App\Models\Unit')->select('name','id');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function photos()
    {
        return $this->hasMany('App\Models\HarvestPhoto')->select('id','file_name');
    }

    function comments()
    {
        return $this->hasMany('App\Models\HarvestComment')->select('id','comment','name','created_at','photo')->orderBy('created_at','DESC');
    }

    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->diffForHumans();
    }

    public function getEstimatedDateAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['estimated_date'])
        ->format('d F Y');
    }

    public function land()
    {
        return $this->belongsTo('App\Models\Land')->select('name','id','large','unit_area','image_1');
    }

    public $incrementing = false;
}
