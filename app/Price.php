<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['id', 'product_id', 'stripe_price_id', 'paypal_price_id', 'price', 'cur', 'interval', 'assigned_to', 'start_date', 'end_date', 'max_sales', 'discount', 'discount_end', 'discount_max', 'status', 'metadata'];

    /*public function product()
    {
        return $this->belongsTo(ProductRole::class);
    }*/

   // public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'metadata' => 'array',
        'start_time'=>'datetime:Y-m-d H:i:s',
        'end_time'=>'datetime:Y-m-d H:i:s',
        'discount_end'=>'datetime:Y-m-d H:i:s',
    ];

}
