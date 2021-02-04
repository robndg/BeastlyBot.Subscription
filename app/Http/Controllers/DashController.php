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
use App\Products\DiscordRoleProduct;
use App\Refund;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Exception\InvalidRequestException;
use App\DiscordHelper;
use App\Subscription;
use App\PaidOutInvoice;
use App\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\StripeHelper;
use Illuminate\Support\Str;
use App\ProductPlanController;

class DashController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

     public function getDash(){
        if(Auth::check()){
            $stripe_helper = auth()->user()->getStripeHelper();
            $discord_helper = new DiscordHelper(auth()->user());

            return view('dash.dashboard')->with('stripe_helper', $stripe_helper)->with('discord_helper', $discord_helper);

        }else{
            return view('discord_login');
        }
    }

    public function getDashGuild($guild_id){
        if(Auth::check()){
            $stripe_helper = auth()->user()->getStripeHelper();
            $discord_helper = new DiscordHelper(auth()->user());
    
            $owner = true; $admin = true;

            /*if(! $discord_helper->ownsGuild($guild_id)) {
                AlertHelper::alertError('You are not the owner of that server!');
                return redirect('/dashboard');
             }*/

            if($owner || $admin) {

                //$guild_id = \request('guild');
         
             
                 $discord_store = null;
         
                 if(! DiscordStore::where('guild_id', $guild_id)->exists()) {
                     $discord_store = new DiscordStore(['guild_id' => $guild_id, 'url' => $guild_id, 'user_id' => auth()->user()->id, 'UUID' => Str::uuid()]);
                     $discord_store->save();
         
                     $stats = new Stat(['type' => 1, 'type_id' => $discord_store->id]);
                     $stats->data = ['subscribers' => ['active' => 0, 'total' => 0], 'disputes' => ['active' => 0, 'total' => 0]];
                     $stats->save();
                     
                 } else {
                     $discord_store = DiscordStore::where('guild_id', $guild_id)->first();
                     //$discord_store->live = 1;
                     //$discord_store->save();
         
                     $stats = Stat::where('type', 1)->where('type_id', $discord_store->id)->first();
                 }

                 $product_roles = ProductRole::where('discord_store_id', $discord_store->id)->get();
                 
                 /*$guild_products = [];

                 foreach($product_roles as $product_role) { 
                    $product_prices = Price::where('product_id', $product_role->id)->get();

                 }*/

                 
         
                /* $roles = $discord_helper->getRoles($guild_id);
         
                 $active = array();
                 $product_roles = [];
         
                 $subscribers = [];
         
                 foreach($roles as $role) { 
                     $discord_product = new DiscordRoleProduct($guild_id, $role->id, null, null);
                     $product = $discord_product->getProduct(); // TODO: check if product active in Stripe/PayPal and change product DB
                     if($product != null) {
                         if($product->active == 1){
                             array_push($active, $role->id);
                         }
                         $product_roles[$role->id] = $product;
                         $subscribers[$role->id] = 0;
                         //$subscribers[$role->id] = Subscription::where('store_id', $discord_store->id)->where('status', '<=', 3)->where('status', '<=', 2)->where('metadata', 'LIKE', '%' . $role->id . '%')->count();
                     } else {
                         $subscribers[$role->id] = 0;
                     }
                 }*/
         
                 // TODO: Fix this for the subscribers tab. 
                 // Also need to fix the servers list subscribers count because if I subscribed to premium and new role it says the subscribers, really 1 subscriber and 2 susbcriptions for that 1 subscriber
                 //$users_roles = $this::getUsersRoles($discord_store->id);
         
                 return view('dash.dash-guild')->with('discord_helper', $discord_helper)->with('guild_id', $guild_id)->with('guild', $discord_helper->getGuild($guild_id))->with('shop', $discord_store)->with('product_roles', $product_roles)->with('bot_positioned', $discord_helper->isBotPositioned($guild_id));


               // return view('dash.dash-guild')->with('guilds', $discord_helper->getOwnedGuilds())->with('stripe_helper', $stripe_helper)->with('discord_helper', $discord_helper);
            } else {
                return view('dash.dashboard')->with('stripe_helper', $stripe_helper)->with('discord_helper', $discord_helper);
            }
        }else{
            return view('discord_login');
        }
    }

    public function getDashGuildProduct($guild_id){
        $product_uuid = \request('uuid');

        $discord_helper = new DiscordHelper(auth()->user());

        $discord_store = DiscordStore::where('guild_id', $guild_id)->first();

        if($product_uuid != false){
           
            // code for editing
        
        }else{

            $roles = $discord_helper->getRoles($guild_id);

            return view('dash.dash-guild-product')->with('discord_helper', $discord_helper)->with('guild_id', $guild_id)->with('guild', $discord_helper->getGuild($guild_id))->with('shop', $discord_store)->with('roles', $roles);
            
        }
    }


    public function saveDashGuildProduct(Request $request){

        $product_uuid = \request('uuid');

        if($product_uuid != false){
           
            // code for editing
        
        }else{

            $roles = $discord_helper->getRoles($guild_id);

            return view('dash.dash-guild-product')->with('discord_helper', $discord_helper)->with('guild_id', $guild_id)->with('guild', $discord_helper->getGuild($guild_id))->with('shop', $discord_store)->with('roles', $roles);
            
        }

    }

}