<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Article;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\ArticleTransformer;
//use App\Transformers\ArticleDetailTransformer;
use App\Services\UploadService;
use Ramsey\Uuid\Uuid;

class ArticleController extends Controller
{
    private $fractal;
    private $ArticleTransformer;
    //private $ArticleDetailTransformer;

    public function __construct(ArticleTransformer $ArticleTransformer,Fractal\Manager $fractal)
    {
        
        $this->middleware('AccessApi');
        $this->ArticleTransformer = $ArticleTransformer;
        //$this->ArticleDetailTransformer = $ArticleDetailTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function index()
    {
        $limit      = Input::get('limit');
        $start      = Input::get('start');

        if($limit==null and $start==null)
        {
            $articles = Article::orderBy('created_at','DESC')->get();
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

            $articles = Article::orderBy('created_at','DESC')->take($limit)->skip($start)->get();
        }
        
                

        $articles = new Collection($articles, $this->ArticleTransformer);
        $articles = $this->fractal->createData($articles); 
        $response = $articles->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function show($id)
    {
        $article = Article::find($id);
        if($article!=null)
        {
            // counter view
            $article->view  = $article->view+1;
            $article->update();
            
            
            $article = new Item($article, $this->ArticleTransformer);
            $article = $this->fractal->createData($article); 
            $response = $article->toArray();
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
            $articles  = Article::skip($start)->take($limit)->where('title','like',"%$keyword%")->orderBy('created_at')->get(); 
            $articles = new Collection($articles, $this->ArticleTransformer);
            $articles = $this->fractal->createData($articles); 
            $response = $articles->toArray();
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
        if(($start==null) or ($start==1))
        {
            $start=0;
        }
        $category = \DB::table('categories')->where('id',$id)->first();

        if(isset($category))
        {
            // filter atau tidak
            if(!isset($start) and !isset($limit))
            {
                $articles = Article::where('category_id',$id)->orderBy('created_at','DESC')->get();
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
                    $articles = Article::where('category_id',$id)->skip($start)->take($limit)->orderBy('created_at','DESC')->get();
                }
            }

            
            $articles = new Collection($articles, $this->ArticleTransformer);
            $articles = $this->fractal->createData($articles); 
            $response = $articles->toArray();
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

    public function popular($start=null,$limit=null,$idCategory=null)
    {
         $limit = isset($limit)?$limit:5;
        
        $start = isset($start)?$start:0;

        if(isset($idCategory))
        {
            $category = \DB::table('categories')->where('id',$idCategory)->first();
            $articles = Article::skip($start)->take($limit)->where('category_id',$idCategory)->orderBy('view','DESC')->get();
            $message = "Artikel terpopuler Pada Kategori ".$category->name;
        }else
        {
            $articles = Article::skip($start)->take($limit)->orderBy('view','DESC')->get();
            $message = "Artikel Terpopuler";
        }
        
        $articles = new Collection($articles, $this->ArticleTransformer);
        $articles = $this->fractal->createData($articles); 
        $response = $articles->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        $response['message'] = $message;
        return response()->json($response,200);   
    }

    public function related($id,$limit=null)
    {
        $limit = $limit==null?5:$limit;
        $article = Article::find($id);
        $keywords = explode(" ",$article->title);
        $articles = Article::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
               $query->orWhere('title', 'like', "%".$keyword."%");
            }
        })->where('id','!=',$id)->limit($limit)->get();

        $articles = new Collection($articles, $this->ArticleTransformer);
        $articles = $this->fractal->createData($articles); 
        $response = $articles->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function store(Request $request,UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'title'         =>  'required|unique:articles',
            'article'       =>  'required',
            'category_id'   =>  'required',
            'tags'          =>  'required',
            'image'         => 'required|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $image                  = $upload->image($request,'image','img_article');
        $input                  = $request->all();
        $input['image']         = $image['data']['file_name'];
        $input['view']          =   0;
        $input['slug']          = $request->title;

	
        $article                = Article::create($input);
return $article;
        $response['data']       = $input;
        $response['data']['id'] = $article->id;
        $response['status']     = "success";
        $response['message']    = 'A New Article With Title '.$request->title.' Has Created';
        return response()->json($response,201);
    }

    public function update($id,Request $request, UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'title'         =>  'required',
            'article'       =>  'required',
            'tags'          =>  'required',
            'category_id'    => 'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $article = article::find($id);
        if($article==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            if ($request->hasFile('image')) {
                $image                  = $upload->image($request,'image','img_article');
                $input                  = $request->all();
                $input['image']         = $image['data']['file_name'];
                $article->update($input);
            }else
            {
                $article->update($request->only('title','article','category_id','tags'));
            }
            
            $response['status']     ='success';
            $response['message']    = "A article With Name ".$article->title." Has Updated";
            $response['data']       = $article;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    public function delete($id)
    {
        $article = article::find($id);
        if($article==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $article->delete();
            $response['status']     ='success';
            $response['message']    = "A article With Title ".$article->title." Has Deleted";
            $response['data']       = null;
            $statusCode             = 200;
            if (file_exists(public_path('/img_article/'.$article->image))) {
                unlink(public_path('/img_article/'.$article->image));
            }
        }
        return response()->json($response,$statusCode);
    }


}
