<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->boolean('admin')->default(false);
            $table->integer('color_scheme')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->bigInteger('discord_id')->after('admin')->unsigned();
            $table->foreign('discord_id')->references('id')->on('discord_o_auths');
            $table->bigInteger('stripe_id')->after('discord_id')->unsigned();
            $table->foreign('stripe_id')->references('id')->on('stripe_connects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
