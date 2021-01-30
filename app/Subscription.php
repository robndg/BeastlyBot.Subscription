<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['id', 'connection_type', 'session_id', 'sub_id', 'user_id', 'owner_id', 'store_id', 'store_customer_id', 'product_id', 'price_id', 'first_invoice_id', 'first_invoice_price', 'first_invoice_paid_at', 'next_invoice_price', 'latest_invoice_id', 'latest_invoice_amount', 'app_fee', 'status', 'visible', 'metadata'];
    //public $incrementing = false;
    //public $primaryKey = 'id';
    //public $timestamps = true;

    protected $casts = [
        'id' => 'string',
        'metadata' => 'array',
        'first_invoice_paid_at'=>'datetime:Y-m-d H:i:s',
    ];

}
