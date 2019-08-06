<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    $image = 'images_'.rand(1,8).'.jpg';
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    return [
        'title'             =>  $title,
         'category_id'      =>  rand(1,4),
         'image'            =>  $image,
         'slug'             =>  $title,
         'publish'          =>   'y',
         'view'             =>   0,
         'article'          =>  $faker->text($maxNbChars = 600)  
    ];
});
