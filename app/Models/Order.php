<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->format('d F Y');
    }

    public function item()
    {
        return $this->hasOne('App\Models\OrderItem');
    }

    public function purchaseItem()
    {
        return $this->hasMany('App\Models\OrderItemView','order_id')->select('id','name','image','qty','price','subtotal')->orderBy('created_at','DESC');
    }

    public function seller()
    {
        return $this->belongsTo('App\Models\User','user_id_seller');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\User','user_id_buyer');
    }
    


    public $incrementing = false;

}
