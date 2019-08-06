<?php

namespace App\Transformers;

use App\Models\Article;
use Flugg\Responder\Transformers\Transformer;

class ArticleDetailTransformer extends Transformer
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
     * @param  \App\ArticleDetail $articleDetail
     * @return array
     */
    public function transform(Article $article)
    {
        return [
            'id'            =>  (int) $article->id,
            'title'         =>  $article->title,
            'category_id'   =>  $article->category_id,
            'publish_date'  =>  date_format($article->created_at,"d/m/Y"),
            'category_name' =>  $article->category->name,
            'article'       =>  $article->article,
            //'image'         =>  $article->image,
            'tags'           =>  $article->tags,
            'tags_array'    =>  explode(",",$article->tags),
            'image_url'     =>  secure_asset('img_article/'.$article->image),
            'view'          =>  $article->view,
            //'comments'      =>  $article->comments,
            //'comments'      =>  $article->comments->load('user')
        ];
    }
    
}
