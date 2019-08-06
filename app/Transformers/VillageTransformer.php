<?php

namespace App\Transformers;

use App\Models\Village;
use Flugg\Responder\Transformers\Transformer;

class VillageTransformer extends Transformer
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
     * @param  \App\Village $village
     * @return array
     */
    public function transform(Village $village)
    {
        $photos = [
            ['id'=>0,secure_asset('img_village/image1.jpg')],
            ['id'=>1,secure_asset('img_village/image2.jpg')],
            ['id'=>2,secure_asset('img_village/image3.jpg')],
            ['id'=>3,secure_asset('img_village/image4.jpg')],
        ];
        return [
            'id' => (int) $village->id,
            'village_name'=>$village->name,
            'district_name'=>$village->district->name,
            'regency_name' =>'Kabupaten Murotai',
            'Province_name' =>'Kalimantan Utara',
            'photos'=>$photos,
            'potential'=>$village->potentials
        ];
    }
}
