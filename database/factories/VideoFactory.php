<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {

    $nomor = rand(1,15);
    $video = 'video_'.$nomor.'.mp4';
    $thumbail = 'video_'.$nomor.'.jpg';
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    return [
        'title'=>$title,
        'description'=>$faker->paragraph($nbSentences = 6, $variableNbSentences = true),
        'slug'=>$title,
        'file'=>$video,
        'img_thumbnail'=>$thumbail,
        'view'=>0,
        'like'=>0,
        'dislike'=>0,
        //'file_url'=>$video,
        'category_id'=>rand(5,8)
    ];
});
