<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    public function potentials()
    {
        return $this->belongsToMany('App\Models\Potential');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District')->withDefault(['data'=>null]);
    }
}
