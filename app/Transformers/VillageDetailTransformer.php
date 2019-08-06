<?php

namespace App\Transformers;

use App\Models\VillageDetail;
use Flugg\Responder\Transformers\Transformer;

class VillageDetailTransformer extends Transformer
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
     * @param  \App\VillageDetail $villageDetail
     * @return array
     */
    public function transform(Village $village)
    {
        return [
            'id' => (int) $village->id,
            'village_name'=>$village->name,
            'district_name'=>$village->district->name
        ];
    }
}
