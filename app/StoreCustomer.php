<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreCustomer extends Model
{
    protected $fillable = ['id', 'user_id', 'discord_store_id', 'customer_stripe_id', 'customer_paypal_id', 'customer_cur', 'stripe_token', 'paypal_token', 'stripe_metadata', 'paypal_metadata', 'referal_code', 'enabled', 'ip_address', 'metadata'];

    public function store()
    {
        return $this->belongsTo(DiscordStore::class);
    }

    /*public function prices()
    {
        return $this->hasMany(Price::class);
    }*/ 

    protected $casts = [
        'id' => 'string',
        'metadata' => 'array',
        'stripe_metadata' => 'array',
        'paypal_metadata' => 'array',
    ];
}
