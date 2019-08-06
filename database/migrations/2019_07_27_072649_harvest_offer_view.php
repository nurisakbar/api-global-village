<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HarvestOfferView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "create view harvest_offer_view as SELECT ho.id,ho.harvest_id,ho.price,ho.qty,ho.note,ho.user_id as user_id_offer,h.user_id as user_id_owner,ho.created_at 
        FROM harvest_offers as ho 
        left join harvests as h on h.id=ho.harvest_id";
        DB::statement($sql);
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
