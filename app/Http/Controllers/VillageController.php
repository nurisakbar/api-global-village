<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Village;

class VillageController extends Controller
{
    
    public function addPotential()
    {
        $village = Village::find(1114061008);
        //$village->potentials()->attach([1,2]);
        dd($village->potentials()->get());
    }
}
