<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 30)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->string('api_token')->nullable();
            $table->string('name', 60)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('gender_id')->unsigned()->nullable();
            $table->tinyInteger('role_id')->unsigned()->nullable();
            $table->unsignedInteger('network_id')->unsigned()->nullable();
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
        Schema::drop('users');
    }
}
