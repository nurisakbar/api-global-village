<?php

use Illuminate\Database\Seeder;
use App\Farmer;
use App\Farm;
use App\Photo;

class FarmsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('farms')->truncate();

        $farmers = Farmer::take(10)->get();
        foreach($farmers as $farmer)
        {
            $farm = [
                'name'          =>  'sawah beras merah',
                'farmer_id'     => $farmer->id,
                'category_id'   => 14,
                'description'   =>  'sawah yang menanam komoditi beras mrah',
                'address'       =>  'kampung cilame, desa tambak baya, temanggung'
            ];

            $farm = Farm::create($farm);

            for($i=1;$i<=6;$i++)
            {
                $photo = ['file_name'=>'farm_'.$i.'.jpg','entity'=>'farm','entity_id'=>$farm->id];
                Photo::create($photo);
            }


        }
    }
}
