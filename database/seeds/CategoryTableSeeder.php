<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();

        $data = [
            ['name'=>'Article Category 1','entity'=>'article'],
            ['name'=>'Article Category 2','entity'=>'article'],
            ['name'=>'Article Category 3','entity'=>'article'],
            ['name'=>'Article Category 4','entity'=>'article'],

            ['name'=>'Video Category 1','entity'=>'video'],
            ['name'=>'Video Category 2','entity'=>'video'],
            ['name'=>'Video Category 3','entity'=>'video'],
            ['name'=>'Video Category 4','entity'=>'video'],

            ['name'=>'Category Product 1','entity'=>'product'],
            ['name'=>'Category Product 2','entity'=>'product'],
            ['name'=>'Category Product 3','entity'=>'product'],
            ['name'=>'Category Product 4','entity'=>'product'],
            ['name'=>'Category Product 5','entity'=>'product'],
        ];
        Category::insert($data);
    }
}
