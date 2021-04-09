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

use \App\User;
use \App\Ban;
use \App\DiscordHelper;
use \App\StoreCustomer;
use \App\ProductRole;
use \App\Price;

class PaymentIntentSucceeded implements ShouldQueue {

    public function handle(WebhookCall $webhookCall) {
        Log::info("PaymentIntentSucceeded Webhook Call");
        Log::info($webhookCall->payload);
        
        $paid = $webhookCall->payload['data']['object']['charges']['data'][0]['paid'];
        $latest_invoice_id = $webhookCall->payload['data']['object']['invoice'];
        //$type = $webhookCall->payload['data']['object']['lines']['data'][0]['plan']['metadata']['type'];
        $type = 'discord_subscription'; // TODO Colby, error "lines" -> has no type

      
        $iteration_counter = 0;
        $continue = false;
        if(Subscription::where('latest_invoice_id', $latest_invoice_id)->exists()){
          $continue = true;
          $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();
        }else{
          $continue = false; // TODO: make this better code, useing timers or getting notice from SubscriptionStarted the table has been made
          while($iteration_counter <= 31 && $continue == false){
            try{
              $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();
            }catch(\Exception $e) {
              $iteration_counter += 1;
              Log::info("Iterating to find Subscription Call to continue");
              Log::info($iteration_counter);
              if($iteration_counter > 30){
                $continue = "fail";
                Log::info("Failed to find Subscription for Call");
              }
            }
          }
        }
        
        if($continue == true){ // found subscription row
          if($paid == true || $paid == 1) {
            if ($subscription->status !== Subscription::$PAID) {
              Log::info("Updating Subscription to Paid");
              $subscription->first_invoice_paid_at = $webhookCall->payload['created'];
              $subscription->status = Subscription::$PAID;
              $subscription->save();

              Log::info($type);
              Log::info($subscription);
              switch ($type) {
                case 'discord_subscription': $this->handleDiscord($subscription);
              }
            }
          }
        }
  
        

    }

    private function handleDiscord ($subscription) {
      Log::info("subscription handleDiscord");
      Log::info($subscription);

      $user_id = $subscription->user_id;
      $discord_helper_user = new \App\DiscordHelper(User::find($user_id));
      Log::info("user_id handleDiscord");
      Log::info($user_id);
     
      $product_role = ProductRole::where('id', $subscription->product_id)->first(); // store product
      $product_price = Price::where('id', $subscription->price_id); // product price

      $discord_store = DiscordStore::where("UUID", $product_role->discord_store_id)->first();
      $store_customer = StoreCustomer::where('user_id', $user_id)->where('discord_store_id', $discord_store->id)->first();
      $store_customer->enabled = 1;
      $store_customer->save();
      

      $guild_id = $discord_store->guild_id;
      Log::info($guild_id);
      
      Log::info("Here 1");
      $role_id = $product_role->role_id;
      Log::info("Here 2");
      $owner_id = $discord_store->user_id;
      Log::info("Here 3");
      Log::info($owner_id);
      $discord_helper = new \App\DiscordHelper(User::find($owner_id));
      Log::info("Here 4");

      //$owner = DiscordOAuth::where('discord_id', $owner_discord_id)->first();
      //$owner_id = $owner->id;
      //$owner_discord = DiscordOAuth::where('user_id', $owner_id)->first();
      $owner_discord_id = $subscription->owner_id;
      Log::info("Here 5");
      //$guild = $discord_helper->getGuild($guild_id);// repeat, moved below


      $bad_guild = false;
      $bad_role = false;
      /*
      Make sure the guild exists. If not cancel and refund
      */
      try {
          $guild = $discord_helper->getGuild($guild_id);
          if($guild == null) {
            $bad_guild = true;
          }
      } catch (\Exception $e) {
          Log::info($e);
          $bad_guild = true;
      }
      /*
      Make sure the role exists. If not cancel and refund
      */
      try {
          $role = $discord_helper->getRole($guild_id, $role_id);
          if($role == null) {
              $bad_role = true;
          }
      } catch (\Exception $e) {
          Log::info($e);
          $bad_role = true;
      }
      Log::info("Here 6");
      if(!$bad_guild && !$bad_role) {
        Log::info("Adding Role and Store Stats");
        $stats = Stat::where('type', 1)->where('type_id', $discord_store->id)->first();
        $subscribers_active = $stats->data['subscribers']['active'];
        $subscribers_total = $stats->data['subscribers']['total'];
        $stats_data = $stats->data;
        $stats_data['subscribers'] = ['active' => $subscribers_active + 1, 'total' => $subscribers_total + 1];
        $stats->data = $stats_data;
        $stats->save();

        if($subscribers_active >= 100){
            $level = 1;
        }elseif($subscribers_active >= 10){
            $level = 2;
        }else{
            $level = 3;
        }
        $discord_store->level = $level;
        $discord_store->save();

        try{

          //$subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();
          Log::info("User_Id");
          Log::info($user_id);
          Log::info("Getting Customer Discord Id");
          
          $customer_discord_id = DiscordOAuth::where('user_id', $user_id)->first()->discord_id;

          $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required

          // add role php
          Log::info("Checking Member and adding if not");

          $isMember = $discord_helper_user->isMember($guild->id, $customer_discord_id);
          Log::info($isMember);

          

          if (!$isMember) { //TODO2: this area results in error
            $discord_client->guild->addGuildMember([
              'guild.id' => intval($guild_id),
              'user.id' => intval($customer_discord_id)
            ]);
          }

          $discord_client->guild->addGuildMemberRole([
              'guild.id' => intval($guild_id),
              'role.id' => intval($role_id),
              'user.id' => intval($customer_discord_id)
          ]);

          $subscription->status = 2;
          $subscription->save();

          //$discord_helper->sendMessage('You have successfully subscribed to ' . $role->name . ' role in the ' . $guild->name . ' server!');

        }catch(\Exception $e) {
          Log::info($e);
          $discord_helper_user->sendMessage('Uh-oh! I couldn\'t add the role your account. Please contact server owner.');
          Log::info("Discord Error");
          Log::info($e->getMessage());
          Log::info($guild_id);
          Log::info($role_id);
          Log::info($user_id);
          
         /* $discord_error = new \App\DiscordError();
              $discord_error->guild_id = $guild_id;
              $discord_error->role_id = $role_id;
              $discord_error->user_id = $user_id;
              $discord_error->message = $e->getMessage();*/

        }


      }
    }
    
}