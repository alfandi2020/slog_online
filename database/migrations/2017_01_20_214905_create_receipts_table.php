<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('service_id')->unsigned()->index();
            $table->char('number', 18)->unique();
            $table->dateTime('pickup_time')->index();
            $table->text('items_detail')->nullable();
            $table->smallInteger('pcs_count')->unsigned();
            $table->smallInteger('items_count')->unsigned();
            $table->decimal('weight', 8, 2)->unsigned();
            $table->unsignedInteger('pack_type_id')->index();
            $table->string('pack_content')->nullable();
            $table->unsignedInteger('pack_value')->nullable();
            $table->char('orig_city_id', 4);
            $table->char('orig_district_id', 7)->default(0);
            $table->char('dest_city_id', 4);
            $table->char('dest_district_id', 7)->default(0);
            $table->boolean('charged_on')->unsigned()->default(1)->comment('1:weight, 2:item');
            $table->text('consignor');
            $table->text('consignee');
            $table->unsignedInteger('creator_id')->index();
            $table->unsignedInteger('network_id')->index();
            $table->string('status_code', 4);
            $table->unsignedInteger('invoice_id')->index()->nullable();
            $table->unsignedInteger('rate_id')->nullable()->index();
            $table->unsignedInteger('amount');
            $table->unsignedInteger('bill_amount');
            $table->unsignedInteger('base_rate');
            $table->unsignedInteger('customer_id')->nullable()->index();
            $table->string('reference_no', 30)->nullable();
            $table->string('customer_invoice_no')->nullable();
            $table->tinyInteger('payment_type_id')->unsigned();
            $table->string('costs_detail');
            $table->unsignedInteger('last_officer_id')->index()->nullable();
            $table->string('last_location_id', 7)->nullable();
            $table->unsignedInteger('pickup_courier_id')->nullable();
            $table->unsignedInteger('delivery_courier_id')->nullable();
            $table->string('notes')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('receipts');
    }
}
