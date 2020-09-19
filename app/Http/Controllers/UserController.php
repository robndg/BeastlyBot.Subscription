<?php

namespace App\Http\Controllers;

use App\AlertHelper;
use App\SiteConfig;
use App\StripeHelper;
use App\DiscordStore;
use App\PaymentMethod;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public static function getViewWithInvoices(string $view, int $num_of_invoices) {
        $stripe_helper = auth()->user()->getStripeHelper();
       
        if(Cache::has('user_invoices_' . auth()->user()->id)) {
            $invoices = Cache::get('user_invoices_' . auth()->user()->id);
        } else {
            \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));

            $invoices = \Stripe\Invoice::all([
                'limit' => $num_of_invoices,
                'customer' => auth()->user()->StripeConnect->customer_id
            ]);

            $invoices_array = $invoices->toArray()['data'];

            // sort the invoices in ASC order
            usort($invoices_array, function($a, $b) {
                return $b['created'] <=> $a['created'];
            });

            Cache::put('user_invoices_' . auth()->user()->id, $invoices_array, 60 * 10);
        }

        return view($view)->with('stripe_login_link', $stripe_helper->getLoginURL())->with('invoices', Cache::get('user_invoices_' . auth()->user()->id, array()));
    }

    public function getPayoutSlide($stripe_account_id) {

        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));

        if (auth()->user()->StripeConnect->express_id != $stripe_account_id && !Auth::user()->admin) return response()->json(['success' => false, 'msg' => 'You do not own this Stripe account.']);

        #if (auth()->user()->error == "1") return response()->json(['success' => false, 'msg' => 'Please refresh the page and connect a US Stripe account.']);

        $unix_now = time();

    #####
        ## TODO: We should add payout delay to their stripe account, so if they get a dispute it adds 7 days
        /*
        $stripe_account_id = Auth::user()->stripe_account_id;
        $stripe_account = \Stripe\Account::retrieve($stripe_account_id);
        1) Get it from stripe
            $stripe_payout_delay = $stripe_account->settings->payouts->schedule->delay_days;
        2) Get it from user DB
            $stripe_payout_delay = User::where('stripe_account_id', '=' , $stripe_account_id)->value('stripe_delay_days');
        */

    #####

    ####
        ## TODO: can we get the fee percent from Stripe API?
        # so we can add days if they get disputes or lower if they are good.
        $app_fee_percent = 5;
    ####


    #### TODO: we need a faster way to get their paid invoices
        $invoices_pending = \Stripe\Invoice::all([
            'status' => 'paid',
            'created' => [
                'gte' => strtotime('-'.$stripe_payout_delay.' days', $unix_now)
            ],
        ]);

        $earnings = 0;
        $pending_invoices = array();
        foreach($invoices_pending as $invoice) {
            if ($invoice->metadata['paid_out'] != 'true' && $invoice->metadata['refunded'] != 'true') {
                try {
                    if($invoice->lines->data[0]->plan->metadata['app_fee_percent']) {
                        $app_fee_percent =  $invoice->lines->data[0]->plan->metadata['app_fee_percent'];
                    }else{
                        $app_fee_percent = 5;
                    }
                    if($invoice->lines->data[0]->plan->metadata['stripe_account_id'] == $stripe_account_id) {
                        array_push($pending_invoices, $invoice);
                        $earnings += (($invoice->amount_paid / 100) * ((100 - $app_fee_percent)/100));
                    }
                } catch(\Exception $e) {
                    \Log::error($e);
                }
            }
        }

        usort($pending_invoices, function($a, $b) {
            return $a['created'] <=> $b['created'];
        });

        return view('slide.slide-payout')->with('pending_invoices', $pending_invoices)->with('stripe_login_link', \Stripe\Account::createLoginLink($stripe_account_id)->url)->with('earnings', $earnings)->with('stripe_payout_delay', $stripe_payout_delay);
    }

    public function cancelSubscription(Request $request) {
        $sub_id = $request['sub_id'];
        $end_now = $request['end_now'];

        if(!\Auth::user()->getStripeHelper()->isSubscribedToID($sub_id)) {
            return response()->json(['success' => false, 'msg' => 'This is not your subscription. Contact support']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));

        try {
            // Get subscription from stripe and cancel it.
            $sub = \Stripe\Subscription::retrieve($sub_id);

            try {
                $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                if (!$product->active) throw new InvalidRequestException();
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
            }

            if($end_now == "1") {
                // Must figure out how to task handler remove role now or else it will just wait till expiration date
                $sub->cancel();
            } else {
                \Stripe\Subscription::update(
                    $sub_id, ['cancel_at_period_end' => true]
                );
              
            }

        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

    public function undoCancelSubscription(Request $request) {


        $sub_id = $request['sub_id'];
        $end_now = $request['end_now'];

        if(!\Auth::user()->ownsSubscription($sub_id)) {
            return response()->json(['success' => false, 'msg' => 'This is not your subscription. Contact support']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Get subscription from stripe and cancel it.
        $sub = \Stripe\Subscription::retrieve($sub_id);

        try {
            $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
            if (!$product->active) throw new InvalidRequestException();
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        if($end_now == "1" && $sub->items->data[0]->plan->active){
            $owner = User::where('id', $product->metadata['id'])->get()[0];
            Notification::create($owner->id, 'warning', 'Subscription now cancelled.');
            $sub->delete();
            // figure out how to task handler remove role now
            return response()->json(['success' => true, 'msg' => 'Subscription cancelled immediately.']);
        }else{
            \Stripe\Subscription::update(
                $sub_id,
                ['cancel_at_period_end' => false
                ]
            );
            return response()->json(['success' => true, 'msg' => 'Subscription un-cancelled.']);
        }

        return response()->json(['success' => false]);
    }

    public function requestSubscriptionRefund(Request $request) {
        $sub_guild_name = $request['sub_guild_name'];
        $sub_role_name = $request['sub_role_name'];
        $sub_user_id = $request['sub_user_id'];
        $sub_id = $request['sub_id'];
        $reason = $request['reason'];


        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Get subscription from stripe and cancel it.
            $sub = \Stripe\Subscription::retrieve($sub_id);

            $latest_invoice = \Stripe\Invoice::retrieve($sub->latest_invoice);

            $refund_terms = $sub->metadata['refund_terms'];
            $refund_days = $sub->metadata['refund_days'];
            $refund_enabled = $sub->metadata['refund_enabled'];

            try {
                        $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                        if (!$product->active) throw new InvalidRequestException();

                        if(Refund::where('sub_id', $sub_id)->exists()) return response()->json(['success' => false, 'msg' => 'You have already submitted a refund request.']);

                            $owner = User::where('id', $product->metadata['id'])->get()[0];
                            if($refund_terms != "1"){
                                Notification::create($owner->id, 'warning', 'Subscription refund requested.');
                            }
                            //$sub_id =
                            $sub_start_date = $sub['start_date'];
                            $sub_period_end = $sub['current_period_end'];
                            $sub_stripe_account_id = $sub['items']['data'][0]['plan']['metadata']['stripe_account_id'];
                            //$sub_user_id =
                            $owner_id = $owner->id;
                            //$sub_guild_name =
                            //$sub_role_name =
                            $sub_guild_id = explode('_', $product->id)[0];
                            $sub_role_id = explode('_', $product->id)[1];

                            $owner_guild = Shop::where('id', $sub_guild_id)->where('owner_id', '=', (Auth::User()->id))->exists();


                            if($owner_guild){
                                $sub_refund_enabled = "0";
                                $sub_refund_days = "0";
                                $sub_refund_terms = "100";
                                $sub_application_fee = "0";

                                \Stripe\Subscription::update(
                                    $sub_id,
                                    ['metadata' => [
                                        'refund_enabled' => $sub_refund_enabled,
                                        'refund_days' => $sub_refund_days,
                                        'refund_terms' => $sub_refund_terms
                                    ],
                                    ]
                                );
                            }else{
                                $sub_application_fee = $owner->app_fee_percent;

                                $sub_refund_enabled = Shop::where('id', $sub_guild_id)->value('refunds_enabled');
                                $sub_refund_days = Shop::where('id', $sub_guild_id)->value('refunds_days');
                                $sub_refund_terms = Shop::where('id', $sub_guild_id)->value('refunds_terms');

                            }
                            $sub_description = $reason;
                            $sub_plan_id = $sub['items']['data'][0]['plan']['id'];
                            $sub_amount = $sub['items']['data'][0]['plan']['amount'];
                            //$sub_application_fee = $sub['application_fee_percent'];

                            Refund::create($sub_id, $sub_start_date, $sub_period_end, $sub_user_id, $owner_id, $sub_stripe_account_id, $sub_guild_name, $sub_role_name, $sub_guild_id, $sub_role_id, $sub_refund_enabled, $sub_refund_days, $sub_refund_terms, $sub_description, $sub_plan_id, $sub_amount, $sub_application_fee);
                            try {
                            // if no questions asked we can make the decision now.
                                if ($refund_terms == "1"){
                                        $issued = "1";
                                        $decision = "1";
                                        $ban = "0";
                                        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls

                                        try {
                                            // Get subscription from stripe and cancel it.
                                            $sub = \Stripe\Subscription::retrieve($sub_id);
                                            try {
                                                $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                                                if (!$product->active) throw new InvalidRequestException();
                                                if(Refund::where('sub_id', $sub_id)->where('decision', '=', $decision)->exists()) return response()->json(['success' => false, 'msg' => 'You have already submitted a refund decision.']);
                                                $owner = User::where('id', $product->metadata['id'])->get()[0];
                                                    //Notification::create($owner->id, 'warning', 'Refund request closed.');
                                                    $refund = Refund::where('sub_id', $sub_id)->get()[0];
                                                    $refund->ban = $ban;
                                                    $refund->save();
                                                try{

                                                    $in_invoice = $sub['latest_invoice'];
                                                    $invoice = \Stripe\Invoice::retrieve($in_invoice);
                                                    $invoice_charge = $invoice->charge;
                                                    //$invoice_app_fee = $invoice->lines->data[0]->metadata['']
                                                    $invoice_app_fee = $invoice->metadata['fee'];

                                                    if ($invoice->metadata['fee']){
                                                        $invoice_app_fee = $invoice->metadata['fee'];
                                                    }elseif ($owner->app_fee_percent){
                                                        $invoice_app_fee = $owner->app_fee_percent;
                                                    }else if ($product->metadata['app_fee_percent']){
                                                        $invoice_app_fee = $product->metadata['app_fee_percent'];
                                                    }else {
                                                        $invoice_app_fee = 5;
                                                    }
                                                    // TODO: use these for more test before issuing
                                                    $invoice_paid = $invoice->paid;
                                                   // $invoice_amount_paid = $invoice->amount_paid;
                                                   // $invoice_customer = $invoice->customer;
                                                   // $invoice_application_fee_amount = $invoice->application_fee_amount;
                                                   $user_id = Refund::where('sub_id', $sub_id)->value('user_id');

                                                    if(Refund::where('sub_id', $sub_id)->where('refund_terms', '=', '100')->exists()){
                                                        $refund->decision = "1";
                                                        $refund->issued = "0";
                                                        $refund->save();
                                                        return response()->json(['success' => false, 'msg' => 'Success, funds will be paid out in Payout (you are the owner of this purchase).']);
                                                        Notification::create($user_id, 'success', 'Refund success.');
                                                    }
                                                    if (Refund::where('sub_id', $sub_id)->where('issued', '=', NULL)->exists()){ // check if no decision on issued
                                                        $refund->decision = "1";
                                                        $refund->issued = $issued;
                                                        $refund->save();
                                                        if($issued = "1"){
                                                            $transfer_id = null;
                                                                //$transfers = array();
                                                            foreach (\Stripe\Transfer::all(['destination' => $owner->stripe_account_id]) as $transfer) {
                                                                $transfer_array = $transfer->toArray();
                                                                if($transfer_array['metadata']['transfer_inv'] == $in_invoice){
                                                                    $transfer_id = $transfer_array['id'];
                                                                    // \Log::info($transfer_id);
                                                                }
                                                            }
                                                            if($invoice->metadata['paid_out'] == 'true' && $invoice->metadata['refunded'] != 'true' && $transfer_id != null){
                                                                if($invoice->metadata['reversed'] != true ){
                                                                \Stripe\Transfer::createReversal($transfer_id);
                                                                \Stripe\Invoice::update(
                                                                    $in_invoice,
                                                                    ['metadata' => ['reversed' => true, 'refunded' => true]]
                                                                );
                                                                \Stripe\Refund::create([
                                                                    'charge' => $invoice_charge,
                                                                    'amount' => (($invoice->amount_paid)*((100-$invoice_app_fee)/100)),
                                                                    //'refund_application_fee' => false,
                                                                    'metadata' => [
                                                                        'reversed' => true,
                                                                        'refunded' => true
                                                                        ]
                                                                    ]);
                                                                }
                                                                Notification::create($owner->id, 'warning', 'Refund reversed and sent successfully.');
                                                                Notification::create($user_id, 'success', 'Refund success.');
                                                            }elseif ($invoice->metadata['refunded'] != 'true'){
                                                            \Stripe\Invoice::update(
                                                                $in_invoice,
                                                                ['metadata' => ['reversed' => false, 'refunded' => true]]
                                                            );
                                                            \Stripe\Refund::create([
                                                            'charge' => $invoice_charge,
                                                            'amount' => (($invoice->amount_paid)*((100-$invoice_app_fee)/100)),
                                                            //'refund_application_fee' => false,
                                                            'metadata' => [
                                                                'refunded' => true
                                                                ]
                                                            ]);
                                                            Notification::create($owner->id, 'success', 'Refund successfully sent.');
                                                            Notification::create($user_id, 'success', 'Refund success.');
                                                            }
                                                        }
                                                        //return response()->json(['success' => false, 'msg' => 'SUCCESS!']);

                                                        // TODO: we need to cancel all upcoming invoices
                                                        // remove the subscription
                                                        // set subscription to "ended: true"
                                                    }else if (Refund::where('sub_id', $sub_id)->where('issued', '=', "1")->exists()){ // check if issued already
                                                        $refund->decision = "1";
                                                        $refund->save();
                                                        return response()->json(['success' => false, 'msg' => 'Refund already issued.']);
                                                    }else{ // check if 1 (decision made but denied)
                                                        return response()->json(['success' => false, 'msg' => 'Refund already denied.']);
                                                        $refund->decision = "1";
                                                        $refund->save();
                                                    }
                                                }catch (\Exception $e){
                                                    if (env('APP_DEBUG')) Log::error($e);
                                                }
                                            } catch (\Exception $e) {
                                                if (env('APP_DEBUG')) Log::error($e);
                                            }
                                            $sub->delete();
                                            // The subscription will be removed from the DB by the bot on expiration date.
                                        } catch (\Exception $e) {
                                            if (env('APP_DEBUG')) Log::error($e);
                                            return response()->json(['success' => false]);
                                        }
                                        //return response()->json(['success' => true, 'msg' => 'You have been refunded!']);

                                    // End refund

                                        //$refund->save();
                                    }
                        } catch (\Exception $e) {
                            if (env('APP_DEBUG')) Log::error($e);
                        }

            // The subscription will be removed from the DB by the bot on expiration date.
                } catch (\Exception $e) {
                    if (env('APP_DEBUG')) Log::error($e);
                    return response()->json(['success' => false]);
                }

        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

    public function decisionRefundRequest(Request $request) {
        //Log::info($request);
        $sub_id = $request['sub_id'];
        $issued = $request['issued'];
        $decision = "1";
        $ban = $request['ban'];
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));


        try {
            // Get subscription from stripe and cancel it.
            $sub = \Stripe\Subscription::retrieve($sub_id);
            $sub->delete();
            try {
                $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                if (!$product->active) throw new InvalidRequestException();

                if(Refund::where('sub_id', $sub_id)->where('decision', '=', $decision)->exists()) return response()->json(['success' => false, 'msg' => 'You have already submitted a refund decision.']);

                $owner = User::where('id', $product->metadata['id'])->get()[0];
                    Notification::create($owner->id, 'success', 'Refund request closed.');

                    $refund = Refund::where('sub_id', $sub_id)->get()[0];
                    $refund->ban = $ban;
                    $refund->save();

                  //  if(Refund::where('sub_id', $sub_id)->where('issued', '=' ,'1')->exists()) return response()->json(['success' => false, 'msg' => 'Refund already issued.']);

                try{

                    $in_invoice = $sub['latest_invoice'];

                    $invoice = \Stripe\Invoice::retrieve($in_invoice);

                    $invoice_charge = $invoice->charge;
                    //$invoice_app_fee = $invoice->lines->data[0]->metadata['']
                    $invoice_app_fee = $invoice->metadata['fee'];

                    if ($invoice->metadata['fee']){
                        $invoice_app_fee = $invoice->metadata['fee'];
                    }elseif ($owner->app_fee_percent){
                        $invoice_app_fee = $owner->app_fee_percent;
                    }else if ($product->metadata['app_fee_percent']){
                        $invoice_app_fee = $product->metadata['app_fee_percent'];
                    }else {
                        $invoice_app_fee = 5;
                    }

                    // TODO: use these for more test before issuing

                    $invoice_paid = $invoice->paid;
                   // $invoice_amount_paid = $invoice->amount_paid;
                   // $invoice_customer = $invoice->customer;
                   // $invoice_application_fee_amount = $invoice->application_fee_amount;

                    if(Refund::where('sub_id', $sub_id)->where('refund_terms', '=', '100')->exists()){
                        $refund->decision = "1";
                        $refund->issued = "0";
                        $refund->save();
                        return response()->json(['success' => false, 'msg' => 'Success, funds will be paid out in Payout (you are the owner of this purchase).']);
                    }
                    if (Refund::where('sub_id', $sub_id)->where('issued', '=', NULL)->exists()){ // check if no decision on issued

                        $refund->decision = "1";
                        $refund->issued = $issued;
                        $refund->save();
                        if($issued = "1"){
                            $transfer_id = null;

                                //$transfers = array();
                            foreach (\Stripe\Transfer::all(['destination' => $owner->stripe_account_id]) as $transfer) {
                                $transfer_array = $transfer->toArray();
                                if($transfer_array['metadata']['transfer_inv'] == $in_invoice){
                                    $transfer_id = $transfer_array['id'];
                                    // \Log::info($transfer_id);
                                }
                            }

                            if($invoice->metadata['paid_out'] == 'true' && $invoice->metadata['refunded'] != 'true' && $transfer_id != null){
                                if($invoice->metadata['reversed'] != true ){

                                \Stripe\Transfer::createReversal($transfer_id);

                                \Stripe\Invoice::update(
                                    $in_invoice,
                                    ['metadata' => ['reversed' => true, 'refunded' => true]]
                                );

                                \Stripe\Refund::create([
                                    'charge' => $invoice_charge,
                                    'amount' => (($invoice->amount_paid)*((100-$invoice_app_fee)/100)),
                                    //'refund_application_fee' => false,
                                    'metadata' => [
                                        'reversed' => true,
                                        'refunded' => true
                                        ]
                                    ]);
                                }
                                $sub->delete();
                                Notification::create($owner->id, 'warning', 'Refund reversed and sent successfully.');
                            }elseif ($invoice->metadata['refunded'] != 'true'){
                            \Stripe\Invoice::update(
                                $in_invoice,
                                ['metadata' => ['reversed' => false, 'refunded' => true]]
                            );
                            \Stripe\Refund::create([
                            'charge' => $invoice_charge,
                            'amount' => (($invoice->amount_paid)*((100-$invoice_app_fee)/100)),
                            //'refund_application_fee' => false,
                            'metadata' => [
                                'refunded' => true
                                ]
                            ]);
                            Notification::create($owner->id, 'warning', 'Refund successfully sent.');
                            }
                        }
                        //return response()->json(['success' => false, 'msg' => 'SUCCESS!']);

                        // TODO: we need to cancel all upcoming invoices
                        // remove the subscription
                        // set subscription to "ended: true"
                    }else if (Refund::where('sub_id', $sub_id)->where('issued', '=', "1")->exists()){ // check if issued already
                        $refund->decision = "1";
                        $refund->save();
                        return response()->json(['success' => false, 'msg' => 'Refund already issued.']);
                    }else{ // check if 1 (decision made but denied)
                        return response()->json(['success' => false, 'msg' => 'Refund already denied.']);
                        $refund->decision = "1";
                        $refund->save();
                    }

                }catch (\Exception $e){
                    if (env('APP_DEBUG')) Log::error($e);
                }

            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
            }

            //$sub->cancel();
            // The subscription will be removed from the DB by the bot on expiration date.
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true]);
    }

}
