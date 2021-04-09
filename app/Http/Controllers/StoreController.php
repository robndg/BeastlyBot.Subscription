<?php

namespace App\Http\Controllers;

use Auth;
use App\AlertHelper;
use App\DiscordStore;
use App\StoreSettings;
#use App\Shop;
use App\User;
use App\Product;
use App\ProductRole;
use App\Price;
//use App\Products\DiscordRoleProduct;
use App\DiscordHelper;
use App\Subscription;
use App\StoreCustomer;
use App\Stat;
use App\Processors;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Exception\InvalidRequestException;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\ProductPlanController;
use App\ProcessorsController;
use App\Exception;
use App\DiscordOAuth;

class StoreController extends Controller {

    // TODO: remove this after making discord_helper load guild info without auth on store front page
   /* public function __construct() {
        $this->middleware('auth');
    }*/

    public function getStoreWelcome($shop_url){

        $discord_store = null;
        if(!StoreSettings::where('url_slug', $shop_url)->exists() && !DiscordStore::where('url', $shop_url)->exists()) {
            return abort(404);
        }else
        if(DiscordStore::where('url', $shop_url)->exists()){
            $discord_store = DiscordStore::where('url', $shop_url)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
            Log::info($discord_store->id);
        }else{
            $store_settings = StoreSettings::where('url_slug', $shop_url)->where('store_type', 1)->first();
            $discord_store = DiscordStore::where('id', $store_settings->store_id)->first();
            Log::info($discord_store->id);
        }
        if($discord_store != null){

            $owner = false;
            if(Auth::check()){
                if(/*$store_settings->members_only && */$discord_store->live == 0){
                    $user_o_auth = DiscordOAuth::where('user_id', auth()->user()->id)->first();
                    if($user_o_auth->discord_id != $discord_store->user_id){
                        //return abort(404);
                    }else{
                        $owner = true;
                    }
                    return view('store.welcome-page')->with('store_settings', $store_settings)->with('discord_store',$discord_store)->with('logged_in', true)->with('owner', $owner);
                }else{
                    return redirect('/shop'.'/'.$shop_url);
                }

            }else{

                return view('store.welcome-page')->with('store_settings', $store_settings)->with('discord_store',$discord_store)->with('logged_in', false)->with('owner', $owner);
                
            }
        }


    }


    public function getStoreFront($shop_url){



        $discord_store = null;
        if(!StoreSettings::where('url_slug', $shop_url)->exists() && !DiscordStore::where('url', $shop_url)->exists()) {
            return abort(404);
        }else
        if(DiscordStore::where('url', $shop_url)->exists()){
            $discord_store = DiscordStore::where('url', $shop_url)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
            Log::info($discord_store->id);
        }else{
            $store_settings = StoreSettings::where('url_slug', $shop_url)->where('store_type', 1)->first();
            $discord_store = DiscordStore::where('id', $store_settings->store_id)->first();
            Log::info($discord_store->id);
        }
        if($discord_store != null){ // if offline, only show to admins

            $user_o_auth = DiscordOAuth::where('user_id', auth()->user()->id)->first();
           
            $owner = false;
            if($user_o_auth->discord_id == $discord_store->user_id){
                $owner = true;
            }
            
            $member_function = true;
            if($store_settings->members_only == true){
                $member_function = false;
            }
            Log::info($user_o_auth->discord_id);
            Log::info($discord_store->user_id);
            Log::info($discord_store->live);
            if(/*($member_function) && */($discord_store->live != 1)){
                if($user_o_auth->discord_id != $discord_store->user_id){
                    Log::info("Here redirect store function");
                    return redirect('/welcome'.'/'.$store_settings->url_slug);
                }
                Log::info("Admin allowed in store not live");
            }
            
            //$owner_array = \App\User::where('id', $discord_store->user_id)->first();
 
            //$discord_helper = new DiscordHelper($owner_array);
            
            $guild_id = $discord_store->guild_id;
            $discord_helper = new DiscordHelper(auth()->user());
            $guild = $discord_helper->getGuild($guild_id);
            

            $product_roles = ProductRole::where('discord_store_id', $discord_store->UUID)->where('access', '!=', 0)->get();


            $auth = false;
            $subscriptions = false;
            $store_customer = false;

            if(Auth::check()){
                $auth = true;
                // get products already purchased, array
                //$store_customer = StoreCustomer::where()
                $subscriptions = Subscription::where('user_id', auth()->user()->id)->where('store_id', $discord_store->id)->get();
                $store_customer = StoreCustomer::where('user_id', auth()->user()->id)->where('discord_store_id', $discord_store->id)->first();
            }
            
            // three arrays
            // 1 guild access
            // 2 members products
            // 3 other products 
    
            return view('store.front-page')->with('discord_helper', $discord_helper)->with('discord_store', $discord_store)->with('store_settings', $store_settings)->with('guild', $guild)->with('auth', $auth)->with('owner', $owner)->with('product_roles', $product_roles)->with('subscriptions', $subscriptions)->with('store_customer', $store_customer);
        }else{
            // return 404
            return abort(404);
            
        }

    }

