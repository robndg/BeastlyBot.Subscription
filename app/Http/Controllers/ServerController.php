<?php

namespace App\Http\Controllers;

use App\SiteConfig;
use App\AlertHelper;
use App\DiscordStore;
#use App\Shop;
use App\User;
use App\Product;
use App\Refund;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Exception\InvalidRequestException;

class ServerController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /* --------------------------------------------------------------------
        ServerController: getServerPage

        /server/{id} -> Create Shop -> return view server

        Quick check and array push for Stripe active roles.
        1) Get $id
        2) Create shop
        3) Check if Stripe account has order
        4) Return server view

        Used:
        -- server blade: the page
    -------------------------------------------------------------------- */

    public static function getServerPage($id){
        /*
        if(! auth()->user()->getDiscordHelper()->ownsGuild($id)) {
            AlertHelper::alertError('You are not the owner of that server!');
            return redirect('/dashboard');
        }*/

        // if there is no store in the db, create one.
        if(! DiscordStore::where('guild_id', $id)->exists()) {
            $discord_store = new DiscordStore(['guild_id' => $id, 'url' => $id]);
            $discord_store->save();
        } else {
            $discord_store = DiscordStore::where('guild_id', $id)->first();
        }

        return view('server')->with('id', $id)->with('shop', $discord_store)->with('has_order', false);
    }

    /* --------------------------------------------------------------------
        ServerController: getStatusRoles

        $request /get-status-roles -> Stripe API -> return array $status_roles

        Quick check and array push for Stripe active roles.
        1) Get $request roles array from SocketHandler (res_roles_ + socket_id)
        2) Check if roles active in Stripe, of not set false
        3) Send json array of role_id, boolean

        Used:
        -- server blade (roles_script): to un-hide active roles in list
    -------------------------------------------------------------------- */

    public static function getStatusRoles(Request $request){

        $roles = $request['roles'];

        $status_roles = array();
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        try {
            foreach ($roles as $role_id) {
                try {
                    $role = \Stripe\Product::retrieve($role_id['guild_id'] . '_' . $role_id['role_id']);
                    $active = $role->active;
                    array_push($status_roles, ['product' => $role_id['role_id'], 'active' => $active, 'role_name' => $role_id['name']]);
                } catch (\Exception $e){
                    array_push($status_roles, ['product' => $role_id['role_id'], 'active' => false, 'role_name' => $role_id['name']]);
                }
            }
            return response()->json($status_roles);

        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

    }

    /* --------------------------------------------------------------------
        ServerController: getSlideRoleSettings

        /slide-roles-settings/{guild_id}/{role_id} -> Stripe API -> return view

        1) Get $guild_id $role_id
        2) Check for Product in Stripe
        3) Return product + prices

        Used:
        -- server blade (roles_script): to show roles settings
    -------------------------------------------------------------------- */

    public static function getSlideRoleSettings($guild_id, $role_id) {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            $shop_url = DiscordStore::where('guild_id', $guild_id)->value('url');
            return view('slide.slide-roles-settings')->with('shop_url', $shop_url)->with('enabled', $product->active)->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special', false)->with('prices', ProductController::getPricesForRole($guild_id, $role_id));
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return view('slide.slide-roles-settings')->with('enabled', false)->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special', false)->with('prices', ProductController::getPricesForRole($guild_id, $role_id));
        }
    }


        /* --------------------------------------------------------------------
        ServerController: getSlideSpecialRoleSettings

        /slide-roles-settings/{guild_id}/{role_id}/{discord_id}/{type} -> Stripe API -> return view

        1) Get $guild_id $role_id $discord_id $type (s or t)
        2) Check for Product in Stripe + special
        3) Return product + prices

        Used:
        -- slide-server-member: to show special roles settings
    -------------------------------------------------------------------- */

    public static function getSlideSpecialRoleSettings($guild_id, $role_id, $type, $discord_id) {
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            $shop_url = DiscordStore::where('guild_id', $guild_id)->value('url');
            $user = User::where('discord_id', '=', $discord_id)->get()[0];
            return view('slide.slide-roles-settings')->with('shop_url', $shop_url)->with('enabled', $product->active)->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special', true)->with('user', $user)->with('prices', ProductController::getPricesForSpecial($guild_id, $role_id, $discord_id));
        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
            return view('slide.slide-roles-settings')->with('enabled', false)->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special', true)->with('user', $user)->with('prices', ProductController::getPricesForSpecial($guild_id, $role_id, $discord_id));
        }
    }

    /* --------------------------------------------------------------------
        ServerController: getRecentTransactions

        $request /get-latest-transactions -> Invoices (last 7 days) -> return array $recent_invoices

        1) Use $request to get $guild_id $roles <-- TODO: why roles?
        2) Pull all invoices from last 7 days
        3) Foreach invoice if stripe_express_id in metadata
        4) Array push $recent_invoices

        Used:
        -- server blade (server_script): fillRecentPayments()

        Cache: 2 minutes
    -------------------------------------------------------------------- */

    public function getRecentTransactions(Request $request) {
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

        $guild_id = $request['guild'];

        if(! auth()->user()->getDiscordHelper()->ownsGuild($guild_id)) {
            AlertHelper::alertError('You are not the owner of that server!');
            return redirect('/dashboard');
        }

        $key = 'recent_transactions_' . $guild_id;

       /* if(Cache::has($key)) {
            return response()->json(Cache::get($key));
        }*/

        $roles = $request['roles'];

        $recent_invoices = array();
        $date2 = new DateTime();
        $unix_now = time();
        /* --V1
        DiscordStore::where('guild_id', $id)->value(''))
        $owner_id = Shop::where('id', '=', $guild_id)->value('owner_id');
        $stripe_express_id = User::where('id', '=', $owner_id)->value('stripe_express_id');
        */
        $stripe_express_id = auth()->user()->StripeConnect->express_id;
        
        
        /*old
        $stripe_account_array = \Stripe\Account::retrieve(
            $stripe_express_id
        );*/
        //Error::log($stripe_express_id);

       // $has_product = Product::where('guild', '=', $guild_id)->exists();


            try {
                $invoices = \Stripe\Invoice::all([
                    'created' => [
                        'gte' => strtotime('-7 day', $unix_now)
                    ],
                    'status' => 'paid'
                ]);

                foreach ($invoices as $invoice) {
                        $date1 = new DateTime();
                        $value = $invoice->created;
                        $date1->setTimestamp($value);
                        $interval = $date1->diff($date2);
                        $user = null;
                        //if (User::where('stripe_express_id', $invoice->lines->data[0]['plan']->metadata['stripe_express_id'])->exists())
                        //    $user = User::where('stripe_express_id', $invoice->data[0]['plan']->metadata['stripe_express_id'])->get()[0];
                        if ($invoice->metadata['stripe_express_id'] == $stripe_express_id) {
                            $product_id = $invoice->lines->data[0]['plan']->id;
                            array_push($recent_invoices, [
                                'role_id' => explode("_", $product_id)[1],
                                'id' => $invoice->id,
                                'email' => $invoice->customer_email,
                                'discord_username' => $user != null ? $user->getDiscordUsername() : $invoice->customer_email,
                                'amount' => $invoice->amount_paid / 100,
                                'created' => $invoice->created,
                                'str_date' => date('Y-m-d h:i:s', $invoice->created)
                            ]);
                        }
                        //$has_invoice = true;
                }
            }catch(\Exception $e) {}


           /* foreach($roles as $role_id => $role_val) {
                for ($i = 0; $i < 13; $i++) {

                    if ($i === 1 || $i === 3 || $i === 6 || $i === 12) {

                        try {
                            $subscriptions = \Stripe\Subscription::all([
                                'plan' => $guild_id . '_' . $role_id . '_' . $i . '_r',
                                'created' => [
                                    'gte' => strtotime('-7 day', $unix_now)
                                ],
                                'status' => 'all'
                            ]);

                            foreach ($subscriptions as $subscription) {
                                foreach (\Stripe\Invoice::all(['subscription' => $subscription->id]) as $invoice) {
                                    if ($invoice->paid) {
                                        $date1 = new DateTime();
                                        $value = $invoice->created;
                                        $date1->setTimestamp($value);
                                        $interval = $date1->diff($date2);
                                        $user = null;
                                        if (User::where('stripe_express_id', $invoice->lines->data[0]['plan']->metadata['stripe_express_id'])->exists())
                                            $user = User::where('stripe_express_id', $invoice->lines->data[0]['plan']->metadata['stripe_express_id'])->get()[0];
                                        if ($interval->days < 3) {
                                            $product_id = $invoice->lines->data[0]['plan']->product;
                                            array_push($recent_invoices, [
                                                'role_id' => explode("_", $product_id)[1],
                                                'id' => $invoice->id,
                                                'email' => $invoice->customer_email,
                                                'discord_username' => $user != null ? $user->getDiscordUsername() : $invoice->customer_email,
                                                'amount' => $invoice->amount_paid / 100,
                                                'created' => $invoice->created,
                                                'str_date' => date('Y-m-d h:i:s', $invoice->created)
                                            ]);
                                        }
                                    }
                                }
                            }
                        }catch(\Exception $e) {}
                    }
                }
            }*/

        usort($recent_invoices, function($a, $b) {
            return $b['created'] <=> $a['created'];
        });

        /*Cache::put($key, $recent_invoices, 120);*/
        return response()->json($recent_invoices);
    }

    /* --------------------------------------------------------------------
        ServerController: getTransactions

        $request /get-transactions -> Subscriptions -> Invoices -> return array $recent_invoices

        1) Use $request to get $guild_id $roles
        2) Foreach role Subscription ($i < 13)
        3) Get all Stripe Subscriptions for each role within 60 days
        4) Foreach All invoices for each Subscription id
        4) Array push paid $recent_invoices

        Used:
        -- server blade (payments_script): loadPayments()
    -------------------------------------------------------------------- */

    public function getTransactions(Request $request) {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $guild = $request['guild'];

        if(!auth()->user()->getDiscordHelper()->ownsGuild($guild)) {
            return response()->json();
        }

        $roles = $request['roles'];
        $unix_now = time();

        $recent_invoices = array();

        foreach($roles as $role_id => $role_val) {
            for ($i = 0; $i < 13; $i++) {
                if ($i === 1 || $i === 3 || $i === 6 || $i === 12) {
                    try {
                        $subscriptions = \Stripe\Subscription::all([
                            'plan' => $guild . '_' . $role_id . '_' . $i . '_r',
                            'created' => [
                                'gte' => strtotime('-60 day', $unix_now)
                            ],
                            'status' => 'all'
                        ]);

                        foreach ($subscriptions as $subscription) {
                            foreach (\Stripe\Invoice::all(['subscription' => $subscription->id]) as $invoice) {
                                if ($invoice->paid) {
                                    array_push($recent_invoices, $invoice);
                                }
                            }
                        }
                    }catch(\Exception $e) {}
                }
            }
        }

        usort($recent_invoices, function($a, $b) {
            return $b['created'] <=> $a['created'];
        });

        return response()->json($recent_invoices);
    }


    /* ServerController: getDisputes */


    public function getDisputes(Request $request) {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $guild = $request['guild'];

        if(!\auth()->user()->getDiscordHelper()->ownsGuild($guild)) {
            return response()->json();
        }

        $unix_now = time();

        $disputes_array = array();

        foreach (Refund::where('guild_id', $guild)->get() as $dispute) {
            array_push($disputes_array, $dispute);
        }

        return response()->json($disputes_array);
    }


    /* --------------------------------------------------------------------
        ServerController: updateShop

        $request /save-server-settings -> Shop Table -> return response success => true/false

        1) Use $request to get shop details
        2) Create shop table if not already
        3) Check url exists already
        4) Update table
        4) Return response

        Used:
        -- server blade (slide-server-settings): saveServerSettings()
    -------------------------------------------------------------------- */

    public function updateShop(Request $request) {
        $guild_id = $request['id'];
        $desc = $request['description'];
        $url = $request['url'];
        $refunds_enabled = $request['refunds_enabled'];
        $refunds_days = $request['refunds_days'];
        $refunds_terms = $request['refunds_terms'];

        if(!\auth()->user()->getDiscordHelper()->ownsGuild($guild_id)) 
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);

        if(DiscordStore::where('url', strtolower($url))->where('guild_id', '!=', $guild_id)->exists()) 
            return response()->json(['success' => false, 'msg' => 'That URL is already in use.']);
            
        $shop = DiscordStore::where('guild_id', $guild_id)->first();
        $shop->url = $url;
        $shop->description = $desc;
        $shop->refunds_enabled = $refunds_enabled;
        $shop->refunds_days = $refunds_days;
        $shop->refunds_terms = $refunds_terms;
        $shop->save();

        return response()->json(['success' => true]);
    }

    /* --------------------------------------------------------------------
        ServerController: updateStatus

        $request /save-go-live -> Shop Table -> return response success => true/false

        1) Use $request to shop id and status
        2) Create shop table if not already
        3) Return false if !canAcceptPayments (shows modal)
        4) Check if Subscription still active (error 2)
        5) Save shop live true (Live), false (Test)
        6) Return response

        Used:
        -- server blade: onClick #live-switch #test-switch
    -------------------------------------------------------------------- */

    public function updateStatus(Request $request) {
        $id = $request['id'];
        $live = $request['live'];

        if(!\auth()->user()->getDiscordHelper()->ownsGuild(DiscordStore::where('id', $id)->value('guild_id'))) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }else {

            if(!\auth()->user()->getStripeHelper()->hasActiveExpressPlan()){
                //return response()->json(['success' => false]);
                return response()->json(['success' => false, 'msg' => 'You cannot accept payments because you do not have an active plan.']);
            } else{
                $shop = DiscordStore::where('id', $id)->first();
                /*
                if(\auth()->user()->error == '2'){
                    return response()->json(['success' => false, 'msg' => 'Please pay partner invoice to go Live.']);
                }*/
                if($live === "Live"){
                    $shop->live = true;
                    #$shop->owner_id = (\auth()->user()->id);
                }
                else{
                    $shop->live = false;
                }
                $shop->save();
                return response()->json(['success' => true]);
            }

        }
    }



    public function memberRoleAdd(Request $request) {
        $discord_id = $request['discord_id'];
        $guild_id = $request['guild_id'];
        $role_id = $request['role_id'];
        $role_name = $request['role_name'];
        $duration = $request['duration'];
        $trial = $request['trial'];
        $duration_days = $duration * 30;
        $amount = $request['amount'];
        $nick_user = $discord_id;

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        if(!\auth()->user()->getDiscordHelper()->ownsGuild($guild_id)) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }else {
            //Shop::create($id);

            if(!\auth()->user()->canAcceptPayments()){
                return response()->json(['success' => false, 'msg' => 'You must have an active Live plan to add roles.']);
                return false;
            } else{
                if(\auth()->user()->error == '2'){
                    return response()->json(['success' => false, 'msg' => 'Please pay partner invoice to go add Roles.']);
                }
                // Check if customer already logged in once
                try{
                    $cus = User::where('discord_id', '=', $discord_id)->get()[0];
                    $cus_stripe_customer_id = $cus->stripe_customer_id;
                    $cus_id = $cus->id;
                    $nick_user = $cus->getDiscordUsername();

                    // We need to grab the Stripe Customer object from the stripe_customer_id connected to the account in our DB
                    $customer = \Stripe\Customer::retrieve($cus_stripe_customer_id);

                    // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
                    if ($customer === null || $customer->email === null || $customer->email === '') {
                        return response()->json(['success' => false, 'msg' => 'Member must log in at least once first.']);
                    }
                    foreach ($customer->subscriptions as $subscription) {
                        if ($subscription->items->data[0]->plan->active && ($subscription->items->data[0]->plan->product == $guild_id . '_' . $role_id)){
                            return response()->json(['success' => false, 'msg' => 'Member is already subscribed to that role. Remove to add new duration.']);
                        }
                    }
                // If not found just record discord_id
                }catch (\Exception $e) {
                    Log::error($e);
                    Log::info('customer not found but creating product');
                    $nick_user = $discord_id;
                }

                // Check if input is a valid duration
                /*if (!$amount >0){
                    return response()->json(['success' => false, 'msg' => 'Something here.']);
                }*/

                // Check if input is a valid duration
                if ($duration != ('1' || '3' || '6' || '12')){
                    return response()->json(['success' => false, 'msg' => 'You must enter a valid subscription duration.']);
                }

                $shop = Shop::where('id', '=', $guild_id)->get()[0];
                $owner_id = $shop->owner_id;

                // Okay now we begin adding a user to a role, or creating their checkout
                    // A*
                    try {
                        // use to test if customer or not
                        $customer_test = \Stripe\Customer::retrieve($cus_stripe_customer_id);

                        if ($amount != (null || NULL)){
                            throw new InvalidRequestException();
                        }
                        if ($customer_test == (null || NULL)){
                            throw new InvalidRequestException();
                        }
                        // Yes customer
                        // A) Free first term
                        if($trial == "1"){
                            \Stripe\Subscription::create([
                                'customer' => $cus_stripe_customer_id,
                                'items' => [
                                [
                                    'plan' => ($guild_id . '_' . $role_id . '_' . $duration . '_r'),
                                ],
                                ],
                                'trial_from_plan' => true,
                                'trial_period_days' => $duration_days,
                                'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                            ]);





                            // TODO ***: not sure how to get the subscription created from above for this order function

                            /*$order = new Order();
                            $order->id = sub id?;
                            $order->save();*/




                            return response()->json(['success' => true, 'msg' => 'Adding role to member.']);

                        // B) Just doing a regular checkout
                        }else{
                            \Stripe\Subscription::create([
                                'customer' => $cus_stripe_customer_id,
                                'items' => [
                                [
                                    'plan' => ($guild_id . '_' . $role_id . '_' . $duration . '_r'),
                                ],
                                ],
                                'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                            ]);
                            return response()->json(['success' => true, 'msg' => 'Member sent invoice.']);
                        }

                        // First. Must send invoice then add role somehow?

                    // B*
                    // Subscription plan does not exist with that price (even if trial), OR if customer does not exist yet
                    } catch (\Exception $e) {
                        Log::error($e);
                        // 1A) Create the plan for role for free trial (id and nickname: t)
                        if($trial == "1"){
                            try{
                                $plan_t = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_t_' . $discord_id);
                                return response()->json(['success' => false, 'msg' => 'This trial has already been created for this user. Please delete it and try again.']);
                            } catch (\Exception $e) {
                            // have to add a try here for plan already exists
                                \Stripe\Plan::create([
                                    "amount" => $amount * 100,
                                    "interval" => "month",
                                    "interval_count" => $duration,
                                    "product" => $guild_id . '_' . $role_id,
                                    "currency" => "usd",
                                    'metadata' => [
                                        'id' => $owner_id,
                                        'stripe_express_id' => $shop->stripe_express_id,
                                        'app_fee_percent' => $shop->app_fee_percent,
                                        'payout_delay' => $shop->stripe_delay_days

                                    ],
                                    "id" => ($guild_id . '_' . $role_id . '_' . $duration . '_r_t_' . $discord_id),
                                    "nickname" => ($role_name . ' - ' . $duration . '/mo - t - ' . $nick_user),
                                ]);
                                try{
                                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                                    $cus_stripe_customer_id = User::where('discord_id', '=', $discord_id)->value('stripe_express_id');
                                    Log::info($cus_stripe_customer_id);
                                    $retrieved_cus = \Stripe\Account::retrieve(
                                        $cus_stripe_customer_id
                                    );
                                    Log::info("yeahh");

                                    $input_this = $guild_id;

                                    \Stripe\Account::update(
                                        $cus_stripe_customer_id,
                                        ['metadata' => ['order_id' => $input_this]]
                                    );
                                }catch (\Exception $e){
                                    # we add this when they visit the shop instead
                                    Log::error($e);
                                }
                            }
                        }else{
                            try{
                                $plan_s = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id);
                                return response()->json(['success' => false, 'msg' => 'This special has already been created for this user. Please delete it and try again.']);
                            } catch (\Exception $e) {
                            // 1B) Create the plan for role for special discount (id and nickname: s)
                            // have to add a try here for plan already exists
                                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                                \Stripe\Plan::create([
                                    "amount" => $amount * 100,
                                    "interval" => "month",
                                    "interval_count" => $duration,
                                    "product" => $guild_id . '_' . $role_id,
                                    "currency" => "usd",
                                    'metadata' => [
                                        'id' => $owner_id,
                                        'stripe_express_id' => $shop->stripe_express_id,
                                        'app_fee_percent' => $shop->app_fee_percent,
                                        'payout_delay' => $shop->stripe_delay_days

                                    ],
                                    "id" => ($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id),
                                    "nickname" => ($role_name . ' - ' . $duration . '/mo - s - ' . $nick_user),
                                ]);
                                try{
                                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                                    $cus_stripe_customer_id = User::where('discord_id', '=', $discord_id)->value('stripe_express_id');
                                    Log::info($cus_stripe_customer_id);
                                    $retrieved_cus = \Stripe\Account::retrieve(
                                        $cus_stripe_customer_id
                                    );
                                    Log::info("yeahh");

                                    $input_this = $guild_id;

                                    \Stripe\Account::update(
                                        $cus_stripe_customer_id,
                                        ['metadata' => ['order_id' => $input_this]]
                                    );
                                    Log::info("yeahhhh");
                                }catch (\Exception $e){
                                    # we add this when they visit the shop instead
                                    Log::error($e);
                                }
                            }
                        }
                        // 1A or 1B
                        Product::createProduct($guild_id, $role_id, $duration);
                        return response()->json(['success' => true, 'msg' => 'Plan created user must visit store to enable.']);
                        //
                        /*try{
                            // 2A) New subscription with free trial (using plan above)
                            if($trial == ""){
                                \Stripe\Subscription::create([
                                    'customer' => $cus_stripe_customer_id,
                                    'items' => [
                                    [
                                        'plan' => ($guild_id . '_' . $role_id . '_' . $duration . '_r_t_' . $discord_id),
                                    ],
                                    ],
                                    //'trial_period_days' => $duration_days,
                                    'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                                ]);
                               // Have user log into Shop and check out with product
                               return response()->json(['success' => true, 'msg' => 'Trial role created user must visit store to activate.']);

                            // 2B) New subscription without free trial (using plan above)
                            }else{
                                \Stripe\Subscription::create([
                                    'customer' => $cus_stripe_customer_id,
                                    'items' => [
                                    [
                                        'plan' => ($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id),
                                    ],
                                    ],
                                    'metadata' => ['id' => $owner_id, 'discord_id' => $discord_id]
                                ]);
                                return response()->json(['success' => true, 'msg' => 'Special role created user must visit store to activate.']);
                                // TODO: send invoice, then somehow check paid to add role
                            }
                        }catch(\Exception $e) {
                            Log::error($e);
                            return response()->json(['success' => true, 'msg' => 'Plan created user must visit store to enable.']);
                        }*/

                    }

                return response()->json(['success' => false, 'msg' => 'error.']);
            }

        }
    }

    public function getSpecialRoles(Request $request){
        $roles = $request['roles'];
        $guild_id = $request['guild_id'];
        //$roles = [];
        $durations = array(1, 3, 6, 12);
        $discord_id = (\auth()->user()->DiscordOAuth->discord_id);
        $plans = [];
        $role_ids = [];

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        foreach ($roles as $role_id) {
            //if (!in_array($role_id, $role_ids)) {
                foreach($durations as $duration){
                    try {
                        $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id['role_id'] . '_' . $duration . '_r_t_' . $discord_id);
                        array_push($plans, ['plan' => $plan]);
                        $role_id_actual = $role_id['role_id'];
                        array_push($roles_ids, ['role_id' => $role_id_actual]);
                        //array_push($role_ids, ['role_ids' => $role_id['role_id']]);
                    } catch (\Exception $e) {
                    // Log::error($e);
                        try{
                            $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id['role_id'] . '_' . $duration . '_r_s_' . $discord_id);
                            array_push($plans, ['plan' => $plan]);
                            $role_id_actual = $role_id['role_id'];
                            array_push($roles_ids, ['role_id' => $role_id_actual]);
                        }catch (\Exception $e) {
                            Log::error($e);
                        }
                    }
                }
           // }
        }

        return $plans;

    }
















    // old. not in use Feb 20/20

    public function getSlideRolePrices($guild_id, $role_id) {
        return view('slide.slide-roles-prices')->with('guild_id', $guild_id)->with('role_id', $role_id)->with('prices', ProductController::getPricesForRole($guild_id, $role_id));
    }

    // old. not in use Feb 20/20

    public static function getActiveRoles(Request $request){
        //\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $guild_id = $request['guild_id'];
        $role_id = $request['role_id'];

        try {

            // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                // TODO: maybe send an array of roles from socket and check here instead of using ajax foreach

                // Find products that are enabled, if no pricing plans disable. Only show true for enabled and pricing plan
                    try {
                        $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
                        if($product->active){
                            /* */
                            $prices_total = 0;
                            $prices = [];
                            $durations = array(1, 3, 6, 12);
                            try {

                                foreach ($durations as $duration) {
                                    try {
                                        $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r');
                                        $prices_total += $plan->amount;
                                    } catch (\Exception $e){
                                        $prices_total += 0;
                                    }
                                }
                                if($prices_total == 0){
                                    // Id like to disable it but if its active for a trial/special role add then we cant
                                    /*\Stripe\Product::update(
                                        $guild_id . '_' . $role_id,
                                        ['active' => false]
                                    );*/
                                    // was enabled now disabled
                                    return response()->json(['success' => false]);
                                }else{
                                    // the good ones
                                    return response()->json(['success' => true]);
                                }
                                //return true;
                            } catch (\Exception $e) {
                                if (env('APP_DEBUG')) Log::error($e);
                            }
                            /* */
                            // if we wanna remove the slow top code we just use this
                            // return response()->json(['success' => true]);
                        }else{
                            // the disabled guys
                            return response()->json(['success' => false]);
                        }
                    } catch (\Exception $e) {
                        return response()->json(['success' => false]);
                        Error::log($e);
                    }


        } catch (\Exception $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

    }


}


