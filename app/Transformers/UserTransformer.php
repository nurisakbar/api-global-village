<?php

namespace App\Transformers;

use App\Models\User;
use Flugg\Responder\Transformers\Transformer;

class UserTransformer extends Transformer
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
     * @param  \App\User $user
     * @return array
     */
    public function transform(User $user)
    {
        
        if($user->village_id==null)
        {
            $village = null;
        }else
        {
            //$region = $user->village_id==''?'kosong':$user->village->district->regency->province;
            $village = $user->village;
        }

        $photo = $user->photo==null?null:secure_asset('img_user/'.$user->photo);

        return [
            'id'        =>  (string)$user->id,
            'name'      =>  $user->name,
            'email'     =>  $user->email,
            'phone'     =>  $user->phone,
            'address'   =>  $user->address,
            'village'   =>  $village,
            'photo'     =>  $photo ,
            'token'     =>  $user->api_token
        ];
    }
}
