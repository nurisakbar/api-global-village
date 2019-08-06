<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDeliveryDestination extends Model
{

    public function full_address()
    {
        return $this->belongsTo('App\Models\ViewRegion','village_id','village_id');
    }

    public $incrementing = false;
}
