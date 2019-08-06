<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemView extends Model
{
    protected $table="order_item_view";


    public function getImageAttribute($value)
    {

        return secure_asset('img_product/'.$value);
    }

    public $incrementing = false;
}
