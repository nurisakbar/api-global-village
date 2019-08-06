<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Product;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\ProductTransformer;
use App\Services\UploadService;

class ProductController extends Controller
{
    private $fractal;
    private $ProductTransformer;

    public function __construct(ProductTransformer $ProductTransformer, Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->ProductTransformer = $ProductTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function index()
    {
        $limit      = Input::get('limit');
        $start      = Input::get('start');

        if($limit==null and $start==null)
        {
            $Products = Product::orderBy('created_at','DESC')->get();
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

            $Products = Product::orderBy('created_at','DESC')->take($limit)->skip($start)->get();
        }
                
        $Products = new Collection($Products, $this->ProductTransformer);
        $Products = $this->fractal->createData($Products); 
        $response = $Products->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function show($id)
    {
        $Product = Product::find($id);
        if($Product!=null)
        {
            // counter view
            $Product->view  = $Product->view+1;
            $Product->update();
            
            
            $Product = new Item($Product, $this->ProductTransformer);
            $Product = $this->fractal->createData($Product); 
            $response = $Product->toArray();
            $response['success'] = true;
            $response['status']  = 200;
        }else
        {
            $response['success'] = false;
            $response['status']  = 404;
        }
        return response()->json($response,$response['status']);
    }


    public function byRegion($regencyId)
    {
        $regency = \DB::table('regencies')->where('id',$regencyId)->first();

        $products = ProductView::where('regency_id',$regencyId)->get();
        $countProduct = $products->count();
        $products = new Collection($products, $this->ProductViewTransformer);
        $products = $this->fractal->createData($products); 
        $response = $products->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        $response['message'] = "Terdapat $countProduct Product Pada Kabupaten".$regency->name;
        return response()->json($response,200);
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
            $Products  = Product::skip($start-1)->take($limit)->where('name','like',"%$keyword%")->orderBy('created_at')->get(); 
            $Products = new Collection($Products, $this->ProductTransformer);
            $Products = $this->fractal->createData($Products); 
            $response = $Products->toArray();
            $response['success'] = true;
            $response['status']  = 200;
            return response()->json($response,200);
        }else
        {
            return response()->json($errors,200);
        }
    }

    public function category($id,$start=null,$limit=null)
    {
        $category = \DB::table('categories')->where('id',$id)->first();

        if(isset($category))
        {

            // filter atau tidak

            if(!isset($start) and !isset($limit))
            {
                $Products = Product::where('category_id',$id)->orderBy('created_at','DESC')->get();
            }else
            {
                // filter 
                $errors     = [];
                if(!isset($start))
                {
                    array_push($errors,['message'=>'Parameter Start Tidak Boleh kosong']);
                }

                if(!isset($limit))
                {
                    array_push($errors,['message'=>'Parameter Limit Tidak Boleh kosong']);
                }

                if(count($errors)>0)
                {
                    return $errors;
                }else
                {
                    $Products = Product::where('category_id',$id)->skip($start)->take($limit)->orderBy('created_at','DESC')->get();
                }
            }

            
            $Products = new Collection($Products, $this->ProductTransformer);
            $Products = $this->fractal->createData($Products); 
            $response = $Products->toArray();
            $response['success'] = true;
            $response['status']  = 200;
        }else
        {
            $response['message'] = "undefine category";
            $response['success'] = true;
            $response['status']  = 200;
        }

        return response()->json($response,200);
    }



    public function related($id,$limit=null)
    {
        $limit = $limit==null?5:$limit;
        $product = Product::find($id);
        $keywords = explode(" ",$product->name);
        $products = Product::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
               $query->orWhere('name', 'like', "%".$keyword."%");
            }
        })->where('id','!=',$id)->limit($limit)->get();

        $products = new Collection($products, $this->ProductTransformer);
        $products = $this->fractal->createData($products); 
        $response = $products->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function getComment($id,$limit=null)
    {
        $limit = $limit==null?5:$limit;
        $product = Product::find($id);
        if($product!=null)
        {
            $response['success'] = true;
            $response['status']  = 200;
            $response['data'] = $product->comments;
        }else
        {
            $response['success'] = false;
            $response['status']  = 401;
            $response['message'] = "undefine product id";
        }
        return response()->json($response,$response['status']);
    }

    public function store(Request $request,UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'name'          =>  'required',
            'description'   =>  'required',
            'category_id'   =>  'required',
            'token'         =>  'required',
            'stock'         =>  'required|integer',
            'price'         =>  'required',
            'unit_id'       =>  'required',
            'weight'        =>  'required',
            'image_1'         =>  'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user = \DB::table('users')->where('api_token',$request->token)->first();
        if($user->village_id==null)
        {
            $response['status']     = "failed";
            $response['message']    = 'silahkan lengkapi profile anda';
        }
        elseif(isset($user))
        {
            $upload_image1  = $upload->image($request,'image_1','img_product');
            $image1         = $upload_image1['data']['file_name'];
            
           
            if(isset($request->image_2))
            {
                $upload_image2  = $upload->image($request,'image_2','img_product');
                $image2         = $upload_image2['data']['file_name']!=null?$upload_image2['data']['file_name']:null;
            }else
            {
                $image2 = null;
            }

            if(isset($request->image_3))
            {
                $upload_image3  = $upload->image($request,'image_3','img_product');
                $image3         = $upload_image2['data']['file_name']!=null?$upload_image3['data']['file_name']:null;
            }else
            {
                $image3 = null;
            }

            if(isset($request->image_4))
            {
                $upload_image4  = $upload->image($request,'image_4','img_product');
                $image4         = $upload_image2['data']['file_name']!=null?$upload_image4['data']['file_name']:null;
            }else
            {
                $image4 = null;
            }
            

            $input                  = $request->all();
            $input['view']          =   0;
            $input['stock']         = $request->stock;
            $input['slug']          = $request->name;
            $input['user_id']       = $user->id;
            $input['image_1']       = $image1;
            $input['image_2']       = $image2;
            $input['image_3']       = $image3;
            $input['image_4']       = $image4;
            $Product                = Product::create($input);
            $response['data']       = $input;
            $response['data']['id'] = $Product->id;
            $response['status']     = "success";
            $response['message']    = 'Berhasil Menambahkan Produk';
        }else
        {
            $response['status']     = "failed";
            $response['message']    = 'invalid token';
        }
            
        return response()->json($response,201);
    }

    public function update($id,Request $request,UploadService $upload)
    {

        $validator          = \Validator::make($request->all(), [
            'name'          =>  'required',
            'description'   =>  'required',
            'category_id'   =>  'required',
            'token'         =>  'required',
            'stock'         =>  'required|integer',
            'price'         =>  'required',
            'unit_id'       =>  'required',
            'weight'        =>  'required',
            //'image_1'         =>  'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $Product = Product::find($id);
        if($Product==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $input          = $request->all();
            $input['slug']  = $request->name;


            if ($request->hasFile('image_1')) {
                $upload_image1      = $upload->image($request,'image_1','img_product');
                $input['image_1']   = $upload_image1['data']['file_name'];
                $this->deleteImage($Product->image_1);
            }

            if ($request->hasFile('image_2')) {
                $upload_image2      = $upload->image($request,'image_2','img_product');
                $input['image_2']  = $upload_image2['data']['file_name'];
                $this->deleteImage($Product->image_2);
            }

            if ($request->hasFile('image_3')) {
                $upload_image3     = $upload->image($request,'image_3','img_product');
                $input['image_3'] = $upload_image3['data']['file_name'];
                $this->deleteImage($Product->image_3);
            }

            if ($request->hasFile('image_4')) {
                $upload_image4      = $upload->image($request,'image_4','img_product');
                $input['image_4']  = $upload_image4['data']['file_name'];
                $this->deleteImage($Product->image_4);
            }
            

            $ProductUpdate          = $Product->update($input);
            $response['status']     = 'success';
            $response['message']    = "Update Produk Berhasil";
            //$response['data']       = $Product;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }


    public function uploadLeration()
    {
          // =================== upload images ==============================
          $photo =    [];

          $upload_image1  = $upload->image($request,'image_1','img_product');
          $image1         = $upload_image1['data']['file_name'];

          $photo[] = ['created_at'=>date('Y-m-d H:i:s'),'id'=>Uuid::uuid4(),'file_name'=>$image1,'product_id'=>$product_id];
          
         
          if(isset($request->image_2))
          {
              $upload_image2  = $upload->image($request,'image_2','img_product');
              $image2         = $upload_image2['data']['file_name']!=null?$upload_image2['data']['file_name']:null;
              $photo[] = ['created_at'=>date('Y-m-d H:i:s'),'id'=>Uuid::uuid4(),'file_name'=>$image2,'product_id'=>$product_id];
          }

          if(isset($request->image_3))
          {
              $upload_image3  = $upload->image($request,'image_3','img_product');
              $image3         = $upload_image2['data']['file_name']!=null?$upload_image3['data']['file_name']:null;
              $photo[] = ['created_at'=>date('Y-m-d H:i:s'),'id'=>Uuid::uuid4(),'file_name'=>$image3,'product_id'=>$product_id];
          }

          if(isset($request->image_4))
          {
              $upload_image4  = $upload->image($request,'image_4','img_product');
              $image4         = $upload_image2['data']['file_name']!=null?$upload_image4['data']['file_name']:null;
              $photo[] = ['created_at'=>date('Y-m-d H:i:s'),'id'=>Uuid::uuid4(),'file_name'=>$image4,'product_id'=>$product_id];
          }

          \DB::table('product_photos')->insert($photo);
    }

    public function deleteImage($file)
    {
        //return $file;
        if (file_exists(public_path('/img_product/'.$file))) {
            unlink(public_path('/img_product/'.$file));
        }
    }

    public function delete($id)
    {
        $Product = Product::find($id);
        if($Product==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $Product->delete();
            $response['status']     ='success';
            $response['message']    = "A Product With Title ".$Product->title." Has Deleted";
            $response['data']       = null;
            $statusCode             = 200;
            
            if (file_exists(public_path('/img_Product/'.$Product->image))) {
                unlink(public_path('/img_Product/'.$Product->image));
            }
        }
        return response()->json($response,$statusCode);
    }



}
