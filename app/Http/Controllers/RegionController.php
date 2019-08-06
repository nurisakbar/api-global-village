<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\VillageTransformer;


class RegionController extends Controller
{
    private $fractal;
    private $VillageTransformer;

    public function __construct(VillageTransformer $VillageTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->VillageTransformer = $VillageTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function province()
    {
        $response['data']       = Province::orderBy('name','ASC')->get();
        $response['status']     = "success";
        return response()->json($response,200);
    }

    public function regency($id)
    {
        $response['data']       = Regency::where('province_id',$id)->orderBy('name','ASC')->get();
        $response['status']     = "success";
        return response()->json($response,200);
    }

    public function district($id)
    {
        $response['data']       = District::where('regency_id',$id)->orderBy('name','ASC')->get();
        $response['status']     = "success";
        return response()->json($response,200);
    }

    public function villages($id)
    {
        $response['data']       = \DB::table('view_region')->where('district_id',$id)->orderBy('village_name','ASC')->get();
        //$response['data']       = Village::where('district_id',$id)->orderBy('name','ASC')->get();
        $response['status']     = "success";
        return response()->json($response,200);
    }

    public function villageDetail($id)
    {
 
        // $response['data']       = \DB::table('view_region')->where('village_id',$id)->first();
        // $response['status']     = "success";
        // return response()->json($response,200);

        $village = Village::find($id);
        $village = new Item($village, $this->VillageTransformer);
        $village = $this->fractal->createData($village); 
        $response = $village->toArray();
        $response['success'] = true;
        $response['status']  = 200;

        return response()->json($response,$response['status']);
    }


    // create view view_region as 
    // select v.id as village_id,d.id as district_id,r.id as regency_id,
    // p.id as province_id,v.name as village_name,d.name as district_name,
    // r.name as regency_name,p.name as province_name
    // from villages as v
    // join districts as d on v.district_id=d.id
    // join regencies as r on d.regency_id=r.id
    // join provinces as p on r.province_id=p.id
}
