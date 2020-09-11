<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

class SubscriptionExpired implements ShouldQueue
{
    // TODO: TEST
    public function handle(WebhookCall $webhookCall)
    {
    }

    // {
    //     "id": "evt_1HQGQCHTMWe6sDFbag1abfoj",
    //     "object": "event",
    //     "api_version": "2019-08-14",
    //     "created": 1599846746,
    //     "data": {
    //       "object": {
    //         "id": "sub_I0GyJhJrdhdlV8",
    //         "object": "subscription",
    //         "application_fee_percent": null,
    //         "billing": "charge_automatically",
    //         "billing_cycle_anchor": 1599846746,
    //         "billing_thresholds": null,
    //         "cancel_at": null,
    //         "cancel_at_period_end": false,
    //         "canceled_at": null,
    //         "collection_method": "charge_automatically",
    //         "created": 1599846746,
    //         "current_period_end": 1602438746,
    //         "current_period_start": 1599846746,
    //         "customer": "cus_Hzejn102gol501",
    //         "days_until_due": null,
    //         "default_payment_method": "pm_1HQGQ9HTMWe6sDFbiNBzsCNO",
    //         "default_source": null,
    //         "default_tax_rates": [
    //         ],
    //         "discount": null,
    //         "ended_at": null,
    //         "invoice_customer_balance_settings": {
    //           "consume_applied_balance_on_void": true
    //         },
    //         "items": {
    //           "object": "list",
    //           "data": [
    //             {
    //               "id": "si_I0GyVmbmxD6YwT",
    //               "object": "subscription_item",
    //               "billing_thresholds": null,
    //               "created": 1599846746,
    //               "metadata": {
    //               },
    //               "plan": {
    //                 "id": "discord_590728038849708043_596078383906029590_1_r",
    //                 "object": "plan",
    //                 "active": true,
    //                 "aggregate_usage": null,
    //                 "amount": 1000,
    //                 "amount_decimal": "1000",
    //                 "billing_scheme": "per_unit",
    //                 "created": 1599704617,
    //                 "currency": "usd",
    //                 "interval": "month",
    //                 "interval_count": 1,
    //                 "livemode": false,
    //                 "metadata": {
    //                   "user_id": "1"
    //                 },
    //                 "nickname": null,
    //                 "product": "discord_590728038849708043_596078383906029590",
    //                 "tiers": null,
    //                 "tiers_mode": null,
    //                 "transform_usage": null,
    //                 "trial_period_days": null,
    //                 "usage_type": "licensed"
    //               },
    //               "price": {
    //                 "id": "discord_590728038849708043_596078383906029590_1_r",
    //                 "object": "price",
    //                 "active": true,
    //                 "billing_scheme": "per_unit",
    //                 "created": 1599704617,
    //                 "currency": "usd",
    //                 "livemode": false,
    //                 "lookup_key": null,
    //                 "metadata": {
    //                   "user_id": "1"
    //                 },
    //                 "nickname": null,
    //                 "product": "discord_590728038849708043_596078383906029590",
    //                 "recurring": {
    //                   "aggregate_usage": null,
    //                   "interval": "month",
    //                   "interval_count": 1,
    //                   "trial_period_days": null,
    //                   "usage_type": "licensed"
    //                 },
    //                 "tiers_mode": null,
    //                 "transform_quantity": null,
    //                 "type": "recurring",
    //                 "unit_amount": 1000,
    //                 "unit_amount_decimal": "1000"
    //               },
    //               "quantity": 1,
    //               "subscription": "sub_I0GyJhJrdhdlV8",
    //               "tax_rates": [
    //               ]
    //             }
    //           ],
    //           "has_more": false,
    //           "total_count": 1,
    //           "url": "/v1/subscription_items?subscription=sub_I0GyJhJrdhdlV8"
    //         },
    //         "latest_invoice": "in_1HQGQAHTMWe6sDFbJ86OHH5l",
    //         "livemode": false,
    //         "metadata": {
    //         },
    //         "next_pending_invoice_item_invoice": null,
    //         "pause_collection": null,
    //         "pending_invoice_item_interval": null,
    //         "pending_setup_intent": null,
    //         "pending_update": null,
    //         "plan": {
    //           "id": "discord_590728038849708043_596078383906029590_1_r",
    //           "object": "plan",
    //           "active": true,
    //           "aggregate_usage": null,
    //           "amount": 1000,
    //           "amount_decimal": "1000",
    //           "billing_scheme": "per_unit",
    //           "created": 1599704617,
    //           "currency": "usd",
    //           "interval": "month",
    //           "interval_count": 1,
    //           "livemode": false,
    //           "metadata": {
    //             "user_id": "1"
    //           },
    //           "nickname": null,
    //           "product": "discord_590728038849708043_596078383906029590",
    //           "tiers": null,
    //           "tiers_mode": null,
    //           "transform_usage": null,
    //           "trial_period_days": null,
    //           "usage_type": "licensed"
    //         },
    //         "quantity": 1,
    //         "schedule": null,
    //         "start": 1599846746,
    //         "start_date": 1599846746,
    //         "status": "incomplete",
    //         "tax_percent": null,
    //         "transfer_data": null,
    //         "trial_end": null,
    //         "trial_start": null
    //       }
    //     },
    //     "livemode": false,
    //     "pending_webhooks": 3,
    //     "request": {
    //       "id": "req_82cS4VMHkr2BYR",
    //       "idempotency_key": null
    //     },
    //     "type": "customer.subscription.created"
    //   }
}