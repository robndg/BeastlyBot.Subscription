<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

class BeastlyConfig extends Model
{
    protected $table = 'site_configuration';
    public static $keys =['STRIPE_KEY', 'STRIPE_SECRET', 'STRIPE_CLIENT_ID', 'STRIPE_WEBHOOK_SECRET', 'STRIPE_PAYOUT_DELAY', 'EXPRESS_PROD_ID', 'MONTHLY_PLAN', 'YEARLY_PLAN', 'STRIPE_CONNECT_LINK', 'SHOP_URL', 'DISCORD_AUTH_REDIRECT', 'BOT_CONNECTION_URL', 'DISCORD_CLIENT_ID', 'DISCORD_SECRET', 'DISCORD_BOT_LINK'];
  

    public static function set(string $key, $value): void {
        $site_config = !BeastlyConfig::where('id', 1)->exists() ? new BeastlyConfig() : BeastlyConfig::where('id', 1)->first();

        if(! BeastlyConfig::where('id', 1)->exists()) $site_config->save();

        if(! in_array($key, self::$keys)) throw new Exception("SiteConfiguration [ERROR]: Key " . $key . " not valid!");

        BeastlyConfig::where('id', 1)->update([$key => $value]);
    }

    public static function get(string $key) {
        $site_config = !BeastlyConfig::where('id', 1)->exists() ? new BeastlyConfig() : BeastlyConfig::where('id', 1)->first();

        if(! BeastlyConfig::where('id', 1)->exists()) $site_config->save();
        
        if(! in_array($key, self::$keys)) throw new Exception("SiteConfiguration [ERROR]: Key " . $key . " not valid!");

        return $site_config->$key;
    }

    public static function setDefaultValues() {
        self::set('STRIPE_KEY', 'pk_test_KeyiJNgicDxlA6AZeaT4DHLL000VlMFTii');
        self::set('STRIPE_SECRET', 'sk_test_CXSQrW2dFKVFf2mxpuVKrge400nlnthp2e');
        self::set('STRIPE_CLIENT_ID', 'ca_Fm0KaKiRMrz8QMhnKfTvM0p9x1484RzG');
        self::set('STRIPE_WEBHOOK_SECRET', 'whsec_e73JNJvsEv6UFtFzwnXBqyeR1DRCfdFI');
        self::set('STRIPE_PAYOUT_DELAY', '7');
        self::set('EXPRESS_PROD_ID', 'prod_GbihfSzt1nfQkg');
        self::set('MONTHLY_PLAN', 'plan_GbiiDSRkOovFPF');
        self::set('YEARLY_PLAN', 'plan_GbiisRXZmt3IFC');
        self::set('STRIPE_CONNECT_LINK', 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=http://localhost:8000&client_id=' . self::get('STRIPE_CLIENT_ID'));
        self::set('SHOP_URL', '/shop');
        self::set('DISCORD_AUTH_REDIRECT', 'http://localhost:8000/discord_oauth');
        self::set('BOT_CONNECTION_URL', '127.0.0.1');
        self::set('DISCORD_CLIENT_ID', '590725202489638913');
        self::set('DISCORD_SECRET', 'GFrrypnToNrfFBbFAnkBYVm_XdDlw-yP');
        self::set('DISCORD_BOT_LINK', 'https://discordapp.com/oauth2/authorize?client_id=590725202489638913&scope=bot&permissions=281020422');
    }

}
