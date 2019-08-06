<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Harvest;

use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;

use App\Transformers\HarvestTransformer;
use App\Services\UploadService;
use Ramsey\Uuid\Uuid;
use App\HarvestOfferView;
use App\Transformers\HarvestOfferTransformer;
class HarvestController extends Controller
{
    private $fractal;
    private $HarvestTransformer;
    
    public function __construct(HarvestTransformer $HarvestTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->HarvestTransformer = $HarvestTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function index()
    {
        $limit      = Input::get('limit');
        $start      = Input::get('start');

        if($limit==null and $start==null)
        {
            $Harvests = Harvest::orderBy('created_at','DESC')->get();
        }
        else
        {

            $errors = [];

            if($limit==null)
            {
                array_push($errors,['message'=>'parameter limit tidak boleh kosong']);
            }

            if($start==null)
            {
                array_push($errors,['message'=>'parameter start tidak boleh kosong']);
            }

            $Harvests = Harvest::orderBy('created_at','DESC')->take($limit)->skip($start)->get();
        }

        
                

        $Harvests = new Collection($Harvests, $this->HarvestTransformer);
        
        $Harvests = $this->fractal->createData($Harvests); 
        $response = $Harvests->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function category($id,$start,$limit)
    {
       
        if($limit==null and $start==null)
        {
            $Harvests = Harvest::orderBy('created_at','DESC')->where('category_id',$id)->get();
            
        }
        else
        {

            $errors = [];

            if($limit==null)
            {
                array_push($errors,['message'=>'parameter limit tidak boleh kosong']);
            }

            if($start==null)
            {
                array_push($errors,['message'=>'parameter start tidak boleh kosong']);
            }
      
            $Harvests = Harvest::orderBy('created_at','DESC')->where('category_id',$id)->take($limit)->skip($start)->get();
        }

        $Harvests = new Collection($Harvests, $this->HarvestTransformer);
        $Harvests = $this->fractal->createData($Harvests); 
        $response = $Harvests->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }


    public function show($id)
    {
        $Harvest = Harvest::find($id);
        if($Harvest!=null)
        {
            // counter view
            $Harvest->view  = $Harvest->view+1;
            $Harvest->update();
            
            
            $Harvest = new Item($Harvest, $this->HarvestTransformer);
            $Harvest = $this->fractal->createData($Harvest); 
            $response = $Harvest->toArray();
            $response['success'] = true;
            $response['status']  = 200;
        }else
        {
            $response['success'] = false;
            $response['status']  = 404;
        }
        return response()->json($response,$response['status']);
    }
    
    
    public function search()
    {
        $keyword    = Input::get('keyword');
        $start      = Input::get('start');
        $limit      = Input::get('limit');

        $errors = [];

        if($keyword==null)
        {
            array_push($errors,['message'=>'Parameter Keyword Tidak Boleh kosong']);
        }

        if($start==null)
        {
            array_push($errors,['message'=>'Parameter Start Tidak Boleh kosong']);
        }

        if($limit==null)
        {
            array_push($errors,['message'=>'Parameter Limit Tidak Boleh kosong']);
        }

        if(count($errors)<1)
        {
            $Harvests  = Harvest::skip($start-1)->take($limit)->where('title','like',"%$keyword%")->orderBy('created_at')->get(); 
            $Harvests = new Collection($Harvests, $this->HarvestTransformer);
            $Harvests = $this->fractal->createData($Harvests); 
            $response = $Harvests->toArray();
            $response['success'] = true;
            $response['status']  = 200;
            return response()->json($response,200);
        }else
        {
            return response()->json($errors,200);
        }
    }

    public function comment($id)
    {
        $harvest = Harvest::find($id);
        if($harvest==null)
        {
            $response['success'] = false;
            $response['status']  = 401;
        }else
        {
            $response['success'] = true;
            $response['status']  = 200;
            //$comments = $harvest->comments;
            $response['data'] = $harvest->comments;
        }

        return response()->json($response,200);
    }

    public function related($id,$limit=null)
    {
        $limit = $limit==null?5:$limit;
        $harvest = Harvest::find($id);
        $keywords = explode(" ",$harvest->title);
        $Harvest = Harvest::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
               $query->orWhere('title', 'like', "%".$keyword."%");
            }
        })->where('id','!=',$id)->limit($limit)->get();

