<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoMigrate extends Model
{
    //
    protected $fillable = ['type', 'store_id', 'stripe_subscriptions', 'prices_meta', 'products_created', 'products_created_array', 'products_errors_array', 'prices_created', 'prices_created_array', 'prices_errors_array', 'subscriptions_created', 'subscriptions_created_array', 'subscriptions_errors_array', 'step', 'notes', 'metadata'];

    public $incrementing = true;

    protected $casts = [
        //'metadata' => 'array',
        'stripe_subscriptions' => 'array',
        'prices_meta' => 'array',
        'products_created_array' => 'array',
        'products_errors_array' => 'array',
        'prices_created_array' => 'array',
        'prices_errors_array' => 'array',
        'subscriptioneds_created_array' => 'array',
        'subscriptions_errors_array' => 'array',
        'metadata' => 'array',
      //  'start_time'=>'datetime:Y-m-d H:i:s',
    ];
}
