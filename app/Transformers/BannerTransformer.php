<?php

namespace App\Transformers;

use App\Models\Banner;
use Flugg\Responder\Transformers\Transformer;

class BannerTransformer extends Transformer
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
     * @param  \App\Banner $banner
     * @return array
     */
    public function transform(Banner $banner)
    {
        return [
            'id' => (int) $banner->id,
            'name'=>$banner->name,
            'description'=>$banner->description,
            'image_mobile'=>asset('img_banner/'.$banner->image_mobile),
            'image_web'=>asset('img_banner/'.$banner->image_web),
        ];
    }
}
