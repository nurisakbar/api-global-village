<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker\Generator as Faker;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\UserTransformer;
use App\User;

class TestController extends Controller
{

    private $fractal;
    private $UserTransformer;
    
    public function __construct(UserTransformer $UserTransformer,Fractal\Manager $fractal)
    {
        parent::__construct();
        return $this->middleware('AccessApi');
        $this->UserTransformer = $UserTransformer;
        $this->fractal = new Fractal\Manager();
    }



    // public function __construct()
    // {
    //     return $this->middleware('AccessApi');
    // }

    public function a()
    {
        $articles = User::all();
        $articles = new Collection($articles, $this->UserTransformer);
        $articles = $this->fractal->createData($articles); 
        $response = $articles->toArray();
        return $response;

        // $a = \DB::table('users')->get();
        // return $a;
    }

    public function comment(Faker $faker)
    {
        \DB::table('article_comments')->truncate();
        \DB::table('video_comments')->truncate();
        \DB::table('product_comments')->truncate();
        \DB::table('harvest_comments')->truncate();

        for($i=1;$i<=50;$i++)
        {
            
            for($j=1;$j<=7;$j++)
            {
                $ArticleComment = [
                    'article_id'=>  $i,
                    'user_id'   =>  rand(1,30),
                    'comment'   =>  $faker->realText($maxNbChars = 100, $indexSize = 2),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                \DB::table('article_comments')->insert($ArticleComment);
            }
            
          

            for($j=1;$j<=7;$j++)
            {
                $VideoComment = [
                    'video_id'=>  $i,
                    'user_id'   =>  rand(1,30),
                    'comment'   =>  $faker->realText($maxNbChars = 100, $indexSize = 2),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                \DB::table('video_comments')->insert($VideoComment);
            }
            
            
             for($j=1;$j<=7;$j++)
            {
                $ProductComment = [
                    'product_id'=>  $i,
                    'user_id'   =>  rand(1,30),
                    'comment'   =>  $faker->realText($maxNbChars = 100, $indexSize = 2),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                \DB::table('product_comments')->insert($ProductComment);
            }

            
            for($j=1;$j<=7;$j++)
            {
                $HarvestComment = [
                    'harvest_id'=>  $i,
                    'user_id'   =>  rand(1,30),
                    'comment'   =>  $faker->realText($maxNbChars = 100, $indexSize = 2),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                \DB::table('harvest_comments')->insert($HarvestComment);
            }
        }
    }
}
