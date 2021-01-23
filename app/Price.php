<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['UUID', 'product_id', 'stripe_price_id', 'paypal_price_id', 'price', 'cur', 'interval', 'assigned_to', 'start_date', 'end_date', 'max_sales', 'discount', 'discount_end', 'discount_max', 'status', 'metadata'];

    public function product()
    {
        return $this->belongsTo(ProductRole::class);
    }
}
