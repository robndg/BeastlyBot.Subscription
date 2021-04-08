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
use \App\Refferals;
use \App\StoreCustomer;
use \App\StoreSettings;

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
      $referral_code = null;
      $referral_code = $webhookCall->payload['data']['object']['metadata']['referralCode'];
      Log::info($referral_code);

      $subscription = Cache::get($subscriptionId);

      if($subscription !== null) {
          $subscription->sub_id = $sub_id;
          $subscription->first_invoice_id = $first_invoice_id;
          $subscription->latest_invoice_id = $first_invoice_id;
          $subscription->create();
          $subscription->save();

          /*if($referral_code != null){
            try{
              if(StoreCustomer::where('referral_code', $referral_code)->exists()){
                $product_price = Price::where('price_id', $subscription->price_id)->first();
                $store = DiscordStore::where('UUID', $product_price->store_id)->first();
                $store_settings = StoreSettings::where('store_id', $product_price->store_id)->first();
                $referrer_customer = StoreCustomer::where('referral_code', $referral_code)->where('discord_store_id',$store->id)->first();
                $purchaser_customer = StoreCustomer::where('store_customer', $subscription->user_id)->where('discord_store_id',$store->id)->first();
                
                if($referrer_customer->id != $purchaser_customer){
                  Log::info('Refferer and Purchaser cannot get referral');
                }else{
                
                  $refund_sub_id = $subscription->sub_id;
                  //$subscription_refund_invoice_id = Subscription::where('user_id', )->where('store_id')->where('') // fill in with Cron which invoice to payout
  
                  $refferal = new Refferal([
                    'user_id' => $referrer_customer->user_id,
                    'store_type' => 1,
                    'store_id' => $store->id,
                    'referrer_customer_id' => $referrer_customer->id,
                    'purchaser_customer_id' =>  $purchaser_customer->id,
                    'subscription_id' => $subscription->id,
                    'referral_code' => $ref_code,
                    'refund_amount' => ($product_price->price * 0.95) * ($store_settings->referral_percent_fee),
                    'refund_sub_id' => $referrer_customer->customer_stripe_id,
                    'refund_invoice_id' => NULL, // cron
                    'paid' => NULL, // cron
                    'override' => 0,
                    'count' => 0,
                    'UUID' => $subscriptionId, // cron copies if store setting recur, creates new table
                  ]);
                  $refferal->create();
                }
  
              }
            }catch (Exception $e){
              Log::info($e);
              Cache::forget($subscriptionId);
            }
        }*/

        Cache::forget($subscriptionId);
          
      }

      

    }
    
}