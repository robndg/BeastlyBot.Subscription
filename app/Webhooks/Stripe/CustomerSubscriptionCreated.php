<?php

namespace App\Webhooks\Stripe;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;
use \App\StripeConnect;
use \App\DiscordOAuth;
use \App\NewSubscription;

use \App\ScheduledInvoicePayout;
use \App\DiscordStore;
use \App\Subscription;
use \App\Stat;
use RestCord\DiscordClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use \App\StripeHelper;

class CustomerSubscriptionCreated implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall) // get sub here and put in subscription DB (or created sub DB here) then payment intent confirms
    {

      Log::info("Customer Subscription Created Webhook");
      Log::info($webhookCall->payload);

      $subscriptionId = $webhookCall->payload['data']['object']['metadata']['subscriptionId'];
      Log::info($subscriptionId);
      $sub_id = $webhookCall->payload['data']['object']['id'];
      Log::info($sub_id);
      //$latest_invoice = $webhookCall->payload['data']['object']['items']['latest_invoice'];
      $latest_invoice_amount = $webhookCall->payload['data']['object']['items']['data'][0]['price']['unit_amount'];
      Log::info($latest_invoice_amount);
      $first_invoice_id = $webhookCall->payload['data']['object']['latest_invoice'];
      Log::info($first_invoice_id);
      $plan_amount = $webhookCall->payload['data']['object']['plan']['amount'];
      Log::info($plan_amount);

      $subscription = Cache::get($subscriptionId);

      if($subscription !== null) {
          $subscription->sub_id = $sub_id;
          $subscription->first_invoice_id = $first_invoice_id;
          $subscription->latest_invoice_id = $first_invoice_id;
          $subscription->create();
          $subscription->save();
          Cache::forget($subscriptionId);
      }
        
    }

