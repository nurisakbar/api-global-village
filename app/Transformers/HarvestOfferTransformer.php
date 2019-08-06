<?php

namespace App\Transformers;

use App\Models\HarvestOfferView;
use Flugg\Responder\Transformers\Transformer;

class HarvestOfferTransformer extends Transformer
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
     * @param  \App\HarvestOffer $harvestOffer
     * @return array
     */
    public function transform(HarvestOfferView $harvestOffer)
    {
        return [
            'id'            => $harvestOffer->id,
            'harvest_id'    => $harvestOffer->harvest_id,
            'user_offer'    => $harvestOffer->offer,
            'user_owner'    => $harvestOffer->owner,
            'price'         => $harvestOffer->price,
            'qty'           => $harvestOffer->qty,
            'note'          => $harvestOffer->note,
            //'harvest'       => $harvestOffer->harvest,
            'harvest2'      => [
                'title'             =>  $harvestOffer->harvest->title,
                'image'             =>  secure_asset('img_harvest/'.$harvestOffer->harvest->image_1),
                'estimated_date'    =>  $harvestOffer->harvest->estimated_date,
                'estimated_income'  =>  $harvestOffer->harvest->estimated_income
            ]  
        ];
    }
}
