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

    // TODO: working on changePlan
    public function changePlan(Request $request) {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $plan_id = $request['plan'];

        try {

            $stripe_helper = auth()->user()->getStripeHelper();

            if ($stripe_helper->isSubscribedToPlan($plan_id)) 
                return response()->json(['success' => false, 'msg' => 'You are already subscribed to that plan.']);

            $subscription = $stripe_helper->getSubscriptionForProduct($plan_id);


            try {
            \Stripe\Subscription::update($subscription->id, [
                'prorate' => true,
                'collection_method' => 'charge_automatically',
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $plan_id,
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
