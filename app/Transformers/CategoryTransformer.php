<?php

namespace App\Transformers;

use App\Models\Category;
use Flugg\Responder\Transformers\Transformer;

class CategoryTransformer extends Transformer
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
     * @param  \App\Category $category
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'id' => (int) $category->id,
            'name'=>$category->name,
            'entity'=>$category->entity,
            'publish'=>$category->publish,
            'image_web'=>$category->image_web,
            'image_mobile'=>$category->image_mobile,
        ];
    }
}
