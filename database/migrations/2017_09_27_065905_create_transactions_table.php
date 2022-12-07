<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->char('number', 8)->unique();
            $table->unsignedInteger('invoice_id');
            $table->date('date');
            $table->boolean('in_out')->unsigned();
            $table->unsignedInteger('amount');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('handler_id')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unsignedInteger('payment_method_id')->default(1);
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
        Schema::dropIfExists('transactions');
    }
}
