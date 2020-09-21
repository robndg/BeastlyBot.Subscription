<?php

namespace App\Http\Controllers;

use App\AlertHelper;

use App\StripeHelper;
use App\DiscordStore;
use App\PaymentMethod;
use App\Refund;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Auth;
use App\User;
use App\Subscription;
use App\StripeConnect;
use App\PaidOutInvoice;

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

        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));

        if(!\Auth::user()->getStripeHelper()->isSubscribedToID($sub_id)) {
            return response()->json(['success' => false, 'msg' => 'This is not your subscription. Contact support']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
       

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
        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));


        $sub_id = $request['sub_id'];
        $end_now = $request['end_now'];

        if(!\Auth::user()->ownsSubscription($sub_id)) {
            return response()->json(['success' => false, 'msg' => 'This is not your subscription. Contact support']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls


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
        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));


        try {
            
            // Get subscription from stripe and cancel it.
            $sub = \Stripe\Subscription::retrieve($sub_id);

            $subscription = Subscription::where('id', $sub_id)->first();

            $latest_invoice = \Stripe\Invoice::retrieve($subscription->latest_invoice_id);

            $sub_refund_terms = $subscription->refund_terms;
            $sub_refund_days = $subscription->refund_days;
            $sub_refund_enabled = $subscription->refund_enabled;

            try {
                        $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                        if (!$product->active) throw new InvalidRequestException();

                        if(Refund::where('sub_id', $sub_id)->exists()) return response()->json(['success' => false, 'msg' => 'You have already submitted a refund request.']);

                            //$owner = User::where('id', $product->metadata['id'])->get()[0];
                            $store = DiscordStore::where('id', $subscription->store_id)->first();
                            $owner = User::where('id', $store->user_id)->first();
                            $owner_stripe = StripeConnect::where('user_id', $store->user_id)->first();
                            $user = User::where('id', $subscription->user_id)->first();
                            $sub_stripe = StripeConnect::where('user_id', $subscription->user_id)->first();
                            
                            if($sub_refund_terms != 1){
                                //add Notification::create($owner->id, 'warning', 'Subscription refund requested.');
                            }
                            //$sub_id =
                            $sub_start_date = $sub['start_date'];
                            $sub_period_end = $sub['current_period_end'];
                            //$sub_stripe_account_id = $sub_stripe->customer_id;
                            //$sub_user_id =
                            $owner_id = $owner->id;
                            //$sub_guild_name =
                            //$sub_role_name =
                            /*$sub_guild_id = $store->guild_id;
                            $sub_role_id = $subscription->metadata['role_id'];

                            $owner_guild = false;
                            if($owner_stripe->id == $sub_stripe->id){
                                $owner_guild = true;
                            }
                            $sub_application_fee = 5;
                            if($owner_guild){
                                $sub_application_fee = 0;

                                $sub_refund_enabled = 0;
                                $sub_refund_days = 0;
                                $sub_refund_terms = 100;
                            }*/
                            $sub_description = $reason;
                            $sub_plan_id = $sub['items']['data'][0]['plan']['id'];
                            $sub_amount = $sub['items']['data'][0]['plan']['amount'];
                            
                            //$sub_application_fee = $sub['application_fee_percent'];
                            $sub_application_fee = 5;
                           

                            Refund::create($latest_invoice->id, $sub_id, $sub_start_date, $sub_period_end, $user->id, $owner_id,/* $sub_stripe_account_id, $sub_guild_name, $sub_role_name, $sub_guild_id, $sub_role_id, $sub_refund_enabled, $sub_refund_days, $sub_refund_terms,*/ $sub_description, $sub_plan_id, $sub_amount, $sub_application_fee);
                            try {
                            // if no questions asked we can make the decision now.
                                if ($sub_refund_terms == 1){
                                        $issued = 1;
                                        $decision = 1;
                                        $ban = 0;
                                        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls

                                        try {
                                            // Get subscription from stripe and cancel it.
                                            $sub = \Stripe\Subscription::retrieve($sub_id);
                                            
                                            try {
                                                $product = \Stripe\Product::retrieve($sub->items->data[0]->plan->product);
                                                if (!$product->active) throw new InvalidRequestException();

                                                $refund = Refund::where('sub_id', $sub_id)->first();
                                                if($refund->decision == $decision) return response()->json(['success' => false, 'msg' => 'You have already submitted a refund decision.']);
                                                //$owner = User::where('id', $product->metadata['id'])->get()[0];
                                                    //Notification::create($owner->id, 'warning', 'Refund request closed.');
                                                   // $refund = Refund::where('sub_id', $sub_id)->first();
                                                    $refund->ban = $ban;
                                                    $refund->save();
                                                try{
                                                    $invoice = $latest_invoice;
                                                    $invoice_charge = $invoice->charge;
                                                    //$invoice_app_fee = $invoice->lines->data[0]->metadata['']
                                                    $invoice_app_fee = $sub_application_fee;

                                                    // TODO: use these for more test before issuing
                                                    $invoice_paid = $invoice->paid;
                                                   // $invoice_amount_paid = $invoice->amount_paid;
                                                   // $invoice_customer = $invoice->customer;
                                                   // $invoice_application_fee_amount = $invoice->application_fee_amount;
                                                   $user_id = $user->id;

                                                    if($owner_stripe->id != $sub_stripe->id){
                                                        $subscription->status = 5;
                                                        $subscription->save();

                                                        $refund->decision = 1;
                                                        $refund->issued = 0;
                                                        $refund->save();
                                                        return response()->json(['success' => false, 'msg' => 'Success, funds will be paid out in Payout (you are the owner of this purchase).']);
                                                        //add Notification::create($user_id, 'success', 'Refund success.');
                                                    }else{
                                                        if ($refund->issued == NULL){ // check if no decision on issued
                                                            $refund->decision = 1;
                                                            $refund->issued = $issued;
                                                            $refund->save();
                                                            if($issued = 1){ // if can issue
                                                                
                                                                
                                                                $paid_out_invoice = PaidOutInvoice::where('id', $subscription->latest_invoice_id)->first();

                                                                Log::info($paid_out_invoice);

                                                                $paid_out_bool = true;
                                                                if($paid_out_invoice == NULL){
                                                                    $paid_out_bool = false;
                                                                }
                                                                    //$transfers = array();
                                                                /*foreach (\Stripe\Transfer::all(['destination' => $owner_stripe->express_id]) as $transfer) {
                                                                    $transfer_array = $transfer->toArray();
                                                                    if($transfer_array['metadata']['transfer_inv'] == $in_invoice){
                                                                        $transfer_id = $transfer_array['id'];
                                                                        // \Log::info($transfer_id);
                                                                    }
                                                                }*/
                                                                $subscription->status = 5; // refunded&do not payout
                                                                $subscription->save();
                                                                
                                                                if($paid_out_bool == true){
                                                                   // if($paid_out_invoice->reversed != 1 && $paid_out_invoice->refunded != 1 && $paid_out_invoice->transfer_id != NULL && $subscription->status != 5){
                                                                    \Stripe\Transfer::createReversal($paid_out_invoice->transfer_id);
                                                                    $paid_out_invoice->reversed = 1;
                                                                    $paid_out_invoice->save();

                                                                    \Stripe\Refund::create([
                                                                        'charge' => $invoice_charge,
                                                                        'amount' => $invoice->amount_paid,
                                                                        //'refund_application_fee' => false,
                                                                        'metadata' => [
                                                                            'reversed' => true,
                                                                            'refunded' => true
                                                                            ]
                                                                        ]);
                                                                   // }
                                                                    $paid_out_invoice->refunded = 1;
                                                                    $paid_out_invoice->save();

                                                                    \Stripe\Invoice::update(
                                                                        $subscription->latest_invoice_id,
                                                                        ['metadata' => ['reversed' => true, 'refunded' => true]]
                                                                    );
                                                                    
                                                                    //add Notification::create($owner->id, 'warning', 'Payout reversed and sent successfully.');
                                                                    //add Notification::create($user_id, 'success', 'Refund success.');
                                                                }else if($subscription->status != 5) /*if ($paid_out_bool && $paid_out_invoice->refunded != 1)*/{
                                                               
                                                                    \Stripe\Refund::create([
                                                                    'charge' => $invoice_charge,
                                                                    'amount' => $invoice->amount_paid,
                                                                    //'refund_application_fee' => false,
                                                                    'metadata' => [
                                                                        'refunded' => true
                                                                        ]
                                                                    ]);
                                                                    \Stripe\Invoice::update(
                                                                        $subscription->latest_invoice_id,
                                                                        ['metadata' => ['reversed' => false, 'refunded' => true]]
                                                                    );
                                                                    //add Notification::create($owner->id, 'success', 'Refund successfully sent.');
                                                                    //add Notification::create($user_id, 'success', 'Refund success.');
                                                                }
                                                                   
                                                            }
                                                            //return response()->json(['success' => false, 'msg' => 'SUCCESS!']);

                                                            // TODO: we need to cancel all upcoming invoices
                                                            // remove the subscription
                                                        
                                                        }else if ($refund->issued == 1){ // check if issued already
                                               
                                                            $refund->decision = 1;
                                                            $refund->save();
                                                            return response()->json(['success' => false, 'msg' => 'Refund already issued.']);
                                                        }else{ // check if 1 (decision made but denied)
                                                            return response()->json(['success' => false, 'msg' => 'Refund already denied.']);
                                                 
                                                            $refund->decision = 1;
                                                            $refund->save();
                                                        }
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
        \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));

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

    public function impersonate($id)
    {       
        if(Auth::user()->admin == 1){
            Auth::logout(); // for end current session
            Auth::loginUsingId($id);

            return redirect()->to('/dashboard');
        }else{
            return redirect()->to('/');
        }
    }

}
