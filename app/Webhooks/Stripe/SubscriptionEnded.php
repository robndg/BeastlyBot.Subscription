<?php

namespace App\Webhooks\Stripe;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

use RestCord\DiscordClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use App\User;
use App\Subscription;
use App\StripeConnect;
use App\StripeHelper;
use App\DiscordStore;
use App\PaidOutInvoice;
use App\Stat;

class SubscriptionEnded implements ShouldQueue {

    public function handle(WebhookCall $webhookCall) {
        Log::info("Subscription Ended Webhook Call");
        Log::info($webhookCall->payload);

        $sub_id = $webhookCall->payload['data']['object']['id'];
        $plan_id = $webhookCall->payload['data']['object']['items']['data'][0]['plan']['id'];
        $customer = $webhookCall->payload['data']['object']['customer'];
        $customer_id = StripeConnect::where('customer_id', $customer)->first()->user_id;
        $partner_id = $webhookCall->payload['data']['object']['items']['data'][0]['plan']['metadata']['user_id'];
        $subscriptionType = $webhookCall->payload['data']['object']['metadata']['type'];

        try {
            $subscription = Subscription::where('id', $subscription_id)->first();
            $subscription->status = Subscription::$CANCELLED;
            $subscription->save();
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            Log::info("Sub canceled (4) did not save in DB: ", $subscription_id);
        }
    
        switch ($subscriptionType) {
            case 'discord_subscription': $this->handleDiscord($customer_id, $partner_id, $sub_id);
        }
        
    }

    private function handleDiscord ($customer_id, $partner_id, $subscription_id) {
        $customer_discord_id = DiscordOAuth::where('user_id', $customer_id)->first()->discord_id;
        $partner_discord_id = DiscordOAuth::where('user_id', $customer_id)->first()->discord_id;
        $discord_helper = new \App\DiscordHelper(\App\User::where('id', $customer_id)->first());
        
        Cache::forget('customer_subscriptions_active_' . $customer_id);
        Cache::forget('customer_subscriptions_canceled_' . $customer_id);

        try {
            $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
            $discord_client->guild->removeGuildMemberRole([
                'guild.id' => intval($guild_id),
                'role.id' => intval($role_id),
                'user.id' => intval($customer_discord_id)
            ]);

            $guild = $discord_helper->getGuild($guild_id);
            $role = $discord_helper->getRole($guild_id, $role_id);

            $discord_helper->sendMessage('Your subscription to the ' . $role->name . ' role in the ' . $guild->name . ' server expired. I removed the role from your account.');

            try {
                $discord_store = DiscordStore::where("guild_id", $guild_id)->first();
                $stats = Stat::where('type', 1)->where('type_id', $discord_store->id)->first();
                $subscribers_active = $stats->data['subscribers']['active'];
                $subscribers_total = $stats->data['subscribers']['total'];
                $stats_data = $stats->data;
                $stats_data['subscribers'] = ['active' => $subscribers_active - 1, 'total' => $subscribers_total];
                $stats->data = $stats_data;
                $stats->save();
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
                Log::info("Sub canceled (4) did not save in DB: ", $subscription_id);
            }

        } catch(\Exception $e) {
            $discord_helper->sendMessage('Uh-oh! Something went wrong in removing the role from your account. Don\'t worry, I still canceled the subscription for you.');
            Log::error($e);
        }
    }
}