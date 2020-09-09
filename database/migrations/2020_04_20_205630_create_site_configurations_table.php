<?php

use App\SiteConfig;
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
        Schema::create('site_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('APP_URL')->nullable()->default('https://beastlybot.colbymchenry.com');
            $table->string('STRIPE_KEY')->nullable()->default('pk_test_KeyiJNgicDxlA6AZeaT4DHLL000VlMFTii');
            $table->string('STRIPE_SECRET')->nullable()->default('sk_test_51FF2w1HTMWe6sDFbmnYJsN6c3y1Q3TfQdrbb9JZsU8P7vzTQXFyt4Oz4E3zJGDV3Y7mn86pUgoBTfuT3hyzDJCJN00WxTHKgz0');
            $table->string('STRIPE_CLIENT_ID')->nullable()->default('ca_Fm0KaKiRMrz8QMhnKfTvM0p9x1484RzG');
            $table->string('STRIPE_WEBHOOK_SECRET')->nullable()->default('whsec_e73JNJvsEv6UFtFzwnXBqyeR1DRCfdFI');
            $table->string('STRIPE_PAYOUT_DELAY')->nullable()->default('7');
            $table->string('EXPRESS_PROD_ID')->nullable()->default('prod_GbihfSzt1nfQkg');
            $table->string('MONTHLY_PLAN')->nullable()->default('plan_GbiiDSRkOovFPF');
            $table->string('YEARLY_PLAN')->nullable()->default('plan_GbiisRXZmt3IFC');
            $table->string('SHOP_URL')->nullable()->default('/shop');
            $table->string('BOT_CONNECTION_URL')->nullable()->default('127.0.0.1:3000');
            $table->string('DISCORD_CLIENT_ID')->nullable()->default('590725202489638913');
            $table->string('DISCORD_SECRET')->nullable()->default('GFrrypnToNrfFBbFAnkBYVm_XdDlw-yP');
            $table->string('DISCORD_BOT_PERMISSIONS')->nullable()->default('281020422');
            $table->string('DISCORD_OAUTH_REDIRECT_URL')->nullable()->default('/discord_oauth');
            $table->string('DISCORD_OAUTH_SCOPE')->nullable()->default('identify%20email%20guilds%20guilds.join');
            $table->timestamps();
        });

        if(! SiteConfig::where('id', 1)->exists()) {
            $config = new SiteConfig();
            $config->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_configuration');
    }


}
