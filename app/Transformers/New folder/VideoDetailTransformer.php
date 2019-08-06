<?php

namespace App\Transformers;

use App\Models\Video;
use Flugg\Responder\Transformers\Transformer;

class VideoDetailTransformer extends Transformer
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
     * @param  \App\VideoDetail $videoDetail
     * @return array
     */
    public function transform(Video $video)
    {
        return [
            'id'                => (int) $video->id,
            'title'             =>  $video->title,
            'description'       =>  $video->description,
            'slug'              =>  $video->slug,
            'video_file'        =>  $video->file,
            'video_url'         =>  secure_asset('videos/'.$video->file),
            'img_thumbnail'      =>  $video->img_thumbnail,
            'img_thumbnail_url'  =>  secure_asset('videos/thumbnail/'.$video->img_thumbnail),
            'category_name'     => $video->category->name,
            'category_id'       => $video->category->id,
            'publish_date'      => date_format($video->created_at,"d/m/Y"),
            'view'              => $video->view,
            'like'              => $video->like,
            'tags'              =>  $video->tags,
            'tags_array'        =>  explode(",",$video->tags),
            'dislike'           => $video->dislike,
            //'comments'          => $video->comments
        ];
    }
}
