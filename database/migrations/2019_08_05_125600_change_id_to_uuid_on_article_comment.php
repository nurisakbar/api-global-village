<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdToUuidOnArticleComment extends Migration
{
    public function __construct()
{
    DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
}

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_comments', function (Blueprint $table) {
            //$table->dropColumn('id');
           $table->uuid('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_comments', function (Blueprint $table) {
            //
        });
    }
}
