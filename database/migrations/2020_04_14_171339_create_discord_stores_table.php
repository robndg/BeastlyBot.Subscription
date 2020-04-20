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
        Schema::create('store_discord_servers', function (Blueprint $table) {
            $table->bigInteger('guild_id')->unique();
            $table->string('url')->unique();
            $table->boolean('live')->default(false);
            $table->longText('description')->nullable();
            $table->boolean('refunds_enabled')->default(true);
            $table->integer('refunds_days')->default(7);
            $table->integer('refunds_terms')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_discord_servers');
    }
}
