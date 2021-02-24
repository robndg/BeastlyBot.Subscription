<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processors', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->integer('user_id')->nullable(); // for us maybe
            $table->integer('store_id')->nullable(); // most likely filled
            $table->integer('type')->default(1); //1 stripe // 2 paypal // 3 expansion
            $table->string('processor_id')->nullable(); // if type 1: connect_id (stripe)
            $table->string('cur')->default('USD');
            $table->string('enabled')->default(1);
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
        Schema::dropIfExists('processors');
    }
}
