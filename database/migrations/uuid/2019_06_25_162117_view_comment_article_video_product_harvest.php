<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewCommentArticleVideoProductHarvest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $view_article_comments = "create VIEW view_article_comments as select ac.*,u.name,u.photo 
        from article_comments as ac
        left JOIN users as u on u.id=ac.user_id";

        $view_video_comments = "create VIEW view_video_comments as select vc.*,u.name,u.photo 
        from video_comments as vc
        left JOIN users as u on u.id=vc.user_id";

        $view_product_comments = "create VIEW view_product_comments as select pc.*,u.name,u.photo 
        from product_comments as pc
        left JOIN users as u on u.id=pc.user_id";

        $view_harvest_comments = "create VIEW view_harvest_comments as select hc.*,u.name,u.photo 
        from harvest_comments as hc
        left JOIN users as u on u.id=hc.user_id";

        \DB::statement($view_article_comments);
        \DB::statement($view_harvest_comments);
        \DB::statement($view_product_comments);
        \DB::statement($view_video_comments);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
