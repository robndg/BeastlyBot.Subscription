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
            $table->bigInteger('guild_id')->unique();
            $table->string('user_id');
            $table->string('url')->unique();
<<<<<<< HEAD
            $table->string('UUID')->unique();
=======
>>>>>>> 4291d4b085eb72c32defec48d457d73779fce67a
            $table->string('payment_processor')->default(1);
            $table->boolean('live')->default(false);
            $table->longText('description')->nullable();
            $table->boolean('refunds_enabled')->default(true);
            $table->integer('refunds_days')->default(7);
            $table->integer('refunds_terms')->default(1);
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
