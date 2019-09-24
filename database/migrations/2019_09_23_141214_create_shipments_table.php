<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('shipments')){
            Schema::create('shipments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('api_id');
                $table->integer('user_id');
                $table->string('label', 250)->nullable();
                $table->string('status', 50)->default('AWAITING');
                $table->integer('origin_id')->nullable();
                $table->integer('destination_id')->nullable();
                $table->decimal('price', 8,2);
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
        Schema::dropIfExists('shipments');
    }
}
