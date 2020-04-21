<?php

use App\BeastlyConfig;
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
            foreach(BeastlyConfig::$keys as $key) $table->string($key)->nullable();
            $table->timestamps();
        });

        if(! BeastlyConfig::where('id', 1)->exists()) self::setDefaultValues();
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

    private static function setDefaultValues() {
        BeastlyConfig::set('STRIPE_KEY', 'pk_test_KeyiJNgicDxlA6AZeaT4DHLL000VlMFTii');
        BeastlyConfig::set('STRIPE_SECRET', 'sk_test_CXSQrW2dFKVFf2mxpuVKrge400nlnthp2e');
        BeastlyConfig::set('STRIPE_CLIENT_ID', 'ca_Fm0KaKiRMrz8QMhnKfTvM0p9x1484RzG');
        BeastlyConfig::set('STRIPE_WEBHOOK_SECRET', 'whsec_e73JNJvsEv6UFtFzwnXBqyeR1DRCfdFI');
        BeastlyConfig::set('STRIPE_PAYOUT_DELAY', '7');
        BeastlyConfig::set('EXPRESS_PROD_ID', 'prod_GbihfSzt1nfQkg');
        BeastlyConfig::set('MONTHLY_PLAN', 'plan_GbiiDSRkOovFPF');
        BeastlyConfig::set('YEARLY_PLAN', 'plan_GbiisRXZmt3IFC');
        BeastlyConfig::set('STRIPE_CONNECT_LINK', 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=http://localhost:8000&client_id=' . BeastlyConfig::get('STRIPE_CLIENT_ID'));
        BeastlyConfig::set('SHOP_URL', '/shop');
        BeastlyConfig::set('DISCORD_AUTH_REDIRECT', 'http://localhost:8000/discord_oauth');
        BeastlyConfig::set('BOT_CONNECTION_URL', '127.0.0.1');
        BeastlyConfig::set('DISCORD_CLIENT_ID', '590725202489638913');
        BeastlyConfig::set('DISCORD_SECRET', 'GFrrypnToNrfFBbFAnkBYVm_XdDlw-yP');
        BeastlyConfig::set('DISCORD_BOT_LINK', 'https://discordapp.com/oauth2/authorize?client_id=590725202489638913&scope=bot&permissions=281020422');
    }

}
