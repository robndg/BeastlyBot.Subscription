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
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        if(Session::has('subs_' . $this->user->discord_id)) return Session::get('subs_' . $this->user->discord_id);

        $stripe_subs = \Stripe\Subscription::all(['customer' => $this->user->stripe_customer_id, 'status' => 'active']);

        Session::put('subs_' . $this->user->discord_id, $stripe_subs);
        return $stripe_subs;
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
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            Session::put('stripe_email', $this->getCustomerAccount()->email);
            return Session::get('stripe_email');
        } catch (ApiErrorException $e) {
        }

        return null;
    }

    public function getCustomerAccount(): \Stripe\Customer {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            return \Stripe\Customer::retrieve($this->user->stripe_customer_id);
        } catch (ApiErrorException $e) {
        }
        return null;
    }

    public function isSubscriptionMonthly(): bool {
        $active_plan = $this->getExpressSubscription();
        return $active_plan !== null && $active_plan->id == env('MONTHLY_PLAN');
    }

    public function isSubscriptionYearly(): bool {
        $active_plan = $this->getExpressSubscription();
        return $active_plan !== null && $active_plan->id == env('YEARLY_PLAN');
    }

    public function getExpressSubscription(): \Stripe\Subscription {
        foreach($this->getSubscriptions() as $subscription) {
            if ($subscription->items->data[0]->plan->product === env('EXPRESS_PROD_ID'))  return $subscription;
        }

        return null;
    }

    public function isExpressUser(): bool {
        return $this->user->stripe_express_id !== null;
    }

    public function hasExpressPlan(): bool {
        return $this->getExpressSubscription() != null;
    }

    public function getBalance() {
        if(Cache::has('balance_' . $this->user->stripe_express_id)) return Cache::get('balance_' . $this->user->stripe_express_id, 0);
        $balance = \Stripe\Balance::retrieve(
            ['stripe_account' => $this->user->stripe_express_id]
        );
        Cache::put('balance_' . $this->user->stripe_express_id, $balance, 60 * 5); // 5 minutes
        return Cache::get('balance_' . $this->user->stripe_express_id, 0);
    }

    public function getLoginURL(): string {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        return $this->isExpressUser() ? \Stripe\Account::createLoginLink($this->user->stripe_express_id)->url : null;
    }

    public static function getAccountFromStripeConnect(string $code): \Stripe\Account {
        $token_request_body = array(
            'client_secret' => env('STRIPE_SECRET'),
            'grant_type' => 'authorization_code',
            'client_id' => env('STRIPE_KEY'),
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

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        return \Stripe\Account::retrieve($resp['stripe_user_id']);
    }

}
