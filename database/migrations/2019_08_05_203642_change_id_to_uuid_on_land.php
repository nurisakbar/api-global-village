<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIdToUuidOnLand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lands', function (Blueprint $table) {
            DB::statement('ALTER TABLE products MODIFY id char(36)');
            DB::statement('ALTER TABLE products MODIFY user_id char(36)');
            $table->index(['id', 'user_id','village_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lands', function (Blueprint $table) {
            //
        });
    }
}
