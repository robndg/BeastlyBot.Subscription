<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referrals extends Model
{
    protected $fillable = ['id', 'user_id', 'store_type', 'store_id', 'referrer_customer_id', 'purchaser_customer_id', 'subscription_id', 'referral_code', 'refund_amount', 'refund_sub_id', 'refund_invoice_id', 'paid', 'override', 'count', 'UUID'];

    public $incrementing = true;
            
    protected $casts = [
        'subscription_id' => 'string',
        'UUID' => 'string',
    ];
}
