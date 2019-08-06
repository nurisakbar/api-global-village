<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function regency()
    {
        return $this->belongsTo('App\Models\Regency')->withDefault(['data'=>null]);
    }
}
