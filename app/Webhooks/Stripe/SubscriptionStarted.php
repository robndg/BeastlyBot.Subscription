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

class SubscriptionStarted implements ShouldQueue {

    public function handle(WebhookCall $webhookCall) {
      Log::info("Subscription Created Webhook Call");
      Log::info($webhookCall->payload);

      $subscriptionId = $webhookCall->payload['data']['object']['metadata']['subscriptionId'];
      $subscriptionType = $webhookCall->payload['data']['object']['metadata']['type'];
      $sub_id = $webhookCall->payload['data']['object']['id'];
      $latest_invoice_amount = $webhookCall->payload['data']['object']['items']['data'][0]['price']['unit_amount'];
      $first_invoice_id = $webhookCall->payload['data']['object']['latest_invoice'];
      $plan_amount = $webhookCall->payload['data']['object']['plan']['amount'];

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
    
}