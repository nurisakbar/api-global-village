<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    protected $table="view_product_comments";

    public function getCreatedAtAttribute($value)
    {
        //return date_format(date_create($value),"d/m/Y H:i:s");
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function getPhotoAttribute($photo)
    {
        return secure_asset('img_user/'.$photo);
    }

    public function reply()
    {
        return $this->hasMany('App\Models\ProductComment', 'comment_id')->orderBy('created_at','ASC');
    }

    public $incrementing = false;

    // create VIEW view_product_comments as select pc.*,u.name,u.photo 
    // from product_comments as pc
    // left JOIN users as u on u.id=pc.user_id
}
