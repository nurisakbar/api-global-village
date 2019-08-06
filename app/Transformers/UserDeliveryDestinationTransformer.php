<?php

namespace App\Transformers;

use App\Models\UserDeliveryDestination;
use Flugg\Responder\Transformers\Transformer;

class UserDeliveryDestinationTransformer extends Transformer
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
     * @param  \App\UserDeliveryDestination $userDeliveryDestination
     * @return array
     */
    public function transform(UserDeliveryDestination $userDeliveryDestination)
    {
        return [
            'id'            => (string) $userDeliveryDestination->id,
            'name'          =>  $userDeliveryDestination->name,
            'phone'         =>  $userDeliveryDestination->phone,
            'street'       =>  $userDeliveryDestination->street,
            'full_address'  =>  $userDeliveryDestination->full_address,
            'default'       => $userDeliveryDestination->default
        ];
    }
}
