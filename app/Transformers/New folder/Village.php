<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    //

    public function district()
    {
        return $this->belongsTo('App\District');
    }

    public function potentials()
    {
        return $this->belongsToMany('App\Potential','village_potentials');
    }
}
