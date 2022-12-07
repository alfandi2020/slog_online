<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryProofsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('progress_id');
            $table->unsignedInteger('receipt_id')->unique();
            $table->unsignedInteger('manifest_id');
            $table->unsignedInteger('courier_id');
            $table->unsignedInteger('creator_id');
            $table->string('location_id', 7);
            $table->string('status_code', 4);
            $table->string('recipient', 60);
            $table->dateTime('delivered_at');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_proofs');
    }
}
