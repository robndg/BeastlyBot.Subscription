<?php

namespace App\Http\Controllers;

//use App\plan;
use App\AlertHelper;
use Illuminate\Http\Request;
use App\DiscordStore;
use App\ProductRole;
use App\Ban;
use App\StripeConnect;
use App\StripeHelper;
use App\Price;
use App\Product;
use App\User;
use App\DiscordOAuth;
use App\Processors;
use App\StoreCustomer;
use App\Subscription;

use Illuminate\Support\Facades\Cache;

use App\Products\DiscordRoleProduct;
use App\Products\ProductMsgException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StoreCustomerController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }


    public function setupOrder(Request $request){
 
        Log::info("setupOrder Log");
        // TODO COLBY: make this get from price ID:

        /*$role_name = $request['role_name'];
        $ref_code = $request['ref_code']; // null for now
        $price_id = $request['price_id']; // null for now

        Log::info($request['role_id']); // todo remove
        $role_id = $request['role_id']; // todo remove
        $interval = $request['billing_cycle']; // todo remove
        Log::info($request['billing_cycle']); // todo remove for uuid*/

        $ref_code = '301838193018273793';//$request['ref_code'];
        $priceId = $request['price-id'];

        $product_price = \App\Price::where('id', $priceId)->first();
        
        $product_role = \App\ProductRole::where('id', $product_price->product_id)/*->where('active', 1)*/->first(); // TODO: move this below price to get product from price
        // Find Product not assigned
       
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

        $discord_store = DiscordStore::where('UUID', $product_role->discord_store_id)->first();
        $store_settings = StoreSettings::where('id', $discord_store->id)->first();
        $discord_o_auth = DiscordOAuth::where('discord_id', $discord_store->user_id)->first();
        $discord_helper = new \App\DiscordHelper(User::where('id', $discord_o_auth->user_id)->first());

        $guild = $discord_helper->getGuild($discord_store->guild_id);

        //$script_setup = false; // for later script migration use
        $user_id = auth()->user()->id;


        ///// Initiate Stripe Checkout /////

        // Check if user has stripe account, create?
        //if (! auth()->user()->hasStripeAccount()) 
        //return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);

      

        // 1) Get Customer and Owner Stripe
        $customer_stripe = StripeConnect::where('user_id', $user_id)->first();
        $owner_stripe = StripeConnect::where('user_id', $discord_store->user_id)->first();

        $processor = Processors::where('user_id', $discord_o_auth->user_id)->where('enabled', 1)->first(); // TODOV2: add storeId
        $processor_type = $processor->type; //1 stripe
        $processor_id = $processor->processor_id;

        if($processor_type == 1){ // STRIPE
            StripeHelper::setApiKey();

            // Update Customer Stripe to have Payment Source (TODO: check if necessary)
            \Stripe\Customer::update($customer_stripe->customer_id, ['source' => 'tok_mastercard']); 

            // Create Stripe Token for Customer vs Stripe
            $stripe_token = \Stripe\Token::create(array(
                "customer" => $customer_stripe->customer_id,
                ), array("stripe_account" => $processor_id, "livemode" => false)); // TODO: remove livemode false

            // Get or Create StoreCustomer in DB (will move to top when adding PayPal so we dont duplicate code)
            if(StoreCustomer::where('discord_store_id', $discord_store->id)->where('user_id', $user_id)->exists()){ 
            // $store_customer = StoreCustomer::where('store_stripe', $store_stripe->id, 'customer_stripe', $customer_stripe->id)->update(['token' => $token]);
                $store_customer = StoreCustomer::where('discord_store_id', $discord_store->id)->where('user_id', $user_id)->first();
                $store_customer->update(['customer_stripe' => $customer_stripe->id]); // Redundency if store had PayPal entry only
                $store_customer->update(['stripe_token' => $stripe_token->id]);
                $store_customer->update(['stripe_metadata' => $stripe_token]);
                $store_customer->save();
            }else{
                $customerDiscordId = DiscordOAuth::where('user_id', auth()->user()->id)->first()->discord_id;
                // Stripe, Copy from master Stripe to Owner
                $copiedCustomer = \Stripe\Customer::create(array(
                    "description" => "Customer Created for "/*auth()->user()->getDiscordHelper()->getUsername()*/, // Rob TODO: maybe change or add store name
                    "source" => $stripe_token
                    ), array("stripe_account" => $processor_id, "livemode" => false));
                // Create new Customer for Store  'customer_stripe_id', 'customer_paypal_id', 'customer_cur', 'stripe_token', 'paypal_token', 'stripe_metadata', 'paypal_metadata', 'enabled', 'metadata'
                $store_customer = new StoreCustomer(['id' => Str::uuid(), 'user_id' => $user_id, 'discord_store_id' => $discord_store->id, 'customer_stripe_id' => $copiedCustomer->id, 'customer_paypal_id' => null, 'customer_cur' => 'usd', 'stripe_token' => $stripe_token->id, 'paypal_token' => null, 'stripe_metadata' => $stripe_token, 'paypal_metadata' => null, 'referal_code' => $customerDiscordId/*Str::random(8)*/, 'enabled' => 0, 'ip_address' => null, 'metadata' => null]);
                $store_customer->save();
            }
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

        $new_sub_uuid = (string) Str::uuid(); // todo make this permenant Uuid::generate(4)->string
        Log::info($new_sub_uuid);
    
        try {
            $checkout_data = [
                'cancel_url' => 'http://localhost:8080/shop/' . $store_settings->url_slug . '/' . $product_role->url_slug,//$product->getCallbackCancelURL(), // product_role id, interval
                'success_url' => 'http://localhost:8080/checkout-subscription-success/' . $new_sub_uuid . '/' . $store_customer->id . '/' . $product_role->id,/* . $copiedProduct->id . '/' . $product_price->id . '/' . $store_customer->id . '/' . $role_name,*///$product->getCallbackSuccessURL(),
                'payment_method_types' => ['card'],
                "mode" => "subscription",
                "client_reference_id" => $store_customer->id,
                "customer" => $store_customer->customer_stripe_id,
                "line_items" => [
                    [
                    "quantity" => "1",
                    "description" => "Subscription to " . $product_role->title . " in " . $guild->name,
                    "price_data" => [
                        "currency" => "usd",
                        "product_data" => [
                        "name" => $product_role->title . " in " . $guild->name,
                        "description" => "Subscription to " . $product_role->title . " in " . $guild->name, // TODO: change to discord_helper role name
                            "metadata" => [
                                "productId" => $product_role->id,
                                "subscriptionId" => $new_sub_uuid,
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
                            "subscriptionId" => $new_sub_uuid,
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
           
                $session = $stripe->checkout->sessions->create($checkout_data, array("stripe_account" => $processor_id));
                $subscription = new Subscription(['id' => $new_sub_uuid, 'connection_type' => 1, 'session_id' => $session->id, 'sub_id' => "", 'user_id' => $user_id, 'owner_id' => $discord_store->user_id, 'store_id' => $discord_store->id, 'store_customer_id' => $store_customer->id, 'product_id' => $product_role->id, 'price_id' => $product_price->id, 'first_invoice_id' => null, 'first_invoice_price' => $paid_amount, 'first_invoice_paid_at' => null, 'next_invoice_price' => $next_amount, 'latest_invoice_id' => null, 'latest_invoice_amount' => null, 'app_fee' => $store_app_fee, 'status' => 0, 'visible' => 0, 'metadata' => null]); 
                $subscription->save();
                Cache::put($new_sub_uuid, $subscription);


            return response()->json(['success' => true, 'msg' => $session->id]);
        

        }catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::info($e);
        }catch (Exception $e){
            Log::info($e);
        }
       



    }
    
    public function checkoutSubscriptionSuccessRole($subscriptionId, $customerId, $productId) { // V# 
        Log::info($subscriptionId);
        if(!Subscription::where('id', $subscriptionId)->exists()){
            //$store_customer_id = Cache::get($store_customer);

            if(StoreCustomer::where('id', $customerId)->exists()) {
                AlertHelper::alertError('Store Customer Page TODO.');
                Log::info("Store Customer ID here");
                Log::info($customerId);
                return redirect('/dashboard');
                //Cache::forget($storeCustomerId);
            }else{
                AlertHelper::alertError('No Cus or Store TODO.');
                Log::info("No Customer ID here");
                return redirect('/dashboard');
            }
        }elseif(Subscription::where('id', $subscriptionId)->exists()){
            $subscription = Subscription::where('id', $subscriptionId)->first();

            $store = DiscordStore::where('id', $subscription->store_id)->first();

            if ($store == null) {
                AlertHelper::alertError('Invalid store.');
                return redirect('/dashboard');
            }

            $product = \App\ProductRole::where('id', $subscription->product_id)->first();
            $store_settings = \App\StoreSettings::where('store_id', $store->id)->first();

            if ($product == null) {
                AlertHelper::alertError('Invalid product.');
                //return redirect('/dashboard');
                return redirect('/shop'.'/'.$store_settings->url_slug);
            }
            $interval_string = "month"; // TODO2: get from $subscription or from $price (from $product)
            //AlertHelper::alertSuccess('You are now an ' . $product->title . '.' .  ' You will automatically be billed every 1 ' . $interval_string . '(s) starting today.');
            
            //return redirect('/welcome'.'/'.$store_settings->url_slug);

            if($subscription->user_id != auth()->user()->id){
                AlertHelper::alertError('This is not your subscription.');
                return redirect('/dashboard');
            }
            if($subscription->connection_type == 1){
                if (! auth()->user()->hasStripeAccount())  {
                    Log::info("You do not have a linked stripe account Please relogin.");
                    AlertHelper::alertError('You do not have a linked stripe account Please relogin.');
                    //return redirect('/dashboard');
                    return redirect('/shop'.'/'.$store_settings->url_slug);
                }
            }
            if($subscription->status <= 2){ // 0 payment processing // 1 payment success // 2 role added to discord
                if($subscription->status == 0){
                    AlertHelper::alertSuccess('Just a second...');
                    // waiting on Stripe webhook, show product processing
                }else{
                    AlertHelper::alertSuccess('Congratulations. Subscription Success!');
                }
                $subscription->visible = 1;
                $subscription->save();
                //return redirect('/account/subscriptions'); // Todo Rob: make store sub manager page
                return redirect('/shop'.'/'.$store_settings->url_slug);
            }else{
                AlertHelper::alertInfo('Subscription already added or cancelled.');
            // return redirect('/account/subscriptions'); // Todo Rob: make store sub manager page
                return redirect('/shop'.'/'.$store_settings->url_slug);
            }
        }else{
            AlertHelper::alertError('Colby must do this plz.');
            return redirect('/dashboard');
        }

      

        //return $success ? $product->checkoutSuccess() : $product->checkoutCancel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) { 
     
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(plan $plan)
    {
        //
    }
}
