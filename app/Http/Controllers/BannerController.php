<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use Illuminate\Http\Request;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\BannerTransformer;
//use App\Transformers\BannerDetailTransformer;
use App\Services\UploadService;

class BannerController extends Controller
{

    private $fractal;
    private $BannerTransformer;

    public function __construct(BannerTransformer $BannerTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->BannerTransformer = $BannerTransformer;
        $this->fractal = new Fractal\Manager();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banner = Banner::where('publish','y')->orderBy('created_at','DESC')->get();
        $banner = new Collection($banner, $this->BannerTransformer);
        $banner = $this->fractal->createData($banner); 
        $response = $banner->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'name'         =>  'required|unique:banners',
            'description'  =>  'required',
            'publish'      =>  'required',
            'image_web'    => 'required|mimes:jpeg,jpg,png',
            'image_mobile' => 'required|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        try {
            $input  = $request->all();
            $image_web              = $upload->image($request,'image_web','img_banner');
            $image_mobile           = $upload->image($request,'image_mobile','img_banner');
            $input['slug']          = $request->name;
            $input['image_web']     = $image_web['data']['file_name'];
            $input['image_mobile']  = $image_mobile['data']['file_name'];
            $createBanner = Banner::create($input);

            $response['status']     = "success";
            $response['message']    = "input banner berhasil";
            $statusCode             = 201;
          
          } catch (\Exception $e) {
            $response['status']     = "failed";
            $response['message']    = $e->getMessage();
            $statusCode             = 500;
          }

          return response()->json($response,$statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if($banner)
        {
            if (file_exists(public_path('/img_banner/'.$banner->image_web))) {
                unlink(public_path('/img_banner/'.$banner->web));
            }
            if (file_exists(public_path('/img_banner/'.$banner->image_mobile))) {
                unlink(public_path('/img_banner/'.$banner->image_mobile));
            }
            $banner->delete();
            $response['status'] = "success";
            $response['message'] = "Berhasil Menghapus Banner";
        }else
        {
            $response['status'] = "failed";
            $response['message'] = "data not found";
        }
    }
}
