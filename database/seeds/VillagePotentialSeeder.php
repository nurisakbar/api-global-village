<?php

use Illuminate\Database\Seeder;
use App\Potential;
class VillagePotentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $villages = \DB::table('villages')->get();
        foreach($villages as $village)
        {
            $potential = Potential::inRandomOrder()->first();
            \DB::table('village_potentials')->insert(['village_id'=>$village->id,'potential_id'=>$potential->id]);
        }
    }
}
