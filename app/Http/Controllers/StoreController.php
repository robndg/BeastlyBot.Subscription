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

    public function getStoreWelcome($store_slug){

        if(! DiscordStore::where('url', $store_slug)->exists()) {
            return abort(404);
        } else {
            $discord_store = DiscordStore::where('url', $store_slug)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
        }

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
                return redirect('/shop'.'/'.$store_slug);
            }

        }else{

            return view('store.welcome-page')->with('store_settings', $store_settings)->with('discord_store',$discord_store)->with('logged_in', false)->with('owner', $owner);
            
        }


    }


    public function getStoreFront($shop_title){



        $discord_store = null;
        if(! DiscordStore::where('url', $shop_title)->exists()) {
            return abort(404);
        } else {
            $discord_store = DiscordStore::where('url', $shop_title)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
            Log::info($discord_store->id);
        }
        if($discord_store != null){ // if offline, only show to admins

            $user_o_auth = DiscordOAuth::where('user_id', auth()->user()->id)->first();
            
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
    
            return view('store.front-page')->with('discord_helper', $discord_helper)->with('discord_store', $discord_store)->with('guild', $guild)->with('auth', $auth)->with('product_roles', $product_roles)->with('subscriptions', $subscriptions)->with('store_customer', $store_customer);
        }else{
            // return 404

            
        }

    }

    // AUTH//
    public function getStoreProduct($shop_title, $product_title){

        $discord_store = null;
        if(! DiscordStore::where('url', $shop_title)->exists()) {
            return abort(404);
        } else {
            $discord_store = DiscordStore::where('url', $shop_title)->first();
            $store_settings = StoreSettings::where('store_id', $discord_store->id)->where('store_type', 1)->first();
            Log::info($discord_store->id);
        }
        
        if(Auth::check()){

            $affiliate_id = null;
            if(\request('affiliate_id') !== null) {
                // TODO: add affiliate
            }
        
           
            $discord_helper = new DiscordHelper(auth()->user());
            $discord_o_auth = DiscordOAuth::where('discord_id', $discord_store->user_id)->first();
            $user_o_auth = DiscordOAuth::where('user_id', auth()->user()->id)->first();
            
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

            Log::info($product_title);
            $product_title_unslug = Str::title(str_replace('-', ' ', $product_title));
            Log::info($product_title_unslug);

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

            if(! ProductRole::where('discord_store_id', $discord_store->UUID)->where('title', 'LIKE', '%' . $product_title_unslug . '%')->exists()) {
                // add error
                Log::info("Product Role not Found");
            } else {
                $product_role = ProductRole::where('discord_store_id', $discord_store->UUID)->where('title', 'LIKE', '%' . $product_title_unslug . '%')->first();
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

            
            $processor = Processors::where('user_id', $discord_o_auth->user_id)->where('enabled', 1);
            if ($processor->exists()) {
                $processor = $processor->first();
                $processor_type = $processor->type; //1 stripe
                $processor_id = $processor->processor_id;
            } else {
                $processor = null;
            }

            return view('store.product-page')->with('discord_store', $discord_store)->with('guild', $guild)->with('role', $role)->with('role_id', $role_id)->with('product_role', $product_role)->with('product_prices', $product_prices)->with('affiliate_id', $affiliate_id)->with('processor', $processor);//->with('store_processor_selected_id', $store_processor_selected_id);
            
        }else{
            //return view('discord_login');
            return redirect('/welcome'.'/'.$store_settings->url_slug);
        }
    }



}