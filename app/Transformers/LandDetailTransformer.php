<?php

namespace App\Transformers;

use App\Models\Land;
use Flugg\Responder\Transformers\Transformer;

class LandDetailTransformer extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [];

    /**
     * List of autoloaded default relations.
     *
     * @var array
     */
    protected $load = [];

    /**
     * Transform the model.
     *
     * @param  \App\LandDetail $landDetail
     * @return array
     */
    public function transform(Land $land)
    {
        $backImages     = [];
        $frontImages    = [];

        array_push($backImages,['image_1'=>secure_asset('img_land/'.$land->image_1)]);
        array_push($frontImages,secure_asset('img_land/'.$land->image_1));

        if($land->image_2!='')
        {
            array_push($backImages,['image_2'=>secure_asset('img_land/'.$land->image_2)]);
            array_push($frontImages,secure_asset('img_land/'.$land->image_2));
        }

        if($land->image_3!='')
        {
            array_push($backImages,['image_3'=>secure_asset('img_land/'.$land->image_3)]);
            array_push($frontImages,secure_asset('img_land/'.$land->image_3));
        }

        if($land->image_4!='')
        {
            array_push($backImages,['image_4'=>secure_asset('img_land/'.$land->image_4)]);
            array_push($frontImages,secure_asset('img_land/'.$land->image_4));
        }

        return [
            'id'            => (int) $land->id,
            'name'          =>  $land->name,
            'slug'          =>  $land->slug,
            'large'         =>  $land->large,
            'description'   =>  $land->description,
            'unit_area'     =>  $land->unit_area,
            'back_images'   => $backImages,
            'front_images'  => $frontImages,
            //'image'         =>  secure_asset('img_land/'.$land->image_1)
        ];
    }
}
