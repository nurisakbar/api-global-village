<?php

namespace App\Transformers;

use App\Models\Land;
use Flugg\Responder\Transformers\Transformer;

class LandTtransformer extends Transformer
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
     * @param  \App\LandTtransformer $landTtransformer
     * @return array
     */
    public function transform(Land $land)
    {
        return [
            'id'            => (int) $land->id,
            'name'          =>  $land->name,
            'slug'          =>  $land->slug,
            'large'         =>  $land->large,
            'description'   =>  $land->description,
            'unit_area'     =>  $land->unit_area,
            'image'         =>  secure_asset('img_land/'.$land->image_1)
        ];
    }
}
