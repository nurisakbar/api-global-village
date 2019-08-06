<?php

namespace App\Transformers;

use Flugg\Responder\Transformers\Transformer;
use App\Models\Product;
class ProductDetailTransformer extends Transformer
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
     * @param  \App\ProductDetail $productDetail
     * @return array
     */
    public function transform(Product $product)
    {
        $backImages     = [];
        $frontImages    = [];

        array_push($backImages,['image_1'=>secure_asset('img_product/'.$product->image_1)]);
        array_push($frontImages,secure_asset('img_product/'.$product->image_1));

        if($product->image_2!='')
        {
            array_push($backImages,['image_2'=>secure_asset('img_product/'.$product->image_2)]);
            array_push($frontImages,secure_asset('img_product/'.$product->image_2));
        }

        if($product->image_3!='')
        {
            array_push($backImages,['image_3'=>secure_asset('img_product/'.$product->image_3)]);
            array_push($frontImages,secure_asset('img_product/'.$product->image_3));
        }

        if($product->image_4!='')
        {
            array_push($backImages,['image_4'=>secure_asset('img_harvest/'.$product->image_4)]);
            array_push($frontImages,secure_asset('img_harvest/'.$product->image_4));
        }

        return [
            'id'            =>  (int) $product->id,
            'name'          =>  $product->name,
            'slug'          =>  $product->slug,
            'price'         =>  $product->price,
            'price_idr'     =>  number_format($product->price,0,',','.'),
            'category'      =>  $product->category,
            'description'   =>  $product->description,
            'images'        =>  $product->photos,
            'stock'         =>  $product->stock,
            'user_id'       =>  $product->user_id,
            'user_name'     =>  $product->user->name,
            'user_photo'    => secure_asset('img_user/'.$product->user->photo),
            'view'          =>  $product->view,
            'weight'        =>  $product->weight,
            'unit'          =>  $product->unit,
            'publish_date'  =>  date_format($product->created_at,"d/m/Y"),
            'back_images'   => $backImages,
            'front_images'  => $frontImages,
            'region'        => [
                'vilage'    =>  $product->user->village->name,
                'district'  =>  $product->user->village->district->name,
                'regency'   =>  $product->user->village->district->regency->name,
                'province'  =>  $product->user->village->district->regency->province->name,
            ],
            'comments'      => $product->comments->load('reply')
        ];
    }
}
