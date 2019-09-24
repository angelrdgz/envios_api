<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('locations')){
            Schema::create('locations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id');
                $table->integer('type_id');
                $table->text('address');
                $table->text('address2');
                $table->string('city',80);
                $table->string('state',50);
                $table->string('country',50);
                $table->string('zipcode',6);
                $table->text('reference');
                $table->string('nickname',120);
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('locations');
    }
}
