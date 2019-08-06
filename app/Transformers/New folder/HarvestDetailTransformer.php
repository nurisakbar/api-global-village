<?php

namespace App\Transformers;

//use App\Models\HarvestDetail;
use Flugg\Responder\Transformers\Transformer;
use App\Models\Harvest;

class HarvestDetailTransformer extends Transformer
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
     * @param  \App\HarvestDetail $harvestDetail
     * @return array
     */
    public function transform(Harvest $harvest)
    {
        $backImages     = [];
        $frontImages    = [];

        array_push($backImages,['image_1'=>secure_asset('img_harvest/'.$harvest->image_1)]);
        array_push($frontImages,secure_asset('img_harvest/'.$harvest->image_1));

        if($harvest->image_2!='')
        {
            array_push($backImages,['image_2'=>secure_asset('img_harvest/'.$harvest->image_2)]);
            array_push($frontImages,secure_asset('img_harvest/'.$harvest->image_2));
        }

        if($harvest->image_3!='')
        {
            array_push($backImages,['image_3'=>secure_asset('img_harvest/'.$harvest->image_3)]);
            array_push($frontImages,secure_asset('img_harvest/'.$harvest->image_3));
        }

        if($harvest->image_4!='')
        {
            array_push($backImages,['image_4'=>secure_asset('img_harvest/'.$harvest->image_4)]);
            array_push($frontImages,secure_asset('img_harvest/'.$harvest->image_4));
        }
        
        return [
            'id'                =>  $harvest->id,
            'title'             =>  $harvest->title,
            'slug'              =>  $harvest->slug,
            'description'       =>  $harvest->description,
            'user_id'           =>  $harvest->user_id,
            'user_name'         =>  $harvest->user->name,
            'user_photo'         => secure_asset('img_user/'.$harvest->user->photo),
            //'images'        =>  $harvest->photos,
            'back_images'   => $backImages,
            'front_images'  => $frontImages,
           
            'view'              =>  $harvest->view,
            //'publish_date'      =>  date_format($harvest->created_at,"d F Y"),
            'publish_date'      =>  $harvest->created_at,
            'estimated_date'    =>  $harvest->estimated_date,
            'estimated_income'  =>  $harvest->estimated_income,
            'land'              => $harvest->land,
            'category'          =>  $harvest->category,
            'unit'              =>  $harvest->unit,
            'region'            => [
                'vilage'        =>  $harvest->user->village->name,
                'district'      =>  $harvest->user->village->district->name,
                'regency'       =>  $harvest->user->village->district->regency->name,
                'province'      =>  $harvest->user->village->district->regency->province->name,
            ],
            //'comments'      =>  $harvest->comments,
        ];
    }
}
