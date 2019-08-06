<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Harvest;
use Faker\Generator as Faker;

$factory->define(Harvest::class, function (Faker $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    return [
        'title'=>$title,
        'user_id'=>rand(1,50),
        'slug'=>$title,
        'description'=>$faker->paragraph($nbSentences = 6, $variableNbSentences = true),
    ];
});
