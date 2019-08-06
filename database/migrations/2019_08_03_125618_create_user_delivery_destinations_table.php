<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDeliveryDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_delivery_destinations', function (Blueprint $table) {
            $table->uuid('user_id');  
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone');
            $table->string('village_id');
            $table->string('street');
            $table->timestamps();
            $table->softDeletes();
            $table->enum('default',['y','n'])->default('n');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_delivery_destinations');
    }
}