        $Harvests = new Collection($Harvest, $this->HarvestTransformer);
        $Harvests = $this->fractal->createData($Harvests); 
        $response = $Harvests->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function store(Request $request,UploadService $upload)
    {
        $validator              = \Validator::make($request->all(), [
            'title'             =>  'required',
            'description'       =>  'required',
            'token'             => 'required',
            'land_id'           => 'required',
            'category_id'       =>  'required',
            'estimated_date'    =>  'required',
            'estimated_income'  =>'required',
            'unit_id'           => 'required',
            'image_1'           => 'required|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        if($user!=null)
        {

            $input    = $request->all();

            if ($request->hasFile('image_1')) {
                $upload_image1      = $upload->image($request,'image_1','img_harvest');
                $input['image_1']  = $upload_image1['data']['file_name'];
            }

            if ($request->hasFile('image_2')) {
                $upload_image2      = $upload->image($request,'image_2','img_harvest');
                $input['image_2']  = $upload_image2['data']['file_name'];
            }

            if ($request->hasFile('image_3')) {
                $upload_image3     = $upload->image($request,'image_3','img_harvest');
                $input['image_3'] = $upload_image3['data']['file_name'];
            }

            if ($request->hasFile('image_4')) {
                $upload_image4      = $upload->image($request,'image_4','img_harvest');
                $input['image_4']  = $upload_image4['data']['file_name'];
            }

            $input['view']          = 0;
            $input['id']            = Uuid::uuid4();
            $input['slug']          = $request->title;
            $input['user_id']       = $user->id;
            $Harvest                = Harvest::create($input);

            // set response
            $response['status']     = "success";
            $response['message']    = 'Info Panen Berhasil Ditambahkan';
            $statusCode             = 201;
        }else
        {
            $response['message']    = "user tidak sah";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,201);
    }

    public function update($id,Request $request,UploadService $upload)
    {
        $validator              = \Validator::make($request->all(), [
            'title'             =>  'required',
            'description'       =>  'required',
            'land_id'           => 'required',
            'category_id'       =>  'required',
            'estimated_date'    =>  'required',
            'estimated_income'  =>'required',
            'unit_id'           => 'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $Harvest = Harvest::find($id);
        if($Harvest==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $input    = $request->all();

            if ($request->hasFile('image_1')) {
                $upload_image1      = $upload->image($request,'image_1','img_harvest');
                $input['image_1']  = $upload_image1['data']['file_name'];
                $this->deleteImage($Harvest->image_1);
            }

            if ($request->hasFile('image_2')) {
                $upload_image2      = $upload->image($request,'image_2','img_harvest');
                $input['image_2']  = $upload_image2['data']['file_name'];
                $this->deleteImage($Harvest->image_2);
            }

            if ($request->hasFile('image_3')) {
                $upload_image3     = $upload->image($request,'image_3','img_harvest');
                $input['image_3'] = $upload_image3['data']['file_name'];
                $this->deleteImage($Harvest->image_3);
            }

            if ($request->hasFile('image_4')) {
                $upload_image4      = $upload->image($request,'image_4','img_harvest');
                $input['image_4']  = $upload_image4['data']['file_name'];
                $this->deleteImage($Harvest->image_4);
            }
 
            $HarvestUpdate          = $Harvest->update($input);
            $response['status']     ='success';
            $response['message']    = "A Harvest With Name ".$Harvest->title." Has Updated";
            $response['data']       = $Harvest;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    public function deleteImage($file)
    {
        //return $file;
        if (file_exists(public_path('/img_harvest/'.$file))) {
            unlink(public_path('/img_harvest/'.$file));
        }
    }



    public function delete($id)
    {
        $Harvest = Harvest::find($id);
        if($Harvest==null)
        {
            $response['status']     = "failed";
            $response['message']    = "Resource Not Found";
            $statusCode             = 401;
        }else
        {
            
            if($Harvest->image_1!=null)
            {
                $this->deleteImage($Harvest->image_1);
            }
            if($Harvest->image_2!=null)
            {
                $this->deleteImage($Harvest->image_2);
            }
            if($Harvest->image_3!=null)
            {
                $this->deleteImage($Harvest->image_3);
            }
            if($Harvest->image_4!=null)
            {
                $this->deleteImage($Harvest->image_4);
            }

            $Harvest->delete();
            $response['status']     ='success';
            $response['message']    = "Berhasil Menghapus Panen";
            $statusCode             = 200;
        }

        return response()->json($response,$statusCode);
    }


 


    public function deletePhoto($id,$field)
    {
        $harvest = Harvest::find($id);

        if($harvest!=null)
        {
            $this->deleteImage($harvest->$field);
            $harvest->$field = null;
            $harvest->save();
            
            $response['message']    = "Berhasil Menghapus Foto";
            $response['status']     = true;
            $statusCode             = 201;
        }
        else
        {
            $response['message']    = "invalid id";
            $response['status']     = false;
            $statusCode             = 400;
        }
        return response()->json($response,$statusCode);
    }


    public function createOffer(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'harvest_id'    =>  'required',
            'token'         =>  'required',
            'price'         =>  'required|integer',
            'qty'           =>  'required|integer',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')
                        ->select('id')
                        ->where('api_token',$request->token)
                        ->first();
        $harvest    = \DB::table('harvests')
                        ->select('id')
                        ->where('id',$request->harvest_id)
                        ->first();

        if($user==null or $harvest==null)
        {
            $response['message']    = "id panen atau token tidak ditemukan";
            $response['status']     = false;
            $statusCode             = 401;
        }else
        {
            $harvestOffer = [
                'id'            =>  Uuid::uuid4(),
                'qty'           =>  $request->qty,
                'note'          =>  $request->note,
                'price'         =>  $request->price,
                'user_id'       =>  $user->id,
                'harvest_id'    =>  $harvest->id,
                'created_at'    =>  date('Y-m-d H:i:s'),
                'updated_at'    =>  date('Y-m-d H:i:s')
            ];
            
            \DB::table('harvest_offers')->insert($harvestOffer);

            $response['message']    = "Berhasil Membuat Penawaran";
            $response['status']     = true;
            $statusCode             = 201;
        }
        return response()->json($response,$statusCode);
    }

    public function getOffer($actor,$token,HarvestOfferTransformer $HarvestOfferTransformer)
    {
        $user       = \DB::table('users')
                        ->select('id')
                        ->where('api_token',$token)
                        ->first();

        if($user==null)
        {
            $response['message']    = "id panen atau token tidak ditemukan";
            $response['status']     = false;
            $statusCode             = 401;
        }else
        {
            if($actor=='penawar')
            {
                $harvestOffer = HarvestOfferView::where('user_id_offer',$user->id)->get();
                //return $harvestOffer;
                $harvestOffer           = new Collection($harvestOffer, $HarvestOfferTransformer);
                $harvestOffer           = $this->fractal->createData($harvestOffer); 
                $response               = $harvestOffer->toArray();
                $response['success']    = true;
                $statusCode             = 200;

            }else
            {
                $harvestOffer = HarvestOfferView::where('user_id_owner',$user->id)->get();
                return $harvestOffer;
            }
        }

        return response()->json($response,$statusCode);              
    }

    
}
