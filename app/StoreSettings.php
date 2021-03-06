<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreSettings extends Model
{
    //

    protected $fillable = ['store_type', 'store_id', 'store_image', 'store_name', 'url_slug', 'description', 'about', 'members_only', 'welcome_message', 'welcome_message_settings', 'refunds_enabled', 'refunds_terms', 'refunds_days', 'recurring_referrals', 'referral_percent_fee', 'cancel_subscriptions_on_exit', 'disable_public_downgrades', 'terms_of_service', 'premium', 'remove_network', 'main_color', 'secondary_color', 'show_beastly', 'eyes_color', 'allow_featured', 'metadata'];

    public function discord_store()
    {
        return $this->belongsTo(DiscordStore::class);
    }
    
}