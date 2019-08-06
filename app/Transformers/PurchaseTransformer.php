<?php

namespace App\Transformers;

use App\Models\Order;
use Flugg\Responder\Transformers\Transformer;

class PurchaseTransformer extends Transformer
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
     * @param  \App\Purchase $purchase
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id'            =>  $order->id,
            'order_number'  =>  $order->invoice_number,
            'created_at'    =>  $order->created_at,
            'image'         =>  secure_asset('img_product/'.$order->item->product->image_1),
            'order_status'  =>  $order->order_status,
            'total_pay'     =>  50000,
            'seller'        =>  $order->seller->name,
            'buyer'         =>  $order->buyer->name
        ];
    }
}
