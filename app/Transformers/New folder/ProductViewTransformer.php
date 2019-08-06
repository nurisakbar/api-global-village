<?php

namespace App\Transformers;

use App\Models\ProductView;
use Flugg\Responder\Transformers\Transformer;

class ProductViewTransformer extends Transformer
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
     * @param  \App\ProductView $productView
     * @return array
     */
    public function transform(ProductView $product)
    {
        return [
            'id'            =>  (int) $product->id,
            'name'          =>  $product->name,
            'slug'          =>  $product->slug,
            'price'         =>  $product->price,
            'price_idr'     =>  number_format($product->price,0,',','.'),
            // 'category_id'   =>  $product->category_id,
            // 'category_name' =>  $product->category->name,
            'description'   =>  $product->description,
            //'image'         =>  $product->photos()->first()['file_name'],
            //'image_url'     =>  $product->photos()->where('entity','product')->first()['file_name'],
            'image_url'     =>  secure_asset('img_product/'.$product->image_1),
            'user_id'       =>  $product->user_id,
            'user_name'     =>  $product->seller_name,
            'view'          =>  $product->view,
            'weight'        =>  $product->weight,
            // 'unit'          =>  $product->unit->name,
            'publish_date'  =>  date_format($product->created_at,"d/m/Y"),
            //'location'      =>  $product->user->village->district->name.' - '.$product->user->village->district->regency->name,
            'region'        => [
                'vilage'    =>  $product->village_name,
                'district'  =>  $product->district_name,
                'regency'   =>  $product->regency_name,
                'province'  =>  $product->province_name,
                ]
        ];
    }
}
