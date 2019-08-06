<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {

    $entity = ['article','product','video'];
    return [
        'name'=>$faker->word,
        'entity'=> $entity[rand(0,2)]
    ];
});
