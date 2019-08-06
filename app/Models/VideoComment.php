<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    protected $table="view_video_comments";

    public function getCreatedAtAttribute($value)
    {
        //return date_format(date_create($value),"d/m/Y H:i:s");
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function getPhotoAttribute($photo)
    {
        return secure_asset('img_user/'.$photo);
    }

    public $incrementing = false;

    // create VIEW view_video_comments as select vc.*,u.name 
    // from video_comments as vc
    // left JOIN users as u on u.id=vc.user_id
}
