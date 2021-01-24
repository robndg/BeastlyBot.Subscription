<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreCustomer extends Model
{
    protected $fillable = ['UUID', 'user_id', 'discord_store_id', 'customer_stripe_id', 'customer_paypal_id', 'customer_cur', 'stripe_token', 'paypal_token', 'stripe_metadata', 'paypal_metadata', 'enabled', 'metadata'];

    public function store()
    {
        return $this->belongsTo(DiscordStore::class);
    }

    /*public function prices()
    {
        return $this->hasMany(Price::class);
    }*/


    protected $casts = [
        'metadata' => 'array',
        'stripe_metadata' => 'array',
        'paypal_metadata' => 'array',
    ];
}
