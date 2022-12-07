<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifests', function (Blueprint $table) {
            $table->increments('id');
            $table->char('number', 19)->unique();
            $table->tinyInteger('type_id')->index()->unsigned();
            $table->unsignedInteger('weight')->nullable();
            $table->tinyInteger('pcs_count')->unsigned()->nullable();
            $table->unsignedInteger('orig_network_id')->index();
            $table->unsignedInteger('dest_network_id')->index();
            $table->unsignedInteger('creator_id')->index();
            $table->unsignedInteger('handler_id')->index()->nullable();
            $table->unsignedInteger('customer_id')->index()->nullable();
            $table->dateTime('deliver_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->unsignedInteger('delivery_unit_id')->index()->nullable();
            $table->unsignedInteger('start_km')->nullable();
            $table->unsignedInteger('end_km')->nullable();
            $table->char('dest_city_id', 4)->nullable();
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
        Schema::dropIfExists('manifests');
    }
}
