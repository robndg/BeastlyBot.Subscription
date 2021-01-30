<?php

namespace App\Http\Controllers;

use Auth;
use App\AlertHelper;
use App\Order;
use App\Products\DiscordRoleProduct;
use App\Products\ExpressProduct;
use App\Products\ProductMsgException;
use App\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\InvalidRequestException;
use App\User;
use App\DiscordStore;
use App\StripeConnect;
use App\Ban;
use App\StripeHelper;
use App\DiscordHelper;
use App\StoreCustomer;

class OrderController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /*

     $start = new DateTime($sub->latest_invoice_paid_at);
            $end = new DateTime('NOW');date('F, d', strtotime($now . ' + 2 days')
            $interval = $end->diff($start);
            $days = $interval->format('%d');
            $hours = 24 * $days + $interval->format('%h');
            $minutes = $interval->format('%i');

            */
    public function setupOrder(Request $request){
 
        /*if (! auth()->user()->hasStripeAccount()){
            return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
        }
        $express_promo = NULL;
        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle'], null); // TODO: Find UUID
                break;
                case "express":
                    $product = new ExpressProduct($request['billing_cycle'] == '1' ? env('LIVE_MONTHLY_PLAN_ID') : env('LIVE_YEARLY_PLAN_ID'));
                    $express_promo = ($request['billing_cycle'] == '1' ? 'wRSDRBPq' : 'ategNaIz');
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
        
            if(auth()->user()->getStripeHelper()->isSubscribedToProduct($product->getStripeProduct()->id)) {
                throw new ProductMsgException('You are already subscribed to that product.');
            }
            if($request['guild_id'] != NULL){
                $discord_store = DiscordStore::where('guild_id', $request['guild_id'])->first();
                
                if(Ban::where('user_id', auth()->user()->id)->where('active', 1)->where('type', 1)->where('discord_store_id', $discord_store->id)->exists() && auth()->user()->id != $discord_store->user_id){
                    throw new ProductMsgException('You are banned from purchasing products from this store.');
                }
            }

            $product->checkoutValidate();
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }*/
        $role_name = $request['role_name'];
        $ref_code = $request['ref_code']; // null for now
        $price_id = $request['price_id']; // null for now

        Log::info($request['role_id']); // todo remove
        $role_id = $request['role_id']; // todo remove
        $interval = $request['billing_cycle']; // todo remove
        Log::info($request['billing_cycle']); // todo remove for uuid
        
        $product_role = \App\ProductRole::where('role_id', 'LIKE', '%' . $role_id . '%')/*->where('active', 1)*/->first();
        // Find Product not assigned
        $product_price = \App\Price::where('product_id', $product_role->id)->where('interval', $interval)->where('status', 1)->where('assigned_to', null)->first();
        // Check if can order (total, - 1 after order)
        Log::info($product_price);
        if($product_price->max_sales != null){
            if($product_price->max_sales == 0){
                return response()->json(['success' => false, 'msg' => 'This plan has been sold out or no longer available.']);
            }
        }
        if(($product_price->start_date || $product_price->end_date)){
            $now = new DateTime('NOW');
            if($product_price->start_date >= $now){
                $diff_days = $product_price->start_date->diff($now);
                $days = $diff_days->format('%d');
                if($days == 1){
                    $str_days = " days!";
                }else{
                    $str_days = " day!";
                }
                return response()->json(['success' => false, 'msg' => 'This plan will be available in ' . $days . $str_days]);
            }
            if($product_price->end_date <= $now){
                return response()->json(['success' => false, 'msg' => 'This plan has ended']);
            }
                
        }

        $discord_store = DiscordStore::where('id', $product_role->discord_store_id)->first();
        $discord_helper = new \App\DiscordHelper(User::find($discord_store->user_id));

        $guild = $discord_helper->getGuild($discord_store->guild_id);

        //$script_setup = false; // for later script migration use
        $user_id = Auth::id();


        ///// Initiate Stripe Checkout /////

        // Check if user has stripe account, create?
        if (! auth()->user()->hasStripeAccount()) 
        return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);

        StripeHelper::setApiKey();

        // 1) Get Customer and Owner Stripe
        $customer_stripe = StripeConnect::where('user_id', $user_id)->first();
        $owner_stripe = StripeConnect::where('user_id', $discord_store->user_id)->first();

        // Update Customer Stripe to have Payment Source (TODO: check if necessary)
        \Stripe\Customer::update($customer_stripe->customer_id, ['source' => 'tok_mastercard']); 

        // Create Stripe Token for Customer vs Stripe
        $stripe_token = \Stripe\Token::create(array(
            "customer" => $customer_stripe->customer_id,
            ), array("stripe_account" => $owner_stripe->express_id, "livemode" => false)); // TODO: remove livemode false

        // Get or Create StoreCustomer in DB (will move to top when adding PayPal so we dont duplicate code)
        if(StoreCustomer::where('discord_store_id', $discord_store->id)->where('user_id', $user_id)->exists()){ 
           // $store_customer = StoreCustomer::where('store_stripe', $store_stripe->id, 'customer_stripe', $customer_stripe->id)->update(['token' => $token]);
            $store_customer = StoreCustomer::where('discord_store_id', $discord_store->id)->where('user_id', $user_id)->first();
            $store_customer->update(['customer_stripe' => $customer_stripe->id]); // Redundency if store had PayPal entry only
            $store_customer->update(['stripe_token' => $stripe_token->id]);
            $store_customer->update(['stripe_metadata' => $stripe_token]);
            $store_customer->save();
        }else{
            // Stripe, Copy from master Stripe to Owner
            $copiedCustomer = \Stripe\Customer::create(array(
                "description" => "Customer Created for "/*auth()->user()->getDiscordHelper()->getUsername()*/, // Rob TODO: maybe change or add store name
                "source" => $stripe_token
                ), array("stripe_account" => $owner_stripe->express_id, "livemode" => false));
            // Create new Customer for Store  'customer_stripe_id', 'customer_paypal_id', 'customer_cur', 'stripe_token', 'paypal_token', 'stripe_metadata', 'paypal_metadata', 'enabled', 'metadata'
            $store_customer = new StoreCustomer(['id' => Str::uuid(), 'user_id' => $user_id, 'discord_store_id' => $discord_store->id, 'customer_stripe_id' => $copiedCustomer->id, 'customer_paypal_id' => null, 'customer_cur' => 'usd', 'stripe_token' => $stripe_token->id, 'paypal_token' => null, 'stripe_metadata' => $stripe_token, 'paypal_metadata' => null, 'referal_code' => Str::random(8), 'enabled' => 0, 'ip_address' => null, 'metadata' => null]);
            $store_customer->save();
        }

        $store_customer = StoreCustomer::where('discord_store_id', $discord_store->id)->where('user_id', $user_id)->first();

        // Either copy master Product or Create/Archive New
       /* $copiedProduct_name = $product_role->id . '_' . $store_customer->id; // UUIDS
        $copiedProduct = \Stripe\Product::create([
            'name' => $copiedProduct_name,
        ]);*/

        // Keep for future idea
        //$product_price = new Price(['id' => Str::uuid(), 'product_id' => $product_id, 'stripe_price_id' => $stripe_price_id, 'paypal_price_id' => $paypal_price_id, 'price' => $price, 'cur' => $cur, 'interval' => $interval, 'assigned_to' => $assigned_to, 'start_date' => $start_date, 'end_date' => $end_date, 'max_sales' => $max_sales, 'discount' => $discount, 'discount_end' => $discount_end, 'discount_max' => $discount_max, 'status' => $status, 'metadata' => null]);
        // $product_price->save();
        // Pass the UUID so orders cant be duplicate assigned

        
        //// CREATE STRIPE SESSION ////

        //$stripe_customer = auth()->user()->getStripeHelper()->getCustomerAccount();

        $stripe = StripeHelper::getStripeClient();

        StripeHelper::setApiKey();

        $paid_amount = $product_price->price; // minus discounts
        $store_app_fee = 4;
        $next_amount = $product_price->price; // to show stats front
    
        try {
            $checkout_data = [
                'cancel_url' => 'http://localhost:8080/shop/799348185586991155',//$product->getCallbackCancelURL(), // product_role id, interval
                'success_url' => 'http://localhost:8080/success/randomsubscriptionid',/* . $copiedProduct->id . '/' . $product_price->id . '/' . $store_customer->id . '/' . $role_name,*///$product->getCallbackSuccessURL(),
                'payment_method_types' => ['card'],
                "mode" => "subscription",
                "client_reference_id" => $store_customer->id,
                "customer" => $store_customer->customer_stripe_id,
                "line_items" => [
                    [
                    "quantity" => "1",
                    "description" => "Subscription to " . $guild->name,
                    "price_data" => [
                        "currency" => "usd",
                        "product_data" => [
                        "name" => $guild->name . " Subscription",
                        "description" => "Subscription to " . $role_name, // TODO: change to discord_helper role name
                            "metadata" => [
                                "productId" => $product_role->id,
                            ]
                        ],
                        "unit_amount" => intval($paid_amount),
                        "recurring" => [
                        "interval" => $product_price->interval,
                        "interval_count" => "1"
                        ]
                    ]
                    ]
                ],
                "subscription_data" => [
                    "application_fee_percent" => 4,
                        "metadata" => [
                            /*"guildId" => "*****",
                            "userId" => "*****",
                            "discordGuildId" => "******************",
                            "discordUserId" => "******************",
                            "type" => "*******",
                            "ipAddress" => "*************",*/
                            //'product_name' => $copiedProduct_name, // For if referal can find easier to refund
                            'store_customer' => $store_customer->id, // uuid
                            'product_id' => $product_role->id, // uuid
                            'price_id' => $product_price->id, // uuid
                            'type' => 'discord_subscription', // for future 
                            "referralCode" => $ref_code // probably null
                    ]
                ]
            ];
                // TODO colby: add before or not sure where

                /*if(!empty($request['coupon_code'])) {
                    if(DiscordStore::where('guild_id', $request['guild_id'])->exists()) {
                        $store = DiscordStore::where('guild_id', $request['guild_id'])->first();
                        $checkout_data['subscription_data']['coupon'] = $store->user_id . $request['coupon_code'];
                    }
                }
                if($express_promo != NULL){
                    $checkout_data['subscription_data']['coupon'] = $express_promo;
                }*/
           
                $session = $stripe->checkout->sessions->create($checkout_data, array("stripe_account" => $owner_stripe->express_id));

                //// START SUBSCRIPTION ENTRY ////
                // Create subscription: status 0, visible 0
                // Update and make 1 and 1 on Payment Succeeded. Update with other webhooks.
                // Gives us uuid and session so no duplicates with success URL
               try{
                    $subscription = new Subscription(['id' => Str::uuid(), 'connection_type' => 1, 'session_id' => $session->id, 'sub_id' => null, 'user_id' => $user_id, 'owner_id' => $discord_store->user_id, 'store_id' => $discord_store, 'store_customer_id' => $store_customer->id, 'product_id' => $product_price->id, 'price_id' => $product_price->id, 'first_invoice_id' => null, 'first_invoice_price' => $paid_amount, 'first_invoice_paid_at' => null, 'next_invoice_price' => $next_amount, 'latest_invoice_id' => null, 'app_fee' => $store_app_fee, 'status' => 0, 'visible' => 0, 'metadata' => null]); 
                    $subscription->save();
               }catch (Exception $e){
                    // todo rob: if this catches fix that
                    Log::info($e);
               }

            return response()->json(['success' => true, 'msg' => $session->id]);

        }catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::info($e);
        }catch (Exception $e){
            Log::info($e);
        }
       



    }
    

    public function checkoutSuccessRole(Request $request) {

        Log::info($request->all());
        $success = \request('success');
        //\request('product_type')
        //$copied_product_id, $product_price_id, $customer_id, $role_name
        // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
        if (! auth()->user()->hasStripeAccount())  {
            AlertHelper::alertError('You do not have a linked stripe account.');
            return redirect('/dashboard');
        }
        $role_name = "blahhhh";
        $role_name = \request('role_name');
        AlertHelper::alertSuccess('Congratulations. ' . $role_name . ' is yours!');

       /* $store_customer = StoreCustomer::where('id', $customer_id)->first();
        $store_customer->enabled = 1;
        $store_customer->save();

        $product_price = \App\Price::where('id', $product_price_id)->first();
        if($product_price->max_sales){
            if($product_price->max_sales > 0){
                $product_price = $product_price->max_sales - 1;
                $product_price->save();
            }
        }*/
        $product = new DiscordRoleProduct(\request('guild_id'), \request('role_id'), \request('billing_cycle'), null); // TODO Rob: remove this
        return $success ? $product->checkoutSuccess() : $product->checkoutCancel();
    }


    public function checkout() {
      
        // Assign and recreate after payment
        Log::info(\request());
        $success = \request('success');

        if($success)
            AlertHelper::alertSuccess('Payment successful!');
        else 
            AlertHelper::alertInfo('Payment cancelled.');

        // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
        if (! auth()->user()->hasStripeAccount())  {
            AlertHelper::alertError('You do not have a linked stripe account.');
            return redirect('/dashboard');
        }

       

        return $success ? $product->checkoutSuccess() : $product->checkoutCancel();
    }

    public function changePlan(Request $request) {
        try {
            switch ($request['product_type']) {
                case "express":
                    $product = new ExpressProduct(null, $request['plan_id']);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }

            return $product->changePlan($request['plan_id']);
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

}
