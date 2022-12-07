<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comodity_id');
            $table->unsignedInteger('network_id');
            $table->char('account_no', 8);
            $table->string('code', 20)->nullable();
            $table->string('name', 60);
            $table->string('npwp', 30)->nullable();
            $table->boolean('is_taxed')->unsigned();
            $table->boolean('is_active')->unsigned()->default(1)->index();
            $table->text('pic');
            $table->date('start_date');
            $table->string('address');
            $table->unsignedTinyInteger('category_id')->default(1)->comment('Available category: 1, 2, 3')->index();
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
        Schema::dropIfExists('customers');
    }
}
