<?php

use Illuminate\Database\Seeder;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
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
