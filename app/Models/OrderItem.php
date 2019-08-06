<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable=['user_id','product_id','qty'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withTrashed();
    }

    public $incrementing = false;
}
