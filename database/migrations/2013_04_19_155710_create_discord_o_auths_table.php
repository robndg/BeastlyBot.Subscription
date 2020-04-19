<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscordOAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discord_o_auths', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->bigIncrements('id')->unsigned();
            $table->string('access_token')->unique();
            $table->string('refresh_token')->unique();
            $table->bigInteger('token_expiration');
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
        Schema::dropIfExists('discord_o_auths');
    }
}
