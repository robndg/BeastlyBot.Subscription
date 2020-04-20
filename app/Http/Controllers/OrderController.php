<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\AlertHelper;
use App\Notification;
use App\Order;
use App\Shop;
use App\Refund;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\InvalidRequestException;
use App\User;

class OrderController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function process(Request $request) {
        $cycle = $request['cycle'];
        $affiliate = $request['affiliate_id'];
        $promotion_code = $request['promotion_code'];
        $guild_id = $request['guild_id'];
        $role_id = $request['role_id'];
        $guild_name = $request['guild_name'];

        // Check if user is banned
        // Need to make it only look thru guild_id or role_id

        if (Refund::where('user_id', (auth()->User()->id))->where('guild_id','=', $guild_id)->where('ban','=','1')->exists()) {
            return response()->json(['success' => false, 'msg' => 'You are banned from making purchases.']);
        }

           // First we check to make sure the user isn't already subscribed to this role.
        // Subscribing to one role more than once can pose issues in the DB and logic backend wise.

        if (auth()->user()->getStripeHelper()->isSubscribedToRole($guild_id, $role_id)) {
            return response()->json(['success' => false, 'msg' => 'You are already subscribed to that role. You can edit your subscription in the subscriptions page.']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Try to grab the product. If we can't find it or it isn't active we tell the user it is not available for purchase
        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            if (!$product->active) throw new InvalidRequestException();
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'This role is not enabled by the owner for purchase.']);
        }

        $owner = User::where('id', $product->metadata['id'])->get()[0];

        // Try to grab the price for the plan/duration clicked on
        // *PS: This shouldn't happen as the frontend code doesn't allow them to select a duration that isn't available
        try {
            $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $cycle . '_r');
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'There is no price set for that cycle.']);
        }

        // Check if they are trying to use a coupon, if so grab it.
        $coupon = null;
        if (rtrim($promotion_code) !== '') {
            try {
                $coupon = \Stripe\Coupon::retrieve($owner->id . $promotion_code);
                if (!$coupon->valid) throw new \Exception();
                if ($coupon->max_redemptions === $coupon->times_redeemed) throw new \Exception();
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
                $coupon = null;
                return response()->json(['success' => false, 'msg' => 'Invalid coupon.']);
            }
        }

        // This is where all the main logic happens for the checkout process, above is mostly the setup
        try {
            // We need to grab the Stripe Customer object from the stripe_customer_id connected to the account in our DB
            $customer = \Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);

            // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
            if ($customer === null || $customer->email === null || $customer->email === '') {
                return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
            }
            /**
             * Since there is no way to actually apply a coupon in the Stripe checkout process as of yet
             * we simply apply it to the users stripe account, after they complete checkout we remove it.
             */
            if ($coupon !== null) {
                \Stripe\Customer::update(
                    auth()->user()->stripe_customer_id,
                    ['coupon' => $owner->id . $promotion_code]
                );
            }

            $success_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;
            $cancel_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;

            if (\App\Shop::where('id', $guild_id)->get()[0]->testing){
                $quantity_product = '1';
            }
            else{
                $quantity_product = '0';
                return response()->json(['success' => false, 'msg' => 'Sorry, purchases are disabled in Test mode']);
            }

            if ($affiliate != '0') {
                $success_url .= '&affiliate=' . $affiliate;
                $cancel_url .= '&affiliate=' . $affiliate;
            }

            // Here we create the Stripe checkout session with all the details
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $plan->id,
                        'quantity' => $quantity_product
                    ]]
                ],
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'customer' => $customer->id,
            ]);

            // if coupon is applied we must insert a CheckoutSession into our DB to keep track to make sure we remove it from
            // the stripe user after 5 minutes.
            if ($coupon !== null) {
                $date = new DateTime('now', new DateTimeZone('UTC'));
                // we allow 5 minutes to complete checkout with the applied coupon
                $date->modify('+5 minute');
                // must subtract 4 hours to make time accurate for some reason? This may have bugs in other time regions
                $date->modify('-4 hour');
                $db_session = new \App\CheckoutSession();
                $db_session->id = $session->id;
                $db_session->valid_until = date('Y-m-d H:i:s', $date->getTimestamp());
                $db_session->coupon = $owner->id . $coupon->id;
                $db_session->save();
            }

            // store the checkout ID in the customers CheckoutSession
            Session::put('checkout_id', $session->id);
            return response()->json(['success' => true, 'msg' => $session->id]);
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'This role is not enabled by the owner for purchase.']);
        }
    }

    public function checkoutSuccess() {
        $guild_id = \request('guild_id');
        $role_id = \request('role_id');
        $affiliate = \request('affiliate');

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
        if (!$product->active) throw new InvalidRequestException();

        $owner = User::where('id', $product->metadata['id'])->get()[0];

        $application_fee_percent = $owner->app_fee_percent;

        try {
            // Grab the checkout session ID from the @process method
            // TODO: !!! (retrieve null)
            $session = \Stripe\Checkout\Session::retrieve(Session::get('checkout_id'));

            // add shop refund details

            $refund_enabled = Shop::where('id', $guild_id)->value('refunds_enabled');
            $refund_days = Shop::where('id', $guild_id)->value('refunds_days');
            $refund_terms = Shop::where('id', $guild_id)->value('refunds_terms');

            // check if owner of guild
            $owner_guild = Shop::where('id', $guild_id)->where('owner_id', '=', (auth()->User()->id))->exists();


            // if not owner add app fee
            /*if(!$owner_guild){
                \Stripe\Subscription::update(
                    $session->subscription,
                    ['application_fee_percent' => $application_fee_percent,
                    ]
                );
            }*/
            if ($affiliate !== null) {
                if (Affiliate::where('id', $affiliate)->where('guild_id', $guild_id)->exists()) {
                    \Stripe\Subscription::update(
                        $session->subscription,
                        ['metadata' => ['affiliate' => $affiliate]]
                    );
                }
            }
            if ($refund_enabled !== null){
                \Stripe\Subscription::update(
                    $session->subscription,
                    ['metadata' => [
                    'id' => auth()->user()->id,
                    'discord_id' => auth()->user()->discord_id,
                    'refund_enabled' => $refund_enabled,
                    'refund_days' => $refund_days,
                    'refund_terms' => $refund_terms
                    ]]
                );
            }
            $subscription_array = \Stripe\Subscription::retrieve(
                $session->subscription
            );

            \Stripe\Invoice::update(
                $subscription_array->latest_invoice,
                ['metadata' => ['stripe_express_id' => $owner->stripe_express_id]]
            );

            // Go ahead and remove the session from the database
            if (\App\CheckoutSession::where('id', $session->id)->exists()) \App\CheckoutSession::where('id', $session->id)->delete();


            // remove from user session
            Session::remove('checkout_id');
            // get the stripe customer object
            $customer = \Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
            // get the coupon code used at checkout
            if ($customer->discount !== null) {
                $coupon_code = $customer->discount->coupon->id;
                // check if the coupon lasts longer than once
                if ($customer->discount->coupon->duration !== 'once') {
                    // update the subscription to have the coupon
                    \Stripe\Subscription::update($session->subscription,
                        ['coupon' => $coupon_code]
                    );
                }
            }
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        // remove the coupon from the customer
        \Stripe\Customer::update(
            auth()->user()->stripe_customer_id,
            ['coupon' => '']
        );

        //$owner_id = Shop::where('id', '=', $guild_id)->value('owner_id');
        $stripe_express_id = User::where('id', '=', $owner->id)->value('stripe_express_id');

        \Stripe\Account::update(
            $stripe_express_id,
            ['metadata' => ['order' => true]]
        );

        /**
         * Update the PaymentIntent data with the guild_id and role_id from the transaction since
         * there is no way to initially store this with the Stripe CheckoutSession object API
         **/
       //  foreach (\Stripe\PaymentIntent::all(['customer' => auth()->user()->stripe_customer_id]) as $payment) {

        //     $intent_amount = $payment->amount;
        //     $owner_app_fee = User::where('id', '=', Shop::where('id', $guild_id)->value('owner_id'))->value('app_fee_percent');
         //    $intent_fee_percent = ($intent_amount * $owner_app_fee);
        //        \Log::info($intent_fee_percent);
        //
        //    if ((sizeof($payment->metadata) === 0) || ($payment->application_fee_amount) === NULL) {
        //         try {
        //             \Stripe\PaymentIntent::update($payment->id,
         //                ['metadata' => ['guild_id' => $guild_id, 'role_id' => $role_id]]
                        // 'application_fee_amount' => $intent_fee_percent]
         //            );
        //         } catch (\Exception $e) {
         //            if (env('APP_DEBUG')) Log::error($e);
         //        }
         //    }
        // }
/*
        foreach (\Stripe\PaymentIntent::all(['customer' => auth()->user()->stripe_customer_id]) as $payment) {
             if (sizeof($payment->metadata) === 0) {
                 try {
                     \Stripe\PaymentIntent::update($payment->id,
                         ['metadata' => ['refund_enabled' => $refund_enabled, 'refund_days' => $refund_days, 'refund_terms' => $refund_terms]]
                     );
                 } catch (\Exception $e) {
                     if (env('APP_DEBUG')) Log::error($e);
                 }
             }
         }*/

        AlertHelper::alertSuccess('Payment successful!');

        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            if (!$product->active) throw new InvalidRequestException();

            $owner = User::where('id', $product->metadata['id'])->get()[0];
            Notification::create($owner->id, 'success', 'New order received.');

        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        // TODO: !!! Undefined variable
        $order = new Order();
        $order->id = $session->subscription;
        $order->save();

        \Log::info($order->id);


        return redirect('/account/subscriptions');
        //idk what this is for anymore... lol return redirect('https://discordapp.com/api/oauth2/authorize?response_type=code&client_id=' . $client_id. '&scope=identify%20guilds.join&state=' .$discord_id. '&redirect_uri=https%3A%2F%2Fbeastlybot.com/account/subscriptions&prompt=consent')

    }

    public function checkoutCancel() {
        AlertHelper::alertInfo('Payment cancelled.');

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // remove the coupon from the customer
            \Stripe\Customer::update(
                auth()->user()->stripe_customer_id,
                ['coupon' => '']
            );

            // get the session
            $session = \Stripe\Checkout\Session::retrieve(Session::get('checkout_id'));

            // Go ahead and remove the session from the database
            if (\App\CheckoutSession::where('id', $session->id)->exists()) \App\CheckoutSession::where('id', $session->id)->delete();

            // remove from user session
            Session::remove('checkout_id');
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        $guild_id = \request('guild_id');
        return redirect('/shop/' . $guild_id);

    }

    public function getInvoiceSlide($invoice_id) {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $invoice = \Stripe\Invoice::retrieve($invoice_id);
            return view('slide.slide-invoice')->with('invoice', $invoice);
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return view('slide.slide-invoice')->with('invoice', null);
        }
    }

    public function specialProcess(Request $request) {
        $cycle = $request['cycle'];
        $affiliate = $request['affiliate_id'];
        $promotion_code = $request['promotion_code'];
        $guild_id = $request['guild_id'];
        $role_id = $request['role_id'];
        $guild_name = $request['guild_name'];
        $special_id = $request['special_id'];
        $special_cycle = explode('_', $special_id)[2];
        $discord_id = explode('_', $special_id)[5];
        $special_type = explode('_', $special_id)[4];

        // Check if user is banned
        // Need to make it only look thru guild_id or role_id

        if (Refund::where('user_id', (auth()->User()->id))->where('guild_id','=', $guild_id)->where('ban','=','1')->exists()) {
            return response()->json(['success' => false, 'msg' => 'You are banned from making purchases.']);
        }

           // First we check to make sure the user isn't already subscribed to this role.
        // Subscribing to one role more than once can pose issues in the DB and logic backend wise.

        /*if (auth()->user()->isSubscribedToRole($guild_id, $role_id)) {
            return response()->json(['success' => false, 'msg' => 'You are already subscribed to that role. You can edit your subscription in the subscriptions page.']);
        }*/

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Try to grab the product. If we can't find it or it isn't active we tell the user it is not available for purchase
        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            //if (!$product->active) throw new InvalidRequestException();
        } catch (InvalidRequestException $e) {
            Log::error($e);
            return response()->json(['success' => false, 'msg' => 'This special role is not enabled by the owner for purchase.']);
        }
        Log::info($product);

        $owner = User::where('id', $product->metadata['id'])->get()[0];
        $owner_id = $product->metadata['id'];
        // Try to grab the price for the plan/duration clicked on
        // *PS: This shouldn't happen as the frontend code doesn't allow them to select a duration that isn't available
        //Log::info(($guild_id . '_' . $role_id . '_' . $special_cycle . '_r_' . $special_type . '_' . $discord_id));
        try {
            $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $cycle . '_r_' . $special_type . '_' . $discord_id);
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'There is no price set for that cycle.']);
        }
        try {
            $cus_stripe_customer_id = auth()->user()->stripe_customer_id;
        } catch (InvalidRequestException $e) {
            return response()->json(['success' => false, 'msg' => 'Could not find customer ID in database.']);
        }
        $customer = \Stripe\Customer::retrieve($cus_stripe_customer_id);

        if ($customer === null || $customer->email === null || $customer->email === '') {
            return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
        }
        if($special_type == "t"){
            $type_name = "Trial";
        }else{
            $type_name = "Special";
        }
                try{
                    // 2A) New subscription with plan + free trial
                    if($special_type == "t"){
                        \Stripe\Subscription::create([
                            'customer' => $cus_stripe_customer_id,
                            'items' => [
                            [
                                'plan' => $plan,
                            ],
                            ],
                            //'trial_period_days' => $duration_days,
                            'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                        ]);
                       // Have user log into Shop and check out with product
                       //return response()->json(['success' => true, 'msg' => 'Success. Trial role will be added soon.']);

                    // 2B) New subscription with plan (not free trial)
                    }else{
                        \Stripe\Subscription::create([
                            'customer' => $cus_stripe_customer_id,
                            'items' => [
                            [
                                'plan' => $plan,
                            ],
                            ],
                            'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                        ]);
                        // TODO: send invoice, then somehow check paid to add role
                    }
                    try {

                        // Since there is no way to actually apply a coupon in the Stripe checkout process as of yet
                        // we simply apply it to the users stripe account, after they complete checkout we remove it.

                        /*if ($coupon !== null) {
                            \Stripe\Customer::update(
                                auth()->user()->stripe_customer_id,
                                ['coupon' => $owner->id . $promotion_code]
                            );
                        }*/

                        $success_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;
                        $cancel_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;

                        if (\App\Shop::where('id', $guild_id)->get()[0]->testing){
                            if($plan->metadata['purchased'] == 'true'){
                                $quantity_product = '0';
                                return response()->json(['success' => false, 'msg' => 'Sorry, you have already purchased this role.']);
                            }else{
                                $quantity_product = '1';
                            }
                        }
                        else{
                            $quantity_product = '0';
                            return response()->json(['success' => false, 'msg' => 'Sorry, purchases are disabled in shop Test mode']);
                        }

                        /*if ($affiliate != '0') {
                            $success_url .= '&affiliate=' . $affiliate;
                            $cancel_url .= '&affiliate=' . $affiliate;
                        }*/


                        // TODO: this needs to be moved to checkout success but for now this will do
                        /*\Stripe\Plan::update(
                            $plan->id,
                            ['metadata' => ['purchased' => 'true']]
                        );*/


                        // Here we create the Stripe checkout session with all the details
                        $session = \Stripe\Checkout\Session::create([
                            'payment_method_types' => ['card'],
                            'subscription_data' => [
                                'items' => [[
                                    'plan' => $plan->id,
                                    'quantity' => $quantity_product
                                ]]
                            ],
                            'success_url' => $success_url,
                            'cancel_url' => $cancel_url,
                            'customer' => $customer->id,
                        ]);

                        // if coupon is applied we must insert a CheckoutSession into our DB to keep track to make sure we remove it from
                        // the stripe user after 5 minutes.
                        /*if ($coupon !== null) {
                            $date = new DateTime('now', new DateTimeZone('UTC'));
                            // we allow 5 minutes to complete checkout with the applied coupon
                            $date->modify('+5 minute');
                            // must subtract 4 hours to make time accurate for some reason? This may have bugs in other time regions
                            $date->modify('-4 hour');
                            $db_session = new \App\CheckoutSession();
                            $db_session->id = $session->id;
                            $db_session->valid_until = date('Y-m-d H:i:s', $date->getTimestamp());
                            $db_session->coupon = $owner->id . $coupon->id;
                            $db_session->save();
                        }*/

                        // store the checkout ID in the customers CheckoutSession
                        Session::put('checkout_id', $session->id);
                        return response()->json(['success' => true, 'msg' => $session->id]);
                    } catch (InvalidRequestException $e) {
                        if (env('APP_DEBUG')) Log::error($e);
                        return response()->json(['success' => false, 'msg' => 'This role is not enabled by the owner for purchase.']);
                    }
                }catch(\Exception $e) {
                    Log::error($e);
                    return response()->json(['success' => false, 'msg' => 'There was an error creating your ' . $type_name . ' subscription.']);
                }


        /*

        // Check if they are trying to use a coupon, if so grab it.
        $coupon = null;
        if (rtrim($promotion_code) !== '') {
            try {
                $coupon = \Stripe\Coupon::retrieve($owner->id . $promotion_code);
                if (!$coupon->valid) throw new \Exception();
                if ($coupon->max_redemptions === $coupon->times_redeemed) throw new \Exception();
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
                $coupon = null;
                return response()->json(['success' => false, 'msg' => 'Invalid coupon.']);
            }
        }

        // This is where all the main logic happens for the checkout process, above is mostly the setup
        try {
            // We need to grab the Stripe Customer object from the stripe_customer_id connected to the account in our DB
            $customer = \Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);

            // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
            if ($customer === null || $customer->email === null || $customer->email === '') {
                return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
            }

            // Since there is no way to actually apply a coupon in the Stripe checkout process as of yet
            // we simply apply it to the users stripe account, after they complete checkout we remove it.

            if ($coupon !== null) {
                \Stripe\Customer::update(
                    auth()->user()->stripe_customer_id,
                    ['coupon' => $owner->id . $promotion_code]
                );
            }

            $success_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;
            $cancel_url = env('APP_URL') . '/checkout-success?guild_id=' . $guild_id . '&role_id=' . $role_id;

            if (\App\Shop::where('id', $guild_id)->get()[0]->testing){
                $quantity_product = '1';
            }
            else{
                $quantity_product = '0';
                return response()->json(['success' => false, 'msg' => 'Sorry, purchases are disabled in Test mode']);
            }

            if ($affiliate != '0') {
                $success_url .= '&affiliate=' . $affiliate;
                $cancel_url .= '&affiliate=' . $affiliate;
            }

            // Here we create the Stripe checkout session with all the details
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $plan->id,
                        'quantity' => $quantity_product
                    ]]
                ],
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'customer' => $customer->id,
            ]);

            // if coupon is applied we must insert a CheckoutSession into our DB to keep track to make sure we remove it from
            // the stripe user after 5 minutes.
            if ($coupon !== null) {
                $date = new DateTime('now', new DateTimeZone('UTC'));
                // we allow 5 minutes to complete checkout with the applied coupon
                $date->modify('+5 minute');
                // must subtract 4 hours to make time accurate for some reason? This may have bugs in other time regions
                $date->modify('-4 hour');
                $db_session = new \App\CheckoutSession();
                $db_session->id = $session->id;
                $db_session->valid_until = date('Y-m-d H:i:s', $date->getTimestamp());
                $db_session->coupon = $owner->id . $coupon->id;
                $db_session->save();
            }

            // store the checkout ID in the customers CheckoutSession
            Session::put('checkout_id', $session->id);
            return response()->json(['success' => true, 'msg' => $session->id]);
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'This role is not enabled by the owner for purchase.']);
        }
        */
    }

}
