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
        $latest_invoice_id = $webhookCall->payload['data']['object']['charges']['data'][0]['invoice'];
        $type = $webhookCall->payload['data']['object']['lines']['data'][0]['plan']['metadata']['type'];
        $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();

        if($paid == true || $paid == 1) {
          if ($subscription->status !== Subscription::$PAID) {
            Log::info("Updating Subscription to Paid");
            $subscription->first_invoice_paid_at = $webhookCall->payload['created'];
            $subscription->status = Subscription::$PAID;
            $subscription->save();

            switch ($type) {
              case 'discord_subscription': $this->handleDiscord($subscription);
            }
          }
        }

    }

    private function handleDiscord ($subscription) {
      Log::info("Adding Role and Store Stats");

      $user_id = $subscription->user_id;
     
      $product_role = ProductRole::where('id', $subscription->product_id)->first(); // store product
      $product_price = Price::where('id', $subscription->price_id); // product price

      $discord_store = DiscordStore::where("UUID", $product_role->discord_store_id)->first();
      $store_customer = StoreCustomer::where('user_id', $user_id)->where('discord_store_id', $discord_store->id)->first();
      $store_customer->enabled = 1;
      $store_customer->save();
      //$discord_store = DiscordStore::where("guild_id", $guild_id)->first(); // might leave for store transfers

      $guild_id = $discord_store->guild_id;
      $role_id = $product_role->role_id;

      $owner_discord_id = $discord_store->user_id;
      //$owner_discord_id = DiscordOAuth::where('user_id', $owner_id)->first()->discord_id;
      $owner_id = DiscordOAuth::where('discord_id', $owner_discord_id)->first()->user_id;

      $discord_helper = new \App\DiscordHelper(User::find($owner_id));
     
      //$guild = $discord_helper->getGuild($guild_id); repeat, moved below


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

      if(!$bad_guild && !$bad_role) {

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

          $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();
        
          $customer_discord_id = DiscordOAuth::where('user_id', $user_id)->first()->discord_id;

          $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required

          // add role php

          $isMember = $discord_helper->isMember($guild->id, $discord_helper->getID());

          Log::info("Checking Member and adding if not");

          if (!$isMember) {
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

          $discord_helper->sendMessage('You have successfully subscribed to ' . $role->name . ' role in the ' . $guild->name . ' server!');

        }catch(\Exception $e) {
          Log::info($e);
          $discord_helper->sendMessage('Uh-oh! I couldn\'t add the role your account. Please contact server owner.');
              
          $discord_error = new \App\DiscordError();
              $discord_error->guild_id = $guild_id;
              $discord_error->role_id = $role_id;
              $discord_error->user_id = $user_id;
              $discord_error->message = $e->getMessage();

        }

      }
    }
    
}