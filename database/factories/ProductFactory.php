<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $name = $faker->sentence($nbWords = 6, $variableNbWords = true);
    //$farmer = \App\Farmer::select('id')->inRandomOrder()->first();
    //$images = 'product_'.rand(1,8).'.jpg';
    $harga = ['15000','20000','23500','30000','70000','100000','43000','10000','9000'];
    return [
        'name'          =>  $name,
        'price'         =>  $harga[rand(0,8)],
        'category_id'   =>  rand(9,13),
        'slug'          =>  $name,
        'description'   =>  $faker->paragraph($nbSentences = 12, $variableNbSentences = true),
        'user_id'       =>  round(1,50),
        'view'          =>  0,
        'stock'         =>  rand(3,120),
        'unit_id'       =>  rand(1,3),
        'weight'        =>  rand(1,3),
        'image_1'       =>  'product_'.rand(1,8).'.jpg',
        'image_2'       =>  'product_'.rand(1,8).'.jpg',
        'image_3'       =>  'product_'.rand(1,8).'.jpg',
        'image_4'       =>  'product_'.rand(1,8).'.jpg',
    ];
});
