<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

class BeastlyConfig extends Model
{
    protected $table = 'site_configuration';
    public static $keys =['STRIPE_KEY', 'STRIPE_SECRET', 'STRIPE_CLIENT_ID', 'STRIPE_WEBHOOK_SECRET', 'STRIPE_PAYOUT_DELAY', 'EXPRESS_PROD_ID', 'MONTHLY_PLAN', 'YEARLY_PLAN', 'STRIPE_CONNECT_LINK', 'SHOP_URL', 'DISCORD_AUTH_REDIRECT', 'BOT_CONNECTION', 'DISCORD_CLIENT_ID', 'DISCORD_SECRET', 'DISCORD_BOT_LINK'];
  

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

}
