<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->char('number', 15)->unique();
            $table->tinyInteger('type_id')->index()->default(2);
            $table->string('periode', 10);
            $table->unsignedInteger('customer_id')->nullable();
            $table->date('date')->index();
            $table->date('end_date');
            $table->unsignedInteger('network_id')->index();
            $table->unsignedInteger('creator_id')->index();
            $table->unsignedInteger('handler_id')->nullable();
            $table->unsignedInteger('amount');
            $table->date('sent_date')->nullable();
            $table->date('problem_date')->nullable();
            $table->date('received_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->date('verify_date')->nullable();
            $table->string('charge_details')->nullable();
            $table->string('delivery_info')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['periode', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
