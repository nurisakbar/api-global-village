<?php

namespace App\Transformers;

use App\Models\Land;
use Flugg\Responder\Transformers\Transformer;

class LandTransformer extends Transformer
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
     * @param  \App\Land $land
     * @return array
     */
    public function transform(Land $land)
    {
        return [
            'id' => (int) $land->id,
        ];
    }
}
