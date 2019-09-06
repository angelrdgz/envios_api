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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 150);
                $table->string('email', 200);
                $table->integer('compnay_id')->nullable();
                $table->dateTime('email_verified_at')->nullable();
                $table->string('password', 60);
                $table->string('api_key', 250)->nullable();
                $table->integer('type_id');
                $table->boolean('business')->default(false);
                $table->string('hash', 16)->nullable();
                $table->string('photo', 250)->nullable();
                $table->string('phone', 15);
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