    // AUTH//
    public function getStoreProduct($shop_url, $product_url){

        $discord_store = null;
        if(!StoreSettings::where('url_slug', $shop_url)->exists() && !DiscordStore::where('url', $shop_url)->exists()) {
            return abort(404);
        }else
        if(DiscordStore::where('url', $shop_url)->exists()){
            $discord_store = DiscordStore::where('url', $shop_url)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
            Log::info($discord_store->id);
        }else{
            $store_settings = StoreSettings::where('url_slug', $shop_url)->where('store_type', 1)->first();
            $discord_store = DiscordStore::where('id', $store_settings->store_id)->first();
            Log::info($discord_store->id);
        }
        
        if(Auth::check() && $discord_store != null){

            $affiliate_id = null;
            if(\request('affiliate_id') !== null) {
                // TODO: add affiliate
            }
        
           
            $discord_helper = new DiscordHelper(auth()->user());
            $discord_o_auth = DiscordOAuth::where('discord_id', $discord_store->user_id)->first();
            $user_o_auth = DiscordOAuth::where('user_id', auth()->user()->id)->first();

            $owner = false;
            if($user_o_auth->discord_id == $discord_store->user_id){
                $owner = true;
            }
            
            $member_function = true;
            if($store_settings->members_only == true){
                $member_function = false;
            }
            Log::info($user_o_auth->discord_id);
            Log::info($discord_o_auth->discord_id);
            Log::info($discord_store->live);
            Log::info($discord_store);
            if(/*($member_function) && */($discord_store->live != 1 && ($user_o_auth->discord_id != $discord_store->user_id))){
                Log::info("Here redirect store function");
                return redirect('/welcome'.'/'.$store_settings->url_slug);
            }
 
           
            $guild_id = $discord_store->guild_id;

            Log::info($guild_id);

            Log::info($product_url);
            //$product_title_unslug = Str::title(str_replace('-', ' ', $product_title));
            //Log::info($product_title_unslug);

        /* if(Ban::where('user_id', auth()->user()->id)->where('active', 1)->where('type', 1)->where('discord_store_id', $discord_store->id)->exists() && auth()->user()->id != $discord_store->user_id){
                return abort(404);
            }*/
    
        /* if(!$owner_array->getStripeHelper()->hasActiveExpressPlan()){
                $discord_store->live = false;
                $discord_store->save();
            }*/

            /*if(!$discord_store->live && auth()->user()->id != $discord_store->user_id){
                return view('offline');
            }*/

            if(! ProductRole::where('discord_store_id', $discord_store->UUID)->where('url_slug', $product_url)->exists()) {
                // add error
                Log::info("Product Role not Found");
            } else {
                $product_role = ProductRole::where('discord_store_id', $discord_store->UUID)->where('url_slug', $product_url)->first();
            }

            $product_discord_store_uuid = $product_role->discord_store_id;
            if($discord_store->UUID != $product_discord_store_uuid){
                AlertHelper::alertError('This product does not exist for this store.');
                return abort(404);
            }
            $product_uuid = $product_role->id;
            
            $role_id = $product_role->role_id;
        
            $prices = [];
        
            $product_prices = Price::where('product_id', $product_role->id)->where('status', '=', 1)->get();
                
            foreach (["day", "week", "month", "year"] as $interval) {
                if($product_prices->where('interval', $interval)->first()){
                    $prices[$interval] = $product_prices->where('interval', $interval)->first()->price / 100;
                }else{
                    $prices[$interval] = null;
                }
                // TODO ROB2: move this to checkout;
                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
            
            }
        
            $discord_helper = new DiscordHelper(auth()->user());
            $guild = $discord_helper->getGuild($guild_id);
            $role = $discord_helper->getRole($guild_id, $role_id, 1, true);

            
            if(Processors::where('id', $discord_store->processor_id)->where('enabled', 1)->exists()){
                $processor = Processors::where('id', $discord_store->processor_id)->where('enabled', 1)->first();
                $processor_type = $processor->type;
                $processor_id = $processor->processor_id;
                //$processor_type = $processor->type; //1 stripe
                //$processor_id = $processor->processor_id;
                Log::info("Found Processor");
            } else {
                // add if null no processor for now 0
                $processor_type = 0;
                $processor_id = null;
            }

            return view('store.product-page')->with('discord_store', $discord_store)->with('store_settings', $store_settings)->with('owner', $owner)->with('guild', $guild)->with('role', $role)->with('role_id', $role_id)->with('product_role', $product_role)->with('product_prices', $product_prices)->with('affiliate_id', $affiliate_id)->with('processor_type', $processor_type)->with('processor_id', $processor_id);//->with('store_processor_selected_id', $store_processor_selected_id);
            
        }else{
            //return view('discord_login');
            return redirect('/welcome'.'/'.$store_settings->url_slug);
        }
    }


    public function checkoutSuccess($subscriptionId) { // V2 

        Log::info($subscriptionId);
        if(!Subscription::where('id', $subscriptionId)->exists()){
            //$store_customer_id = Cache::get($storeCustomerId);

            if($store_customer_id !== null) {
                AlertHelper::alertError('Store Load Page TODO.');
                Log::info("Store Customer ID here");
                return redirect('/dashboard');
               // Cache::forget($storeCustomerId);
            }else{
                AlertHelper::alertError('No Cus or Store TODO.');
                Log::info("No Customer ID here");
                return redirect('/dashboard');
            }
        }else{
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
        }
        //return redirect('/dashboard');
        
    }

}