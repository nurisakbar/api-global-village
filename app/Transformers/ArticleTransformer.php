<?php

namespace App\Transformers;

use App\Models\Article;
use Flugg\Responder\Transformers\Transformer;

class ArticleTransformer extends Transformer
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
     * @param  \App\Article $article
     * @return array
     */
    public function transform(Article $article)
    {
        return [
            'id'            =>  (int) $article->id,
            'title'         =>  $article->title,
            //'category_id'   =>  $article->category_id,
            'publish_date'  =>  date_format($article->created_at,"d/m/Y"),
            //'category_name' =>  $article->category->name,
            'category'      => $article->category,
            'article'       =>  $article->article,
            //'image'         =>  $article->image,
            'tags'           =>  $article->tags,
            'tags_array'    =>  explode(",",$article->tags),
            'image_url'     =>  secure_asset('img_article/'.$article->image),
            'view'          =>  $article->view
        ];
    }
}