/*

array (
  'id' => 'evt_1IFopYJJYsXa4u2SqzpLIjav',
  'object' => 'event',
  'account' => 'acct_1FmxdMJJYsXa4u2S',
  'api_version' => '2020-08-27',
  'created' => 1612133741,
  'data' => 
  array (
    'object' => 
    array (
      'id' => 'sub_IrXvtXxYmOQKGR',
      'object' => 'subscription',
      'application_fee_percent' => 4,
      'billing_cycle_anchor' => 1612133741,
      'billing_thresholds' => NULL,
      'cancel_at' => NULL,
      'cancel_at_period_end' => false,
      'canceled_at' => NULL,
      'collection_method' => 'charge_automatically',
      'created' => 1612133741,
      'current_period_end' => 1612220141,
      'current_period_start' => 1612133741,
      'customer' => 'cus_Ir9o6ATvKpAr65',
      'days_until_due' => NULL,
      'default_payment_method' => 'pm_1IFopUJJYsXa4u2SX6n2I0n5',
      'default_source' => NULL,
      'default_tax_rates' => 
      array (
      ),
      'discount' => NULL,
      'ended_at' => NULL,
      'items' => 
      array (
        'object' => 'list',
        'data' => 
        array (
          0 => 
          array (
            'id' => 'si_IrXvy7POqnyoLG',
            'object' => 'subscription_item',
            'billing_thresholds' => NULL,
            'created' => 1612133742,
            'metadata' => 
            array (
            ),
            'plan' => 
            array (
              'id' => 'price_1IFooLJJYsXa4u2SQV11mMMT',
              'object' => 'plan',
              'active' => false,
              'aggregate_usage' => NULL,
              'amount' => 2500,
              'amount_decimal' => '2500',
              'billing_scheme' => 'per_unit',
              'created' => 1612133669,
              'currency' => 'usd',
              'interval' => 'day',
              'interval_count' => 1,
              'livemode' => false,
              'metadata' => 
              array (
              ),
              'nickname' => NULL,
              'product' => 'prod_IrXujtb5iiR1i6',
              'tiers_mode' => NULL,
              'transform_usage' => NULL,
              'trial_period_days' => NULL,
              'usage_type' => 'licensed',
            ),
            'price' => 
            array (
              'id' => 'price_1IFooLJJYsXa4u2SQV11mMMT',
              'object' => 'price',
              'active' => false,
              'billing_scheme' => 'per_unit',
              'created' => 1612133669,
              'currency' => 'usd',
              'livemode' => false,
              'lookup_key' => NULL,
              'metadata' => 
              array (
              ),
              'nickname' => NULL,
              'product' => 'prod_IrXujtb5iiR1i6',
              'recurring' => 
              array (
                'aggregate_usage' => NULL,
                'interval' => 'day',
                'interval_count' => 1,
                'trial_period_days' => NULL,
                'usage_type' => 'licensed',
              ),
              'tiers_mode' => NULL,
              'transform_quantity' => NULL,
              'type' => 'recurring',
              'unit_amount' => 2500,
              'unit_amount_decimal' => '2500',
            ),
            'quantity' => 1,
            'subscription' => 'sub_IrXvtXxYmOQKGR',
            'tax_rates' => 
            array (
            ),
          ),
        ),
        'has_more' => false,
        'total_count' => 1,
        'url' => '/v1/subscription_items?subscription=sub_IrXvtXxYmOQKGR',
      ),
      'latest_invoice' => 'in_1IFopVJJYsXa4u2SATrYIPS3',
      'livemode' => false,
      'metadata' => 
      array (
        'subscriptionId' => '834975e5-0b5f-4796-839e-c551f5934035',
        'store_customer' => '788b6065-f815-420f-9d15-8fa2aad94d86',
        'product_id' => '0eb66af6-458d-4f39-a675-d0632bbe745a',
        'price_id' => 'dfba55b2-30d1-4a6a-8688-b92499229444',
        'type' => 'discord_subscription',
      ),
      'next_pending_invoice_item_invoice' => NULL,
      'pause_collection' => NULL,
      'pending_invoice_item_interval' => NULL,
      'pending_setup_intent' => NULL,
      'pending_update' => NULL,
      'plan' => 
      array (
        'id' => 'price_1IFooLJJYsXa4u2SQV11mMMT',
        'object' => 'plan',
        'active' => false,
        'aggregate_usage' => NULL,
        'amount' => 2500,
        'amount_decimal' => '2500',
        'billing_scheme' => 'per_unit',
        'created' => 1612133669,
        'currency' => 'usd',
        'interval' => 'day',
        'interval_count' => 1,
        'livemode' => false,
        'metadata' => 
        array (
        ),
        'nickname' => NULL,
        'product' => 'prod_IrXujtb5iiR1i6',
        'tiers_mode' => NULL,
        'transform_usage' => NULL,
        'trial_period_days' => NULL,
        'usage_type' => 'licensed',
      ),
      'quantity' => 1,
      'schedule' => NULL,
      'start_date' => 1612133741,
      'status' => 'incomplete',
      'transfer_data' => NULL,
      'trial_end' => NULL,
      'trial_start' => NULL,
    ),
  ),
  'livemode' => false,
  'pending_webhooks' => 1,
  'request' => 
  array (
    'id' => 'req_RXHDClQbxmbztL',
    'idempotency_key' => NULL,
  ),
  'type' => 'customer.subscription.created',
)  


{
  "id": "evt_1IFUAHJJYsXa4u2SOcuJXcNd",
  "object": "event",
  "account": "acct_1FmxdMJJYsXa4u2S",
  "api_version": "2020-08-27",
  "created": 1612054303,
  "data": {
    "object": {
      "id": "sub_IrCZDvEIhnKd7c",
      "object": "subscription",
      "application_fee_percent": 4,
      "billing_cycle_anchor": 1612054303,
      "billing_thresholds": null,
      "cancel_at": null,
      "cancel_at_period_end": false,
      "canceled_at": null,
      "collection_method": "charge_automatically",
      "created": 1612054303,
      "current_period_end": 1612140703,
      "current_period_start": 1612054303,
      "customer": "cus_Ir9o6ATvKpAr65",
      "days_until_due": null,
      "default_payment_method": "pm_1IFUAEJJYsXa4u2SLyAf3VaS",
      "default_source": null,
      "default_tax_rates": [
      ],
      "discount": null,
      "ended_at": null,
      "items": {
        "object": "list",
        "data": [
          {
            "id": "si_IrCZFa7S6wiMTd",
            "object": "subscription_item",
            "billing_thresholds": null,
            "created": 1612054303,
            "metadata": {
            },
            "plan": {
              "id": "price_1IFU9VJJYsXa4u2SEfyTTHUr",
              "object": "plan",
              "active": false,
              "aggregate_usage": null,
              "amount": 1001,
              "amount_decimal": "1001",
              "billing_scheme": "per_unit",
              "created": 1612054257,
              "currency": "usd",
              "interval": "day",
              "interval_count": 1,
              "livemode": false,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_IrCYuOmzGivjLO",
              "tiers_mode": null,
              "transform_usage": null,
              "trial_period_days": null,
              "usage_type": "licensed"
            },
            "price": {
              "id": "price_1IFU9VJJYsXa4u2SEfyTTHUr",
              "object": "price",
              "active": false,
              "billing_scheme": "per_unit",
              "created": 1612054257,
              "currency": "usd",
              "livemode": false,
              "lookup_key": null,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_IrCYuOmzGivjLO",
              "recurring": {
                "aggregate_usage": null,
                "interval": "day",
                "interval_count": 1,
                "trial_period_days": null,
                "usage_type": "licensed"
              },
              "tiers_mode": null,
              "transform_quantity": null,
              "type": "recurring",
              "unit_amount": 1001,
              "unit_amount_decimal": "1001"
            },
            "quantity": 1,
            "subscription": "sub_IrCZDvEIhnKd7c",
            "tax_rates": [
            ]
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/subscription_items?subscription=sub_IrCZDvEIhnKd7c"
      },
      "latest_invoice": "in_1IFUAFJJYsXa4u2S7SAvX8hA",
      "livemode": false,
      "metadata": {
        "subscriptionId": "ab64c4c4-afcb-48d2-ba20-25e7d6ddeb51",
        "store_customer": "788b6065-f815-420f-9d15-8fa2aad94d86",
        "product_id": "22ddb920-9921-48ff-823b-95eb5e0970d2",
        "price_id": "1b08c491-5a73-4bb3-b07e-5c476ec8018d",
        "type": "discord_subscription"
      },
      "next_pending_invoice_item_invoice": null,
      "pause_collection": null,
      "pending_invoice_item_interval": null,
      "pending_setup_intent": null,
      "pending_update": null,
      "plan": {
        "id": "price_1IFU9VJJYsXa4u2SEfyTTHUr",
        "object": "plan",
        "active": false,
        "aggregate_usage": null,
        "amount": 1001,
        "amount_decimal": "1001",
        "billing_scheme": "per_unit",
        "created": 1612054257,
        "currency": "usd",
        "interval": "day",
        "interval_count": 1,
        "livemode": false,
        "metadata": {
        },
        "nickname": null,
        "product": "prod_IrCYuOmzGivjLO",
        "tiers_mode": null,
        "transform_usage": null,
        "trial_period_days": null,
        "usage_type": "licensed"
      },
      "quantity": 1,
      "schedule": null,
      "start_date": 1612054303,
      "status": "incomplete",
      "transfer_data": null,
      "trial_end": null,
      "trial_start": null
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_yL013iibL0GF7T",
    "idempotency_key": null
  },
  "type": "customer.subscription.created"
}
*/
    
}