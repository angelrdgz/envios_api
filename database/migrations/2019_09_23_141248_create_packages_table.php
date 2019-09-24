<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id');
                $table->string('name', 100);
                $table->string('type', 100);
                $table->boolean('status')->default(1);
                $table->decimal('height', 6,2);
                $table->decimal('width', 6,2);
                $table->decimal('length', 6,2);
                $table->decimal('weight', 6,2);
                $table->string('contents', 100);
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
        Schema::dropIfExists('packages');
    }
}
