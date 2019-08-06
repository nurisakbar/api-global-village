<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestOfferView extends Model
{
    protected $table='harvest_offer_view';


    function owner()
    {
        return $this->belongsTo('App\Models\User','user_id_owner')->select('id','name');
    }

    function offer()
    {
        return $this->belongsTo('App\Models\User','user_id_offer')->select('id','name');
    }

    function harvest()
    {
        return $this->belongsTo('App\Models\Harvest');
    }
}
