<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('AccessApi');
    }

    
    // -------------------- Article -----------------------------------------------------
    public function ArticleGetComments($id,$start=null,$limit=null)
    {
        $article = \App\Models\Article::find($id);
        if($article==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid id article";
            $response['data']       = null;
            $statusCode             = 200;
        }else
        {
            $response['status']     = true;
            $response['message']    = 'success';
            $response['data']       = $article->comments;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    public function ArticleCreateComment(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'comment'         =>  'required',
            'token'           =>  'required',
            'article_id'      =>  'required|',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        $article    = \DB::table('articles')->select('id')->where('id',$request->article_id)->first();
  
        if(isset($user) and isset($article))
        {
            try {

                $comment = [
                    'id'            =>  Uuid::uuid4(),
                    'user_id'       =>  $user->id,
                    'comment'       =>  $request->comment,
                    'article_id'    =>  $request->article_id,
                    'created_at'    =>  date('Y-m-d H:i:s'),
                    'updated_at'    =>  date('Y-m-d H:i:s')
                ];
    
                \DB::table('article_comments')->insert($comment);
                $response['message']    = "Berhasil Menambahkan Komentar";
                $response['status']     = true;
                $statusCode             = 201;
              } catch (\Exception $e) {
              
                $response['message']    = $e->getMessage();
                $response['status']     = false;
                $statusCode             = 400;
              }
        }else
        {
            $response['message']    = "id user atau id artikel tidak valid";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,$statusCode);
    }

   // -------------------- Video -----------------------------------------------------
    public function VideoGetComments($id,$start=null,$limit=null)
    {
        
        $video = \App\Models\Video::find($id);
        if($video==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid id video";
            $response['data']       = null;
            $statusCode             = 200;
        }else
        {
            $response['status']     = true;
            $response['message']    = 'success';
            $response['data']       = $video->comments;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }


    public function VideoCreateComment(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'comment'         =>  'required',
            'token'           =>  'required',
            'video_id'      =>  'required|',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        $video      = \DB::table('videos')->select('id')->where('id',$request->video_id)->first();
  
          if(isset($user) and isset($video))
        {
            try {

                $comment = [
                    'id'            =>  Uuid::uuid4(),
                    'user_id'       =>  $user->id,
                    'comment'       =>  $request->comment,
                    'video_id'      =>  $request->video_id,
                    'created_at'    =>  date('Y-m-d H:i:s'),
                    'updated_at'    =>  date('Y-m-d H:i:s')
                ];

               //return $comment;
    
                \DB::table('video_comments')->insert($comment);
                $response['message']    = "Berhasil Menambahkan Komentar";
                $response['status']     = true;
                $statusCode             = 201;
              } catch (\Exception $e) {
              
                $response['message']    = $e->getMessage();
                $response['status']     = false;
                $statusCode             = 400;
              }

        }else
        {
            $response['message']    = "id user atau id video tidak valid";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,$statusCode);
    }


       // -------------------- Product -----------------------------------------------------
    public function ProductCreateComment(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'comment'         =>  'required',
            'token'           =>  'required',
            'product_id'      =>  'required|',
            
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        $product    = \DB::table('products')->select('id')->where('id',$request->product_id)->first();
  
        if(isset($user) and isset($product))
        {
            try {

                $comment = [
                    'id'            =>  Uuid::uuid4(),
                    'user_id'       =>  $user->id,
                    'comment'       =>  $request->comment,
                    'product_id'    =>  $request->product_id,
                    'created_at'    =>  date('Y-m-d H:i:s'),
                    'comment_id'    => $request->comment_id!=null?$request->comment_id:null
                ];
    
                \DB::table('product_comments')->insert($comment);
                $response['message']    = "Berhasil Menambahkan Komentar";
                $response['status']     = true;
                $statusCode             = 201;
              } catch (\Exception $e) {
              
                $response['message']    = $e->getMessage();
                $response['status']     = false;
                $statusCode             = 400;
              }
              ///return response()->json($response,$statusCode);
        }else
        {
            $response['message']    = "id user atau id artikel tidak valid";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,$statusCode);
    }


    public function ProductGetComments($id,$start=null,$limit=null)
    {
        $product = \App\Models\Product::find($id);
        if($product==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid id video";
            $response['data']       = null;
            $statusCode             = 200;
        }else
        {
            $response['status']     = true;
            $response['message']    = 'success';
            $response['data']       = $product->comments;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }



    // -------------------- Harvest -----------------------------------------------------
    
    public function HarvestGetComments($id,$start=null,$limit=null)
    {
        $harvest = \App\Models\Harvest::find($id);
        if($harvest==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid id harvest";
            $response['data']       = null;
            $statusCode             = 200;
        }else
        {
            $response['status']     = true;
            $response['message']    = 'success';
            $response['data']       = $harvest->comments;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }

    
    public function HarvestCreateComment(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'comment'         =>  'required',
            'token'           =>  'required',
            'harvest_id'      =>  'required|',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        $harvest    = \DB::table('harvests')->select('id')->where('id',$request->harvest_id)->first();
  
        if(isset($user) and isset($harvest))
        {
            try {

                $comment = [
                    'id'            =>  Uuid::uuid4(),
                    'user_id'       =>  $user->id,
                    'comment'       =>  $request->comment,
                    'harvest_id'    =>  $request->harvest_id,
                    'created_at'    =>  date('Y-m-d H:i:s')
                ];
    
                \DB::table('harvest_comments')->insert($comment);
                $response['message']    = "Berhasil Menambahkan Komentar";
                $response['status']     = true;
                $statusCode             = 201;
              } catch (\Exception $e) {
              
                $response['message']    = $e->getMessage();
                $response['status']     = false;
                $statusCode             = 400;
              }
              ///return response()->json($response,$statusCode);
        }else
        {
            $response['message']    = "id user atau id artikel tidak valid";
            $response['status']     = false;
            $statusCode             = 401;
        }

        return response()->json($response,$statusCode);
    }
}
