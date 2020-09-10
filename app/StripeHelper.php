<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Subscription;

class StripeHelper
{

    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function getSubscriptions() {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        // TODO: Session Cache not working here
        // if(Session::has('subs_' . $this->user->DiscordOAuth->discord_id)) return Session::get('subs_' . $this->user->DiscordOAuth->discord_id);

        $stripe_subs = \Stripe\Subscription::all(['customer' => $this->user->StripeConnect->customer_id, 'status' => 'active']);

        Session::put('subs_' . $this->user->DiscordOAuth->discord_id, $stripe_subs);
        return $stripe_subs;
    }

    public function getSubscriptionsList() {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        if(Session::has('subs_list_' . $this->user->DiscordOAuth->discord_id)) return Session::get('subs_list_' . $this->user->DiscordOAuth->discord_id);

        $stripe_subs = \Stripe\Subscription::all(['customer' => $this->user->StripeConnect->customer_id, 'status' => 'active']);

        $stripe_subs_list = array();

        foreach ($stripe_subs as $sub) {
            $continue = false;
            if($sub->status != 'expired'){
                try{
                    try{
                    $sub_id = explode('_', $sub->id)[0];
                    $sub_end_date = $sub->current_period_end;

                    $plan = $sub->plan[0];
                    
                    $continue = true;
                    }catch(Exception $e){
                        Log::error($e);
                    }
                    if($continue == true && ($sub->items->data[0]['plan']->product != SiteConfig::get('EXPRESS_PROD_ID'))){

                        $plan_product_full = $sub->items->data[0]['plan']->product;
                        $plan_guild_id = $plan_product_full;
                        try{
                        $plan_guild_id = explode('_', $plan_product_full)[0];
                        }catch(Exception $e){
                            Log::error($e);
                        }
                        $plan_role_id = $plan_product_full;
                        try{
                        $plan_role_id = explode('_', $plan_product_full)[1];
                        }catch(Exception $e){
                            Log::error($e);
                        }
                        $plan_role_nickname_full = $sub->items->data[0]['plan']->nickname;
                        $plan_role_nickname = $plan_role_nickname_full;
                        try{
                        $plan_role_nickname = explode(' - ', $plan_role_nickname)[0];
                        }catch(Exception $e){
                            Log::error($e);
                        }

                        $sub->sub_id = $sub_id; #sub_id
                        $sub->guild_id = $plan_guild_id; #guild_id->guild_name
                        $sub->role_id = $plan_role_id; #guild_id->guild_name
                        $sub->role_name = $plan_role_nickname; #role_name
                        $sub->role_color = "3e8ef7";
                        #$sub->plan_amount = $plan_amount; #role_name
                        $sub->end_date = $sub_end_date; #end date
                        
                        array_push($stripe_subs_list, $sub);
                    }
                }catch(Exception $e){
                    Log::error($e);
                }
            }
        }

        Session::put('subs_list_' . $this->user->DiscordOAuth->discord_id, $stripe_subs_list);
        return $stripe_subs_list;
    }

    public function isSubscribedToProduct(string $id): bool {
        foreach ($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->product == $id) return true;
        }

        return false;
    }

    public function getSubscriptionForProduct(string $id): \Stripe\Subscription {
        foreach ($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->product == $id) return $subscription;
        }
        return null;
    }

    public function isSubscribedToPlan(string $id): bool {
        foreach ($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->id == $id) return true;
        }

        return false;
    }

    public function getSubscriptionForPlan(string $id): \Stripe\Subscription {
        foreach ($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->id == $id) return $subscription;
        }
        return null;
    }

    public function getStripeEmail(): string {
        if (Session::has('stripe_email')) return Session::get('stripe_email');
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        try {
            Session::put('stripe_email', $this->getCustomerAccount()->email);
            return Session::get('stripe_email');
        } catch (ApiErrorException $e) {
        }

        return null;
    }

    public function getCustomerAccount(): \Stripe\Customer {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        try {
            return \Stripe\Customer::retrieve($this->user->StripeConnect->customer_id);
        } catch (ApiErrorException $e) {
        }
        return null;
    }

    public function isSubscriptionMonthly(): bool {
        $active_plan = $this->getExpressSubscription();
        return $active_plan !== null && $active_plan->id == SiteConfig::get('MONTHLY_PLAN');
    }

    public function isSubscriptionYearly(): bool {
        $active_plan = $this->getExpressSubscription();
        return $active_plan !== null && $active_plan->id == SiteConfig::get('YEARLY_PLAN');
    }

    public function getExpressSubscription() {
        foreach($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->product === SiteConfig::get('EXPRESS_PROD_ID'))  return $subscription;
        }

        return null;
    }

    public function isExpressUser(): bool {
        return $this->user->StripeConnect->express_id !== null;
    }

    public function hasExpressPlan(): bool {
        return $this->getExpressSubscription() != null;
    }

    public function hasActiveExpressPlan(): bool {
        $subscription = $this->getExpressSubscription();
        return $subscription != null && $subscription->status == 'active';
    }

    public function getBalance() {
        if(Cache::has('balance_' . $this->user->StripeConnect->express_id)) return Cache::get('balance_' . $this->user->StripeConnect->express_id, 0);
        $balance = \Stripe\Balance::retrieve(
            ['stripe_account' => $this->user->StripeConnect->express_id]
        );
        Cache::put('balance_' . $this->user->StripeConnect->express_id, $balance, 60 * 5); // 5 minutes
        return Cache::get('balance_' . $this->user->StripeConnect->express_id, 0);
    }

    public function getLoginURL(): string {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        return $this->isExpressUser() ? \Stripe\Account::createLoginLink($this->user->StripeConnect->express_id)->url : null;
    }

    public static function getAccountFromStripeConnect(string $code): \Stripe\Account {
        $token_request_body = array(
            'client_secret' => SiteConfig::get('STRIPE_SECRET'),
            'grant_type' => 'authorization_code',
            'client_id' => SiteConfig::get('STRIPE_KEY'),
            'code' => $code,
        );
        $req = curl_init('https://connect.stripe.com/oauth/token');
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true);
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
        // TODO: Additional error handling
        $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
        $resp = json_decode(curl_exec($req), true);
        curl_close($req);

        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        return \Stripe\Account::retrieve($resp['stripe_user_id']);
    }

}
