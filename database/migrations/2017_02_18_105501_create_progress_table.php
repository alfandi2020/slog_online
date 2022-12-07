<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('receipt_id')->index();
            $table->unsignedInteger('manifest_id')->nullable()->index();
            $table->string('notes')->nullable();
            $table->unsignedInteger('creator_id')->index();
            $table->string('creator_location_id', 7);
            $table->char('start_status', 2);
            $table->unsignedInteger('handler_id')->index()->nullable();
            $table->string('handler_location_id', 7)->nullable();
            $table->char('end_status', 2)->nullable();
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
        Schema::dropIfExists('receipt_progress');
    }
}
