<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Land;
use App\Services\UploadService;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\LandTransformer;
use App\Transformers\LandDetailTransformer;
class LandController extends Controller
{
    private $fractal;
    private $LandTransformer;
    private $LandDetailTransformer;

    public function __construct(LandDetailTransformer $LandDetailTransformer,LandTransformer $LandTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->LandTransformer = $LandTransformer;
        $this->LandDetailTransformer = $LandDetailTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function store(Request $request, UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'token'          =>  'required',
            'large'         =>  'required:integer',
            'name'          =>  'required',
            'description'   =>  'required',
            'image_1'       =>  'required',
            'unit_area'     =>  'required',
            'address'       =>  'required',
            'village_id'    =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        
        if(isset($user))
        {
            $upload_image1  = $upload->image($request,'image_1','img_land');
            $image1         = $upload_image1['data']['file_name'];
            
           
            if(isset($request->image_2))
            {
                $upload_image2  = $upload->image($request,'image_2','img_land');
                $image2         = $upload_image2['data']['file_name']!=null?$upload_image2['data']['file_name']:null;
            }else
            {
                $image2 = null;
            }

            if(isset($request->image_3))
            {
                $upload_image3  = $upload->image($request,'image_3','img_land');
                $image3         = $upload_image2['data']['file_name']!=null?$upload_image3['data']['file_name']:null;
            }else
            {
                $image3 = null;
            }

            if(isset($request->image_4))
            {
                $upload_image4  = $upload->image($request,'image_4','img_land');
                $image4         = $upload_image2['data']['file_name']!=null?$upload_image4['data']['file_name']:null;
            }else
            {
                $image4 = null;
            }

            $input                  = $request->only('name','large','description','unit_area','address','village_id');
            $input['image_1']       = $image1;
            $input['image_2']       = $image2;
            $input['image_3']       = $image3;
            $input['image_4']       = $image4;
            $input['slug']          = $request->name;
            $input['user_id']       = $user->id;
            $land                   = Land::create($input);
            $response['message']    = "Berhasil Menambahkan Lahan";
            $response['status']     = true;
            $statusCode             = 200;

        }else
        {
            $response['message']    = "id user atau id lahan invalid";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,$statusCode);
    }

    public function show($id)
    {
        $land = Land::find($id);
        if($land==null)
        {
            $response['message']    = "id user atau id artikel tidak valid";
            $response['status']     = false;
            $statusCode             = 401;
        }else
        {
            $land                   = new Item($land, $this->LandDetailTransformer);
            $land                   = $this->fractal->createData($land); 
            $response               = $land->toArray();
            $response['success']    = true;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    public function destroy($id)
    {
        $land = Land::find($id);
        if($land!=null)
        {
            // mengapus file gambar kebun
            if ($land->image_1!='' and file_exists(public_path('/img_land/'.$land->image_1))) {
                unlink(public_path('/img_land/'.$land->image_1));
            }
            if ($land->image_2!='' and file_exists(public_path('/img_land/'.$land->image_2))) {
                unlink(public_path('/img_land/'.$land->image_2));
            }
            if ($land->image_3!='' and file_exists(public_path('/img_land/'.$land->image_3))) {
                unlink(public_path('/img_land/'.$land->image_3));
            }
            if ($land->image_4!='' and file_exists(public_path('/img_land/'.$land->image_4))) {
                unlink(public_path('/img_land/'.$land->image_4));
            }

            $land->delete();
            $response['message']    = "Berhasil Menghapus Kebun";
            $response['status']     = true;
            $statusCode             = 201;
        }else
        {
            $response['message']    = "Data Kebun Tidak Ditemukan";
            $response['status']     = false;
            $statusCode             = 401;
        }
        return response()->json($response,$statusCode);
    }

    public function update($id,Request $request,UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            //'id'            =>  'required',
            'large'         =>  'required:integer',
            'name'          =>  'required',
            'description'   =>  'required',
            'unit_area'     =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $land = Land::find($id);
        if($land==null)
        {
            $response['message']    = "Data Kebun Tidak Ditemukan";
            $response['status']     = false;
            $statusCode             = 401;
        }else
        {
            $input          = $request->all();
            $input['slug']  = $request->name;


            if ($request->hasFile('image_1')) {
                $upload_image1      = $upload->image($request,'image_1','img_land');
                $input['image_1']   = $upload_image1['data']['file_name'];
                $this->deleteImage($land->image_1);
            }

            if ($request->hasFile('image_2')) {
                $upload_image2      = $upload->image($request,'image_2','img_land');
                $input['image_2']  = $upload_image2['data']['file_name'];
                $this->deleteImage($land->image_2);
            }

            if ($request->hasFile('image_3')) {
                $upload_image3     = $upload->image($request,'image_3','img_land');
                $input['image_3'] = $upload_image3['data']['file_name'];
                $this->deleteImage($land->image_3);
            }

            if ($request->hasFile('image_4')) {
                $upload_image4      = $upload->image($request,'image_4','img_land');
                $input['image_4']  = $upload_image4['data']['file_name'];
                $this->deleteImage($land->image_4);
            }
            $land->update($input);
            $response['message']    = "Perubahan Tersimpan";
            $response['status']     = true;
            $statusCode             = 201;
        }
        return response()->json($response,$statusCode);
    }


    public function deleteImage($file)
    {
        //return $file;
        if (file_exists(public_path('/img_land/'.$file))) {
            unlink(public_path('/img_land/'.$file));
        }
    }

}
