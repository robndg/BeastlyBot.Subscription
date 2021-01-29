<?php

namespace App\Http\Controllers;

use App\AlertHelper;

use App\Order;
use App\Products\DiscordRoleProduct;
use App\Products\ExpressProduct;
use App\Products\ProductMsgException;
use App\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\InvalidRequestException;
use App\User;
use App\DiscordStore;
use App\StripeConnect;
use App\Ban;
use App\StripeHelper;
use App\DiscordHelper;

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
 
        if (! auth()->user()->hasStripeAccount()) 
            return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
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
        }
        Log::info($request['role_id']);
        $role_id = $request['role_id'];
        $interval = $request['billing_cycle'];
        Log::info($request['billing_cycle']);
        $product_role = \App\ProductRole::where('role_id', 'LIKE', '%' . $role_id . '%')/*->where('active', 1)*/->first();
        Log::info($product_role);
        // Find Product not assigned
        $product_price = \App\Price::where('product_id', $product_role->id)->where('interval', $interval)->where('status', 1)->where('assigned_to', null)->first();
        // Check if can order (total, - 1 after order)
        Log::info($product_price);
        if($product_price->max_sales){
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

        // Check if user has stripe account, create 
        if (! auth()->user()->hasStripeAccount()) 
        return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
        // Create price in Stripe (or PayPal)




        StripeHelper::setApiKey();

        $customer_stripe = StripeConnect::where('user_id', auth()->user()->id)->first();
        $owner_stripe = StripeConnect::where('user_id', $discord_store->user_id)->first();

        \Stripe\Customer::update($customer_stripe->customer_id, ['source' => 'tok_mastercard']);

        $token = \Stripe\Token::create(array(
            "customer" => $customer_stripe->customer_id,
            ), array("stripe_account" => $owner_stripe->express_id, "livemode" => false));

            
        $copiedCustomer = \Stripe\Customer::create(array(
            "description" => "Customer Created: " . auth()->user()->getDiscordHelper()->getUsername(), // Rob TODO: change to add UUID for StripeConnect
            "source" => $token // obtained with Stripe.js
            ), array("stripe_account" => $owner_stripe->express_id, "livemode" => false));

        Log::info($copiedCustomer);
      
        $plan = \Stripe\Price::create([
            'unit_amount' => intval($product_price->price),
            'currency' => $product_price->cur,
            'recurring' => ['interval' => $product_price->interval],
            'product' => $product->getStripeID(), // TODO: change to $product_price->id
            /*'product_data' => [
                'name' => auth()->user()->getDiscordHelper()->getUsername(),
            ],*/
            'metadata' => [
                'price' => $product_price->id, 
                'customer' => $copiedCustomer->id,
                'product' => $product->id,
            ],
        ]);

         // Log::info($plan);
        
         // $stripe_price_id = $plan->id;

          //$product_price = new Price(['id' => Str::uuid(), 'product_id' => $product_id, 'stripe_price_id' => $stripe_price_id, 'paypal_price_id' => $paypal_price_id, 'price' => $price, 'cur' => $cur, 'interval' => $interval, 'assigned_to' => $assigned_to, 'start_date' => $start_date, 'end_date' => $end_date, 'max_sales' => $max_sales, 'discount' => $discount, 'discount_end' => $discount_end, 'discount_max' => $discount_max, 'status' => $status, 'metadata' => null]);
         // $product_price->save();
        // Pass the UUID so orders cant be duplicate assigned

        
        // CREATE SESSION, 

        $stripe_customer = auth()->user()->getStripeHelper()->getCustomerAccount();

        $stripe = StripeHelper::getStripeClient();

        StripeHelper::setApiKey();
        try {

            $checkout_data = [ // TODO Colby: Either use checkout_data below this one (plan->id), or make this work and add metadata so we know what role to add with webhook
                'payment_method_types' => ['card'],
                'line_items' => [[
                  'price_data' => [
                    'currency' => $product_price->cur,
                    'recurring' => ['interval' => $product_price->interval],
                    'product_data' => [
                      'name' => auth()->user()->getDiscordHelper()->getUsername() . ' - Product',
                    ],
                    //'product' => $plan->id,  // wish this could work, but i think has to be made each time in product_data (unless we can test in non-test mode)
                    'unit_amount' => intval($product_price->price),
                  ],
                  'quantity' => 1,
                ]],
                'customer' => $copiedCustomer->id,
                'mode' => 'subscription',
                'success_url' => $product->getCallbackSuccessURL(),
                'cancel_url' => $product->getCallbackCancelURL(),
            ];

           /* $checkout_data = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                'price' => $plan->id,
                'quantity' => 1,
                ]],
                'subscription_data' => [
                'application_fee_percent' => 4,
                ],
                'mode' => 'subscription',
                'success_url' => $product->getCallbackSuccessURL(),
                'cancel_url' => $product->getCallbackCancelURL(),
                'customer' => $copiedCustomer->id, // Rob TODO: Store in DB or use find
                'client_reference_id' => auth()->user()->id, // Rob TODO: change to add UUID for User
            ];*/


           /*$checkout_data = [
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                
                'line_items' => [[   
                   
                         'price' => $plan->id,
                        'quantity' => 1,
                        ]
                    ],
                'subscription_data' => [
                    'application_fee_percent' => 4,
                ],
                'success_url' =>  $product->getCallbackSuccessURL(),
                'cancel_url' => $product->getCallbackCancelURL(),
                'customer' => $copiedCustomer->id, // Rob TODO: Store in DB or use find
            ];*/


            //Log::info($session);
       
            /* $checkout_data = [
                    'payment_method_types' => ['card'],
                    'mode' => 'subscription',
                    'subscription_data' => [
                        'application_fee_percent' => 5,
                        'items' => [[
                            'plan' => $plan->id,
                            'quantity' => '1'
                        ]]
                    ],
                    'success_url' => $product->getCallbackSuccessURL(),
                    'cancel_url' => $product->getCallbackCancelURL(),
                    'customer' => $stripe_customer->id,
                ];*/

               /* if(!empty($request['coupon_code'])) {
                    if(DiscordStore::where('guild_id', $request['guild_id'])->exists()) {
                        $store = DiscordStore::where('guild_id', $request['guild_id'])->first();
                        $checkout_data['subscription_data']['coupon'] = $store->user_id . $request['coupon_code'];
                    }
                }
                if($express_promo != NULL){
                    $checkout_data['subscription_data']['coupon'] = $express_promo;
                }*/
                
                Log::info($owner_stripe->express_id);
                $session = $stripe->checkout->sessions->create($checkout_data, array("stripe_account" => $owner_stripe->express_id));
                Log::info($session);
                return response()->json(['success' => true, 'msg' => $session->id]);
        //Log::info($session);
        }catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::info($e);
        }catch (Exception $e){
            Log::info($e);
        }
       



    }
    public function setup(Request $request) {
        if (! auth()->user()->hasStripeAccount()) 
            return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);
        $express_promo = NULL;
        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle']); // TODO: Find UUID
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
        }


       
        // TODO ROB
        // COPY STRIPE CUSTOMER to store / check if CUSTOMER in DB for this Store -> use UUID in session
            // TODO: check if shop uses Stripe or Paypal first

        // Copy Stripe Customer to Connect Owner Stripe 
        
        StripeHelper::setApiKey();

        $customer_stripe = StripeConnect::where('user_id', auth()->user()->id)->first();
        $owner_stripe = StripeConnect::where('user_id', $discord_store->user_id)->first();

       // \Stripe\Customer::update($customer_stripe->customer_id, ['source' => 'tok_mastercard']);

        $token = \Stripe\Token::create(array(
            "customer" => $customer_stripe->customer_id,
            ), array("stripe_account" => $owner_stripe->express_id));

            
        $copiedCustomer = \Stripe\Customer::create(array(
            "description" => "Customer for " . $owner_stripe->id, // Rob TODO: change to add UUID for StripeConnect
            "source" => $token // obtained with Stripe.js
            ), array("stripe_account" => $owner_stripe->express_id));

            Log::info($copiedCustomer);
        // TODO: PayPal Copy

        
        // CREATE SESSION, 


        $stripe_customer = auth()->user()->getStripeHelper()->getCustomerAccount();


        $stripe = StripeHelper::getStripeClient();

       // StripeHelper::setApiKey();

          $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price' => $product->getStripePlan()->id,
              'quantity' => 1,
            ]],
            'subscription_data' => [
              'application_fee_percent' => 5,
            ],
            'mode' => 'subscription',
            'success_url' => $product->getCallbackSuccessURL(),
            'cancel_url' => $product->getCallbackCancelURL(),
            'customer' => $copiedCustomer->id, // Rob TODO: Store in DB or use find
            'client_reference_id' => auth()->user()->id, // Rob TODO: change to add UUID for User
          ], ['stripe_account' => $owner_stripe->express_id]);


       /* $checkout_data = [
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'subscription_data' => [
                'application_fee_percent' => 5,
                'items' => [[
                    'plan' => $product->getStripePlan()->id,
                    'quantity' => '1'
                ]]
            ],
            'success_url' => $product->getCallbackSuccessURL(),
            'cancel_url' => $product->getCallbackCancelURL(),
            'customer' => $stripe_customer->id,
        ];*/

        /*if(!empty($request['coupon_code'])) {
            if(DiscordStore::where('guild_id', $request['guild_id'])->exists()) {
                $store = DiscordStore::where('guild_id', $request['guild_id'])->first();
                $checkout_data['subscription_data']['coupon'] = $store->user_id . $request['coupon_code'];
            }
        }
        if($express_promo != NULL){
            $checkout_data['subscription_data']['coupon'] = $express_promo;
        }
        
        $stripe = StripeHelper::getStripeClient();
        $session = $stripe->checkout->sessions->create($checkout_data, array("stripe_account" => StripeConnect::where('user_id', $discord_store->user_id)->first()));*/
        
        return response()->json(['success' => true, 'msg' => $session->id]);
    }

    public function checkout() {
      
        // Assign and recreate after payment

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

        try {
            switch (\request('product_type')) {
                case "discord":
                    $product = new DiscordRoleProduct(\request('guild_id'), \request('role_id'), \request('billing_cycle'));
                break;
                case "express":
                    $product = new ExpressProduct(\request('plan_id'));
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
        } catch(ProductMsgException $e) {
            AlertHelper::alertWarning($e->getMessage());
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            AlertHelper::alertWarning($e->getError()->message);
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
