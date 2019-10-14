<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLogbooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('logbooks')) {
            Schema::create('logbooks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('user_id');
                $table->string('type', 100);
                $table->decimal('total', 8, 2);
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
        Schema::dropIfExists('table_logbooks');
    }
}
