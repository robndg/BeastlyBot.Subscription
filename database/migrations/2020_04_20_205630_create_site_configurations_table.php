<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('STRIPE_KEY')->nullable();
            $table->string('STRIPE_SECRET')->nullable();
            $table->string('STRIPE_CLIENT_ID')->nullable();
            $table->string('STRIPE_WEBHOOK_SECRET')->nullable();
            $table->string('STRIPE_PAYOUT_DELAY_DAYS')->nullable();
            $table->string('EXPRESS_PRODUCT_ID')->nullable();
            $table->string('EXPRESS_MONTHLY_PLAN')->nullable();
            $table->string('EXPRESS_YEARLY_PLAN')->nullable();
            $table->string('STRIPE_CONNECT_LINK')->nullable();
            $table->string('DISCORD_AUTH_REDIRECT')->nullable();
            $table->string('BOT_CONNECTION_URL')->nullable();
            $table->string('DISCORD_CLIENT_ID')->nullable();
            $table->string('DISCORD_SECRET')->nullable();
            $table->string('DISCORD_BOT_LINK')->nullable();
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
        Schema::dropIfExists('site_configurations');
    }

}
