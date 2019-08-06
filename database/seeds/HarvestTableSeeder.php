<?php

use Illuminate\Database\Seeder;

class HarvestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('harvests')->truncate();
        factory('App\Harvest',50)->create();

        $photos = [];

        for($i=1;$i<=50;$i++)
        {
            $photos[] = ['entity'=>'harvest','file_name'=>'image1.jpg','entity_id'=>$i];
            $photos[] = ['entity'=>'harvest','file_name'=>'image2.jpg','entity_id'=>$i];
            $photos[] = ['entity'=>'harvest','file_name'=>'image3.jpg','entity_id'=>$i];
            $photos[] = ['entity'=>'harvest','file_name'=>'image4.jpg','entity_id'=>$i];
        }

        \DB::table('photos')->insert($photos);
    }
}
