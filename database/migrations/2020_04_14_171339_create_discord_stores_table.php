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
            $table->id();
            $table->string('guild_id')->unique();
            $table->string('user_id');
            $table->string('url')->unique();
            $table->string('UUID')->unique();
            $table->string('payment_processor')->default(1);
            $table->integer('processor_id')->nullable(); // add in processor id (could be stripe processir, could be paypal processor) for now user_id only (not guild specific)
            $table->boolean('live')->default(false);
            $table->longText('description')->nullable();
            $table->boolean('bot_active')->default(true);
            $table->integer('level')->default(1);
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
        Schema::dropIfExists('discord_stores');
    }
}
