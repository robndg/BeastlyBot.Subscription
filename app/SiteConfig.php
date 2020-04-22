<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteConfig extends Model
{
    protected $table = 'site_configuration';

    public static function keys() {
        return Schema::getColumnListing('site_configuration');
    }

    public static function set(string $key, $value): void {
        $site_config = !SiteConfig::where('id', 1)->exists() ? new SiteConfig() : SiteConfig::where('id', 1)->first();

        if(! SiteConfig::where('id', 1)->exists()) $site_config->save();

        if(! in_array($key, self::keys())) throw new Exception("SiteConfiguration [ERROR]: Key " . $key . " not valid!");

        SiteConfig::where('id', 1)->update([$key => $value]);
    }

    public static function get(string $key) {
        $site_config = !SiteConfig::where('id', 1)->exists() ? new SiteConfig() : SiteConfig::where('id', 1)->first();

        if(! SiteConfig::where('id', 1)->exists()) $site_config->save();
        
        if(! in_array($key, self::keys())) throw new Exception("SiteConfiguration [ERROR]: Key " . $key . " not valid!");

        return $site_config->$key;
    }

}
