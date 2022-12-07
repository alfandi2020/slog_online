<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->default(0);
            $table->tinyInteger('service_id')->unsigned();
            $table->unsignedInteger('pack_type_id')->default(1);
            $table->char('orig_city_id', 4);
            $table->char('orig_district_id', 7)->default(0);
            $table->char('dest_city_id', 4);
            $table->char('dest_district_id', 7)->default(0);
            $table->unsignedInteger('rate_kg')->nullable();
            $table->unsignedInteger('rate_pc')->nullable();
            $table->tinyInteger('min_weight')->unsigned()->default(1);
            $table->tinyInteger('max_weight')->unsigned()->nullable();
            $table->string('etd', 10);
            $table->tinyInteger('discount')->unsigned()->nullable();
            $table->unsignedInteger('add_cost')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique([
                'customer_id',
                'service_id',
                'pack_type_id',
                'orig_city_id',
                'orig_district_id',
                'dest_city_id',
                'dest_district_id'
            ],
            'rate_unique_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
