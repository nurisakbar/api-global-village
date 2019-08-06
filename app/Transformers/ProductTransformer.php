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
            'id'            =>  (string) $product->id,
            'name'          =>  $product->name,
            'slug'          =>  $product->slug,
            'price'         =>  $product->price,
            'price_idr'     =>  number_format($product->price,0,',','.'),
            'stock'         =>  $product->stock,
            'description'   =>  $product->description,
            'image_url'     =>  secure_asset('img_product/'.$product->image_1),
            'user'          =>  ['id'=>$product->user->id,'name'=>$product->user->name,'photo'=>secure_asset('img_user/'.$product->user->photo)],
            'category'      =>  $product->category,
            'view'          =>  $product->view,
            'weight'        =>  $product->weight,
            'unit'          =>  $product->unit,
            'back_images'   => $backImages,
            'front_images'  => $frontImages,
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
