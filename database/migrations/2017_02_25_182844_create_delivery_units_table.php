<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->string('plat_no', 20);
            $table->tinyInteger('type_id')->unsigned();
            $table->unsignedInteger('network_id')->index();
            $table->string('description')->nullable();
            $table->boolean('is_active')->unsigned()->default(1);
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
        Schema::dropIfExists('delivery_units');
    }
}
