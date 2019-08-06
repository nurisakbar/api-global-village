<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewOrderItemView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::statement("drop view order_item_view");

        DB::statement("create view order_item_view as SELECT oi.id,oi.user_id as user_id_buyer,oi.order_id,oi.product_id,p.name,p.image_1 as image,oi.qty,oi.price,oi.qty*oi.price as subtotal,oi.created_at,p.user_id as user_id_seller,u.name as seller_name 
        FROM order_items as oi
        left JOIN products as p on p.id=oi.product_id
        left JOIN users as u on u.id=p.user_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view_order_item_view');
    }
}
