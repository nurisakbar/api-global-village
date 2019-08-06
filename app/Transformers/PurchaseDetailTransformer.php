<?php

namespace App\Transformers;

use App\Models\Order;
use Flugg\Responder\Transformers\Transformer;

class PurchaseDetailTransformer extends Transformer
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
     * @param  \App\PurchaseDetail $purchaseDetail
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id'                => $order->id,
            'order_status'      => $order->order_status,
            'created_at'        => $order->created_at,
            'seller'            => ['id'=>$order->seller->id,'name'=>$order->seller->name],
            'buyer'             => ['id'=>$order->buyer->id,'name'=>$order->buyer->name],
            'order_number'      =>  $order->invoice_number,
            'note'              => $order->note,
            'address_delivery'  => $order->buyer->full_address,
            'address_street'    => $order->buyer->address,
            'purchase_item'     => $order->purchaseItem
        ];
    }
}
