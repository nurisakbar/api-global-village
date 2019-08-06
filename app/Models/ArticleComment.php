<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $table="view_article_comments";

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();

        //return date_format(date_create($value),"d/m/Y H:i:s");
    }

    public function getPhotoAttribute($photo)
    {
        return secure_asset('img_user/'.$photo);
    }

    public $incrementing = false;

    // create VIEW view_article_comments as select ac.*,u.name 
    // from article_comments as ac
    // left JOIN users as u on u.id=ac.user_id
}
