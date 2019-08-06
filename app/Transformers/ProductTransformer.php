<?php

namespace App\Transformers;

use App\Models\Product;
use Flugg\Responder\Transformers\Transformer;

class ProductTransformer extends Transformer
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
     * @param  \App\Product $product
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'id'            =>  (int) $product->id,
            'name'          =>  $product->name,
            'slug'          =>  $product->slug,
            'price'         =>  $product->price,
            'price_idr'     =>  number_format($product->price,0,',','.'),
            // 'category_id'   =>  $product->category_id,
            'stock'         =>  $product->stock,
            // 'category_name' =>  $product->category->name,
            'description'   =>  $product->description,
            'image_url'     =>  secure_asset('img_product/'.$product->image_1),
            // 'user_id'       =>  $product->user_id,
            // 'user_name'     =>  $product->user->name,
            'user'          =>  ['id'=>$product->user->id,'name'=>$product->user->name,'photo'=>secure_asset('img_user/'.$product->user->photo)],
            'category'      =>  $product->category,
            'view'          =>  $product->view,
            'weight'        =>  $product->weight,
            'unit'          =>  $product->unit->name,
            'publish_date'  =>  date_format($product->created_at,"d/m/Y"),
           'region'        => [
                'vilage'    =>  $product->user->village->name,
                'district'  =>  $product->user->village->district->name,
                'regency'   =>  $product->user->village->district->regency->name,
                'province'  =>  $product->user->village->district->regency->province->name,
                ]
        ];
    }
}
