<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\CategoryTransformer;
use App\Services\UploadService;

class CategoryController extends Controller
{

    private $fractal;
    private $CategoryTransformer;

    public function __construct(CategoryTransformer $CategoryTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->CategoryTransformer = $CategoryTransformer;
        $this->fractal = new Fractal\Manager();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function pluck($entity=null)
    {
        if($entity==null)
        {
            $category       = Category::pluck('name','id');
        }else
        {
            $category       = Category::where('entity',$entity)->get();
        }
        $category = new Collection($category, $this->CategoryTransformer);
        $category = $this->fractal->createData($category); 
        $response = $category->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function index()
    {
        $category       = Category::orderBy('id','DESC')->get();
        $category = new Collection($category, $this->CategoryTransformer);
        $category = $this->fractal->createData($category); 
        $response = $category->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,UploadService $upload)
    {
       
        $validator = \Validator::make($request->all(), [
            'name'          => 'required|unique:categories',
            'image_web'     =>  'required|mimes:jpeg,jpg,png',
            'image_mobile'  =>  'required|mimes:jpeg,jpg,png',
            'entity'        =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

    
        $file_image_web     = $upload->image($request,'image_web','img_category');
        $file_image_mobile  = $upload->image($request,'image_mobile','img_category');

        $input                  = $request->all();
        $input['slug']          = $request->name;
        $input['image_web']     = $file_image_web['data']['file_name'];
        $input['image_mobile']  = $file_image_mobile['data']['file_name'];

        $category   = Category::create($input);
        $response['data']       = $input;
        $response['data']['id'] = $category->id;
        $response['status']     = "success";
        $response['message']    = 'A New Category With Name '.$request->name.' Has Created';
        return response()->json($response,201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if($category==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode = 404;
        }
        else
        {
            $response['status']     = "success";
            $response['data']       = $category;
            $response['message']    = null;
            $statusCode = 200;
        }

        return response()->json($response,$statusCode);

    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id,UploadService $upload)
    {
        $validator = \Validator::make($request->all(), [
            'name'      =>  'required',
            'entity'    =>  'required',
            'publish'   =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }


        $category = Category::findOrFail($id);
        if($category)
        {
            $category->name     = $request->name;
            $category->entity   = $request->entity;
            $category->publish  = $request->publish;

            if ($request->hasFile('image_web')) {
                $image_web              = $upload->image($request,'image_web','img_category');
                $category->image_web    = $image_web['data']['file_name'];
            }
            if ($request->hasFile('image_mobile')) {
                $image_mobile           = $upload->image($request,'image_mobile','img_category');
                $category->image_mobile = $image_mobile['data']['file_name'];
            }

            $category->update();

            $response['status']     =   'success';
            $response['message']    =   "A Category With Name ".$request->name." Has Updated";
            //$response['data']       =   ['id'=>$category->id,'name'=>$category->name];
        }else
        {
            $response['status']     =   'failed';
            $response['message']    =   'kategori tidak ditemukan';
        }
        
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if($category==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $category->delete();
            $response['status']     ='success';
            $response['message']    = "A Category With Name ".$category->name." Has Deleted";
            $response['data']       = null;
            $statusCode             = 200;

            if (file_exists(public_path('/img_category/'.$category->image_web))) {
                unlink(public_path('/img_category/'.$category->image_web));
            }
            if (file_exists(public_path('/img_category/'.$category->image_mobile))) {
                unlink(public_path('/img_category/'.$category->image_mobile));
            }

        }
        
        return response()->json($response,$statusCode);
    }
}
