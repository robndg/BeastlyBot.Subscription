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
use App\Exception;

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

           

            if($owner || $admin) {

                //$guild_id = \request('guild');
         
             
                 $discord_store = null;
         
                 if(! DiscordStore::where('guild_id', $guild_id)->exists()) {
                   
                    AlertHelper::alertError('Please retry adding the bot.');
                    return redirect('/dashboard');
                    
                     
                 } else {
                     $discord_store = DiscordStore::where('guild_id', $guild_id)->first();
                     //$discord_store->live = 1;
                     //$discord_store->save();
                    if(!Stat::where('type', 1)->where('type_id', $discord_store->id)->exists()){
                        $stats = new Stat(['type' => 1, 'type_id' => $discord_store->id]);
                        $stats->data = ['subscribers' => ['active' => 0, 'total' => 0], 'disputes' => ['active' => 0, 'total' => 0]];
                        $stats->save();
                    }else{
                        $stats = Stat::where('type', 1)->where('type_id', $discord_store->id)->first();
                    }
                     
                 }

                 if(! $discord_helper->ownsGuild($guild_id)) {
                    AlertHelper::alertError('You are not the owner of that server.');
                    return redirect('/dashboard');
                }

                 $product_roles = ProductRole::where('discord_store_id', $discord_store->UUID)->get();
                 
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
            $roles = $discord_helper->getRoles($guild_id);
            $product_role = ProductRole::where('id', $product_uuid)->first();
            if(!$product_role){
                AlertHelper::alertError('This product does not exist.');
                return redirect('/dashboard');
            }

            return view('dash.dash-guild-product')->with('discord_helper', $discord_helper)->with('guild_id', $guild_id)->with('guild', $discord_helper->getGuild($guild_id))->with('shop', $discord_store)->with('roles', $roles)->with('product_role', $product_role);
        
        }else{

            $roles = $discord_helper->getRoles($guild_id);

            return view('dash.dash-guild-product')->with('discord_helper', $discord_helper)->with('guild_id', $guild_id)->with('guild', $discord_helper->getGuild($guild_id))->with('shop', $discord_store)->with('roles', $roles);
            
        }
    }


    public function saveGuildProductRole(Request $request){

        $product_uuid = $request->id;
        
        Log::info($product_uuid);

        $discord_store_id = $request->discord_store_id;
        $role_id = $request->role_id;

        $description = $request->description;
        $title = $request->title;
        $access = $request->access;
        $start_date = $request->start_date;
        $start_time = $request->start_time;
        $end_date = $request->end_date;
        $max_sales = $request->max_sales;
        
        //Log::info($start_time);

       // $start_date_time = new DateTime().format($start_date . ' ' . $end_date);
        //$start_date_time = Carbon::createFromString($start_date . ' '. $end_date);
        //$start_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->format('Y-m-d H:i:s e+');
        $start_date_time = Carbon::createFromFormat('Y-m-d H:i:s', $start_date . ' '. $start_time);
       // $start_date_time_laravel = new DateTime($start_date_time);
        //Log::info($start_date_time_laravel);
        if($product_uuid == 0){
            
        //try{
        // create product   
            $product_role = new ProductRole([
                'id' => Str::uuid(),
                'discord_store_id' => $discord_store_id,
                'role_id' => $role_id,//$product_id,
                'title' => $title,
                'description' => $description,
                'access' => $access,
                'start_date' => $start_date_time,
                'end_date' => $end_date,
                'max_sales' => $max_sales,
                'active' => 0,
                ]);
                $product_role->save();

                $product_role = ProductRole::where('discord_store_id', $discord_store_id)->where('role_id', $role_id)->first(); // TODO dash: change to latest or get string uuid
                AlertHelper::alertSuccess('Great now add some prices!');
                return response()->json(['success' => true, 'product_uuid' => $product_role->id]);
           /* } catch (\Exception $e){
               $message = $e->getMessage();
                return response()->json(['success'=> false, 'message' => $message]);
                
                
            }*/
            // then allow prices with uuid

        
        }else{

           // try{
                $product_role = ProductRole::where('id', $product_uuid)->first();
                //$product_role->discord_store_id = $discord_store_id;
                $product_role->role_id = $role_id;
                $product_role->description = $description;
                $product_role->access = $access;
                $product_role->start_date = $start_date_time;
                $product_role->end_date = $end_date;
                $product_role->max_sales = $max_sales;
                // if prices then active 1
                //$product_role->active = 1;
                $product_role->save();

                return response()->json(['success' => true, 'product_uuid'=> $product_uuid]);

           /* } catch (\Exception $e){
                $message = $e->getMessage();
                return response()->json(['success'=> false, 'product_uuid'=> $product_role->id, 'message' => $message]);
            }*/
            
            

              
        }

    }

    public function returnNewStore(){

        $discord_helper = new DiscordHelper(auth()->user());
        $guild_user_id = $discord_helper->getID();
        if(DiscordStore::where('user_id', $guild_user_id, 'created_at', NULL)->exists()){
            $lastNewStore = DiscordStore::where('user_id', $guild_user_id, 'created_at', NULL)->first();
            AlertHelper::alertSuccess('Your store dashboard!');
            return response()->json(['success' => true, 'store' => $lastNewStore]);
        }else{
            return response()->json(['success' => false]);
        }
        

        
    }
    

}