<?php

namespace App\Http\Controllers;

use App\AlertHelper;
use App\StripeHelper;
use App\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\InvalidRequestException;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public static function getViewWithInvoices($view, $invoices) {
        $stripe_helper = auth()->user()->getStripeHelper();

        // get last 100 most recent invoices for this customer
        $invoices = \Stripe\Invoice::all([
            'limit' => $invoices,
            'customer' => auth()->user()->stripe_customer_id
        ]);

        $invoices_array = $invoices->toArray()['data'];

       // sort the invoices in ASC order
        usort($invoices_array, function($a, $b) {
            return $b['created'] <=> $a['created'];
        });

        return view($view)->with('stripe_login_link', $stripe_helper->getLoginURL())->with('invoices', $invoices_array);
    }

    public static function getViewWithSubscriptions($view) {
        $stripe_helper = auth()->user()->getStripeHelper();
        $subscriptions = array();
        foreach ($stripe_helper->getSubscriptions() as $subscription) $subscriptions[$subscription->id] = $subscription->toArray();
        return view($view)->with('subscriptions', $subscriptions);
    }

    public function connectStripe() {
        $code = \request('code');

        // if there is an error connecting to Stripe, abort and let user know
        if (isset($_GET['error'])) {
            if (env('APP_DEBUG')) Log::error($_GET['error']);
            AlertHelper::alertError('Something went wrong! Open a support ticket.');
            return redirect('/dashboard');
        }

        if($code == null) return;

        $user = auth()->user();

        $stripe_account = StripeHelper::getAccountFromStripeConnect($code);

        if($stripe_account->country == 'US' && $user->stripe_express_id == null) {
            $user->stripe_express_id = $stripe_account->id;
            $user->save();
            AlertHelper::alertSuccess('Stripe account created! You can now accept payments.');
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            // Set payout schedule to 7 days automatically by default
            \Stripe\Account::update($stripe_account->id,
                ['settings' =>
                    ['payouts' =>
                        [ 'schedule' =>
                            ['delay_days' => 7]
                        ]
                    ]
                ]
            );
            return redirect('/dashboard#open-servers=true');
        } else {
            AlertHelper::alertError('This is not a US account or you have already connected an account.');
            return redirect('/dashboard');
        }
    }

    public function checkoutExpressPlan(Request $request) {
        $promotion_code = $request['promotion_code'];
        $plan = $request['monthly'] == 'true' ? env('MONTHLY_PLAN') : env('YEARLY_PLAN');
        $stripe_helper = auth()->user()->getStripeHelper();

        // make sure they have linked their stripe acount
        if (!$stripe_helper->isExpressUser()) return response()->json(['success' => false, 'msg' => 'Please connect or create your stripe account.']);
        // make sure they do not already have an active express plan with us
        if ($stripe_helper->hasActivePlan()) return response()->json(['success' => false, 'msg' => 'You already have an active live plan.']);
        // this is pretty useless, but it's making sure they have a valid stripe Customer account with us, not Express. That way we can bill their Customer account
        if ($stripe_helper->getStripeEmail() == null) return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        // Try to grab the price for the plan/duration clicked on
        // *PS: This shouldn't happen as the frontend code doesn't allow them to select a duration that isn't available
        try {
            $plan = \Stripe\Plan::retrieve($plan);
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'Could not find plan ID.']);
        }
        // This is where all the main logic happens for the checkout process, above is mostly the setup
        try {
            $customer = $stripe_helper->getCustomerAccount();
            // Here we create the Stripe checkout session with all the details
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $plan->id
                    ]]
                ],
                'success_url' => env('APP_URL') . '/buy-plan-success',
                'cancel_url' => env('APP_URL') . '/buy-plan-cancel',
                'customer' => $customer->id,
            ]);
            // store the checkout ID in the customers CheckoutSession
            Session::put('checkout_id', $session->id);
            return response()->json(['success' => true, 'msg' => $session->id]);
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => 'This role is not enabled by the owner for purchase.']);
        }
    }

    public function checkoutExpressPlanSuccess() {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Grab the checkout session ID from the @process method
            $session = \Stripe\Checkout\Session::retrieve(Session::get('checkout_id'));

            $user = User::where('id', auth()->user()->id)->get()[0];
            $user->plan_sub_id = $session->subscription;
            $user->save();


            Log::info($session->subscription . ' ' . $user->plan_sub_id);

            // Go ahead and remove the session from the database
            // if (\App\CheckoutSession::where('id', $session->id)->exists()) \App\CheckoutSession::where('id', $session->id)->delete();

            // remove from user session
            Session::remove('checkout_id');
            // get the stripe customer object
            $customer = \Stripe\Customer::retrieve(auth()->user()->stripe_customer_id);
            if($customer->discount != null) {
                // get the coupon code used at checkout
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

       // \Stripe\Account::update(
       //     auth()->user()->stripe_express_id,
        //    ['charges_enabled' => 'true']
        //);

        Session::put('alert', ['type' => 'success', 'msg' => 'Payment successful!']);

        return redirect('/servers?click-first=true');
    }

    public function checkoutExpressPlanFailure() {
        Session::put('alert', ['type' => 'info', 'msg' => 'Payment cancelled.']);

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
            // if (\App\CheckoutSession::where('id', $session->id)->exists()) \App\CheckoutSession::where('id', $session->id)->delete();

            // remove from user session
            Session::remove('checkout_id');
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        return redirect('/account/settings');
    }

    public function changePlan(Request $request) {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $plan = $request['plan'];

        try {
            $subscription = \Stripe\Subscription::retrieve(auth()->user()->plan_sub_id);

            $plan_id = $subscription->items->data[0]->plan->id;

            if ($plan === $plan_id) {
                return response()->json(['success' => false, 'msg' => 'You are already subscribed to that plan.']);
            }

            try {
            \Stripe\Subscription::update(auth()->user()->plan_sub_id, [
                'prorate' => true,
                'collection_method' => 'charge_automatically',
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $plan,
                    ],
                ],
            ]);

            $invoice = \Stripe\Invoice::upcoming(["customer" => auth()->user()->stripe_customer_id]);
            } catch(\Exception $e) {
                return $this->buyPlan($request);
            }

            // TODO: Somehow send the user the invoice in a sweet alert so they can pay it immediately.
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'msg' => 'Plan changed. You were billed automatically.',
            'invoice_url' => $invoice->invoice_pdf]);
    }

    public function getPayoutSlide($stripe_express_id) {

        if (auth()->user()->stripe_express_id != $stripe_express_id && !auth()->user()->admin) return response()->json(['success' => false, 'msg' => 'You do not own this Stripe account.']);

        if(auth()->user()->error == "1") return response()->json(['success' => false, 'msg' => 'Please refresh the page and connect a US Stripe account.']);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $unix_now = time();
        //$stripe_express_id = auth()->user()->stripe_express_id;

        // $payout_valid = \Stripe\Invoice::all([
        //     'status' => 'paid',
        //     'created' => [
        //         'lte' => strtotime('-7 day', $unix_now)
        //     ],
        // ]);


       // $stripe_account = \Stripe\Account::retrieve(
        //    $stripe_express_id
        //  );

       // $stripe_payout_delay = $stripe_account->settings->payouts->schedule->delay_days;
        $stripe_payout_delay = User::where('stripe_express_id', '=' , $stripe_express_id)->value('stripe_delay_days');

       // $app_fee_percent = User::where('stripe_express_id', $stripe_express_id)->value('app_fee_percent');

        $invoices_pending = \Stripe\Invoice::all([
            'status' => 'paid',
            'created' => [
                'gte' => strtotime('-'.$stripe_payout_delay.' days', $unix_now)
            ],
        ]);
        $app_fee_percent = User::where('stripe_express_id', '=' , $stripe_express_id)->value('app_fee_percent');

        $earnings = 0;
        $pending_invoices = array();
        foreach($invoices_pending as $invoice) {
            if ($invoice->metadata['paid_out'] != 'true' && $invoice->metadata['refunded'] != 'true') {
                try {
                    //$product_id = $invoice->lines->data[0]->plan->product;
                    //$product = \Stripe\Product::retrieve($product_id);
                    if($invoice->lines->data[0]->plan->metadata['app_fee_percent']) {
                        $app_fee_percent =  $invoice->lines->data[0]->plan->metadata['app_fee_percent'];
                    }else{
                        $app_fee_percent = User::where('stripe_express_id', $stripe_express_id)->value('app_fee_percent');
                    }
                   // if( $invoice->lines->data[0]->plan->metadata['payout_delay']) {
                   //     $stripe_payout_delay = $invoice->lines->data[0]->plan->metadata['payout_delay'];
                   // }
                    if($invoice->lines->data[0]->plan->metadata['stripe_express_id'] == $stripe_express_id) {
                        array_push($pending_invoices, $invoice);
                        $earnings += (($invoice->amount_paid / 100) * ((100 - $app_fee_percent)/100));
                    }
                } catch(\Exception $e) {
                    Log::error($e);
                }
            }
        }

        usort($pending_invoices, function($a, $b) {
            return $a['created'] <=> $b['created'];
        });

        return view('slide.slide-payout')->with('pending_invoices', $pending_invoices)->with('stripe_login_link', \Stripe\Account::createLoginLink($stripe_express_id)->url)->with('earnings', $earnings)->with('stripe_payout_delay', $stripe_payout_delay);
    }

}
