<?php

namespace App\Transformers;

use App\Models\OrderItem;
use Flugg\Responder\Transformers\Transformer;

class OrderItemTransformer extends Transformer
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
     * @param  \App\OrderItem $orderItem
     * @return array
     */
    public function transform(OrderItem $orderItem)
    {
        return [
            'id'            => (int) $orderItem->id,
            'product_id'    =>  $orderItem->product_id,
            'product_name'  =>  $orderItem->product->name,
            'price'         =>  $orderItem->product->price,
            'price_idr'     =>  number_format($orderItem->product->price,0,',','.'),
            'qty'           =>  $orderItem->qty,
            'subtotal'      =>  $orderItem->qty*$orderItem->price,
            'image_url'     =>  secure_asset('img_product/'.$orderItem->product->image_1),
            //'created_at'    =>  $orderItem->created_at
        ];
    }
}
