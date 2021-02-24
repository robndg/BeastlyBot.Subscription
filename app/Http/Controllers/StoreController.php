<?php

namespace App\Http\Controllers;

use Auth;
use App\AlertHelper;
use App\DiscordStore;
#use App\Shop;
use App\User;
use App\Product;
use App\ProductRole;
use App\Price;
//use App\Products\DiscordRoleProduct;
use App\DiscordHelper;
use App\Subscription;
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

    public function __construct() {
        $this->middleware('auth');
    }


    public function getStoreProduct($url, $product_role_slug){

        if(Auth::check()){

            $affiliate_id = null;
            if(\request('affiliate_id') !== null) {
                // TODO: add affiliate
            }
        
            $discord_store = null;
            if(! DiscordStore::where('url', $url)->exists()) {
                return abort(404);
            } else {
                $discord_store = DiscordStore::where('url', $url)->first();
                Log::info($discord_store->id);
            }
            $owner_array = \App\User::where('id', $discord_store->first()->user_id)->first();
            $discord_helper = new DiscordHelper(auth()->user());
            $guild_id = $discord_store->guild_id;

            Log::info($guild_id);

            Log::info($product_role_slug);
            $product_title_unslug = Str::title(str_replace('-', ' ', $product_role_slug));
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

            $processor = Processors::where('user_id', $owner_array->id)->where('enabled', 1)->first();
            $processor_type = $processor->type; //1 stripe
            $processor_id = $processor->processor_id;

            return view('store.product-page')->with('discord_store', $discord_store)->with('guild', $guild)->with('role', $role)->with('role_id', $role_id)->with('product_role', $product_role)->with('product_prices', $product_prices)->with('affiliate_id', $affiliate_id)->with('processor', $processor);//->with('store_processor_selected_id', $store_processor_selected_id);
            
        }else{
            return view('discord_login');
        }
    }


}