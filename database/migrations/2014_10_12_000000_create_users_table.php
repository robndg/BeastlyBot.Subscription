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
            $table->id();
            $table->bigInteger('discord_id')->unique();
            $table->boolean('admin')->default(false);
            $table->integer('theme_color')->default(0);
            $table->string('permissions')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_express_id')->nullable();
            $table->string('discord_access_token');
            $table->string('discord_refresh_token');
            $table->bigInteger('discord_token_expiration');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
