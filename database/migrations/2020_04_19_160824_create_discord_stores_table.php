<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscordStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discord_stores', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->string('url')->unique();
            $table->boolean('testing')->default(false);
            $table->longText('description')->nullable();
            $table->boolean('refunds_enabled')->default(true);
            $table->integer('refunds_days')->default(7);
            $table->integer('refunds_terms')->default(1);
            $table->timestamps();
        });

        Schema::table('discord_stores', function($table) {
            $table->bigInteger('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discord_stores');
    }
}
