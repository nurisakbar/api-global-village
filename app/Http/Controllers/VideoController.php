<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Video;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\VideoTransformer;
//use App\Transformers\VideoDetailTransformer;
use App\Services\UploadService;
use Ramsey\Uuid\Uuid;

class VideoController extends Controller
{
    private $fractal;
    private $VideoTransformer;
    //private $VideoDetailTransformer;

    public function __construct(VideoTransformer $VideoTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->VideoTransformer = $VideoTransformer;
        //$this->VideoDetailTransformer = $VideoDetailTransformer;
        $this->fractal = new Fractal\Manager();
    }


    public function index()
    {
        $limit      = Input::get('limit');
        $start      = Input::get('start');

        if($limit==null and $start==null)
        {
            $Videos = Video::orderBy('created_at','DESC')->get();
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

            $Videos = Video::orderBy('created_at','DESC')->take($limit)->skip($start)->get();
        }
                

        $Videos = new Collection($Videos, $this->VideoTransformer);
        $Videos = $this->fractal->createData($Videos); 
        $response = $Videos->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function show($id)
    {
        $Video = Video::find($id);
        if($Video!=null)
        {
            // counter view
            $Video->view  = $Video->view+1;
            $Video->update();
            
            
            $Video = new Item($Video, $this->VideoTransformer);
            $Video = $this->fractal->createData($Video); 
            $response = $Video->toArray();
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
            $Videos  = Video::skip($start-1)->take($limit)->where('title','like',"%$keyword%")->orderBy('created_at')->get(); 
            $Videos = new Collection($Videos, $this->VideoTransformer);
            $Videos = $this->fractal->createData($Videos); 
            $response = $Videos->toArray();
            $response['success'] = true;
            $response['status']  = 200;
            return response()->json($response,200);
        }else
        {
            return response()->json($errors,200);
        }
    }

    public function related($id,$limit=null)
    {
        $limit = $limit==null?5:$limit;
        
        $Video = Video::find($id);
        $keywords = explode(" ",$Video->title);
        $Videos = Video::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
               $query->orWhere('title', 'like', "%".$keyword."%");
            }
        })->where('id','!=',$id)->limit($limit)->get();

        $Videos = new Collection($Videos, $this->VideoTransformer);
        $Videos = $this->fractal->createData($Videos); 
        $response = $Videos->toArray();
        $response['success'] = true;
        $response['status']  = 200;
        return response()->json($response,200);
    }

    public function category($id,$start=null,$limit=null)
    {
        $category = \DB::table('categories')->where('id',$id)->first();

        if(isset($category))
        {

            // filter atau tidak

            if(!isset($start) and !isset($limit))
            {
                $videos = Video::where('category_id',$id)->orderBy('created_at','DESC')->get();
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
                    $videos = Video::where('category_id',$id)->skip($start)->take($limit)->orderBy('created_at','DESC')->get();
                }
            }

            
            $videos = new Collection($videos, $this->VideoTransformer);
            $videos = $this->fractal->createData($videos); 
            $response = $videos->toArray();
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

    public function generateIageThumbnail($fileName)
    {

        $ffmpeg           = "ffmpeg";
        
        $videoFile        = 'videos/'.$fileName;

        $imageFile        = $fileName;

        //$imageFile = 'videos/thumbail/'.preg_replace('/\s+/', '', pathinfo($imageFile,PATHINFO_FILENAME)).'.jpg';
        
        $thumbnail = preg_replace('/\s+/', '', pathinfo($imageFile,PATHINFO_FILENAME)).'.jpg';
        $thumbnail_url = 'videos/thumbnail/'.preg_replace('/\s+/', '', pathinfo($imageFile,PATHINFO_FILENAME)).'.jpg';
        $size = "1200x870";
        
        $getFromSecond = 15;
        
        $cmd = "$ffmpeg -i $videoFile -an -ss $getFromSecond -s $size $thumbnail_url";
        shell_exec($cmd);

        return ['file_name' =>$thumbnail,'img_thumbnail'=>$thumbnail];
    }

    public function store(Request $request,UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'title'         =>  'required|unique:videos',
            'video'         =>  'required',
            'description'   =>  'required',
            'tags'          =>  'required',
            'category_id'   =>  'required',
            //'image'         => 'required|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        //$image                  = $upload->image($request,'videos/thumbnail');
        $video                  = $upload->video($request,'videos');
        $img_thumbnail          = $this->generateIageThumbnail($video['data']['file_name']);

        $input                  = $request->all();
        $input['img_thumbnail'] = $img_thumbnail['file_name'];
        $input['file']          = $video['data']['file_name'];
        $input['slug']          = $request->title;
        $input['view']          =   0;
        $input['like']          = 0;
        $input['dislike']       = 0;
        $Video                  = new Video();
        $result                  = $Video->create($input);
        $response['data']       = $input;
        $response['data']['id'] = $result->id;
        $response['status']     = "success";
        $response['message']    = 'A New Video With Title '.$request->title.' Has Created';
        return response()->json($response,201);
    }



    public function update($id,Request $request, UploadService $upload)
    {
        $validator          = \Validator::make($request->all(), [
            'title'         =>  'required',
            'description'   =>  'required',
            'tags'          => 'required',
            'category_id'   =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $Video = Video::find($id);
        
      
        if($Video==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            if ($request->hasFile('video')) {
                $video                  = $upload->video($request,'videos');
                $img_thumbnail          = $this->generateIageThumbnail($video['data']['file_name']);
                $Video->title = $request->title;
                
                $input                  = $request->all();
                $input['img_thumbnail'] = $img_thumbnail['file_name'];
                $input['file']          = $video['data']['file_name'];
                $input['slug']          = $request->title;
                $Video->update($input);
            }else
            {
                $input                  = $request->all();
                $input['slug']          = $request->title;
                $Video->update($input);
            }

            //$VideoUpdate          = $Video->update($request->all());
            $response['status']     ='success';
            $response['message']    = "A Video With Name ".$Video->title." Has Updated";
            $response['data']       = $Video;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    public function delete($id)
    {
        $video = Video::find($id);
        if($Video==null)
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 200;
        }else
        {
            $Video->delete();
            $response['status']     ='success';
            $response['message']    = "A Video With Title ".$video->title." Has Deleted";
            $response['data']       = null;
            $statusCode             = 200;
            if (file_exists(public_path('/videos/thumbnail/'.$video->img_thumbnail))) {
                unlink(public_path('/video/img_thumbnail/'.$video->img_thumbnail));
            }
        }
        return response()->json($response,$statusCode);
    }



}
