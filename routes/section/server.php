<?php

use App\DiscordStore;
use App\SiteConfig;
use App\Shop;
use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/servers', function () {
    return view('servers');
});
Route::get('/slide-servers', function () {
    return view('slide.slide-servers');
});

Route::get('/server/{id}', 'ServerController@getServerPage');

Route::post('/get-active-roles', 'ServerController@getActiveRoles');

Route::post('/get-status-roles', 'ServerController@getStatusRoles');

Route::get('/get-latest-transactions', 'ServerController@getRecentTransactions');

Route::get('/get-transactions', 'ServerController@getTransactions');

Route::get('/get-disputes', 'ServerController@getDisputes');

Route::get('/slide-server-settings/{id}', function ($id) {
    return view('/slide/slide-server-settings')->with('shop', DiscordStore::where('guild_id', $id)->first());
});
Route::get('/slide-list-products/{id}', function ($id) {
    return view('/slide/slide-server-list-products')->with('id', $id)->with('shop', DiscordStore::where('guild_id', $id)->first());
});

Route::get('/slide-roles-settings/{guild_id}/{role_id}', 'ServerController@getSlideRoleSettings');

Route::get('/slide-special-roles-settings/{guild_id}/{role_id}/{type}/{discord_id}', 'ServerController@getSlideSpecialRoleSettings');

Route::get('/slide-roles-prices/{guild_id}/{role_id}', 'ServerController@getSlideRolePrices');

Route::get('/slide-server-member/{guild_id}/{user_id}', function ($guild_id, $user_id) {
    try {
        $user = User::where('discord_id', $user_id)->get()[0];
        $discord_id = $useruser()->DiscordOAuth->discord_id;
        $invoices_array = [];
        $subscriptions = array();

       /* if(\Illuminate\Support\Facades\Session::has('inv_' . $user_id . $guild_id)) {
            $invoices_array =  \Illuminate\Support\Facades\Session::get('inv_' . $user_id . $guild_id);
        } else {*/
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));

            foreach ($user->getSubscriptions($guild_id) as $subscription) {
                $invoices = \Stripe\Invoice::all(['subscription' => $subscription->id]);
                foreach ($invoices as $invoice) $invoices_array[$invoice->id] = $invoice;
            }

            $stripe_subs = \Stripe\Subscription::all(['customer' => $user->stripe_customer_id]);
            //Log::info($stripe_subs);
            //Log::info($user->stripe_customer_id);
            foreach ($stripe_subs as $sub) {
                $continue = false;
                if($sub->status != 'expired'){
                    try{
                        try{
                        $plan = \Stripe\Plan::retrieve($sub->items->data[0]->plan->id);
                        $continue = true;
                        }catch(Exception $e){
                            #Log::error($e);
                        }
                        if($continue == true){
                            $plan_guild_id = explode('_', $plan->product)[0];
                            $plan_role_id = explode('_', $plan->product)[1];
                            //Log::info($plan->id);
                            try{
                                $plan_special = explode('_', $plan->id)[4];
                                $plan_special_bool = true;
                            }catch(Exception $e) {
                                $plan_special = null;
                                $plan_special_bool = false;
                            }
                            //Log::info($plan_special_bool);
                            if ($plan_guild_id === $guild_id) {
                                $sub->all = $plan;
                                $sub->role_id = $plan_role_id;
                                //$subuser()->DiscordOAuth->discord_id = $discord_id;
                                $sub->plan_special = $plan_special;
                                $sub->plan_special_bool = $plan_special_bool;
                                array_push($subscriptions, $sub);
                            }
                        }
                    }catch(Exception $e){
                        Log::error($e);
                    }
                }
            }

            $other_plans = array();
            $other = array();
            $durations = array(1, 3, 6, 12);

            //$stripe_plans = \Stripe\Plan::all(['customer' => $user->stripe_customer_id]);
            $role_id = 671597529606914059;
            #Log::info("-1");
            foreach ($durations as $duration) {

                #Log::info("0");
                try {
                    #Log::info("1");
                    $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id);
                    Log::info($plan);
                    $plan_guild_id = explode('_', $plan->product)[0];
                    $plan_role_id = explode('_', $plan->product)[1];
                    //Log::info($plan->id);
                    try{
                        $plan_special = explode('_', $plan->id)[4];
                        $plan_special_bool = true;
                    }catch(Exception $e) {
                        $plan_special = null;
                        $plan_special_bool = false;
                    }
                    if ($plan_guild_id === $guild_id) {
                        #$other->all = $plan;
                        $other->role_id = $plan_role_id;
                        //$subuser()->DiscordOAuth->discord_id = $discord_id;
                        $other->plan_special = $plan_special;
                        $other->plan_special_bool = $plan_special_bool;
                        array_push($other_plans, $other);
                    }
                    #Log::info("2");
                } catch (\Exception $e) {
                    Log::error($e);
                    //$prices[$duration] = -1;
                    try {
                        $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_t_' . $discord_id);

                        $plan_guild_id = explode('_', $plan->product)[0];
                        $plan_role_id = explode('_', $plan->product)[1];
                        Log::info($plan->id);
                        Log::info("trial retrieve");
                        try{
                            $plan_special = explode('_', $plan->id)[4];
                            $plan_special_bool = true;
                        }catch(Exception $e) {
                            $plan_special = null;
                            $plan_special_bool = false;
                        }
                        if ($plan_guild_id === $guild_id) {
                            #$other->all = $plan;
                            $other->role_id = $plan_role_id;
                            //$subuser()->DiscordOAuth->discord_id = $discord_id;
                            $other->plan_special = $plan_special;
                            $other->plan_special_bool = $plan_special_bool;
                            array_push($other_plans, $other);
                        }
                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }
            }

            //\Illuminate\Support\Facades\Session::put('inv_' . $user_id . $guild_id, $invoices_array);
       /* }*/
        //Log::info($subscriptions);
        return view('/slide/slide-server-member')->with('guild_id', $guild_id)->with('user', $user)
            ->with('invoices', $invoices_array)->with('subscriptions', $subscriptions)->with('other_plans', $other_plans);
    }catch(Exception $e) {
        Log::error($e);
    }
});

Route::get('/slide-server-member-role-add/{guild_id}/{user_id}', function ($guild_id, $user_id) {
    $user = User::where('discord_id', $user_id)->get()[0];
    return view('/slide/slide-server-member-role-add')->with('guild_id', $guild_id)->with('user', $user);
});
Route::post('/server-member-role-add', 'ServerController@memberRoleAdd');
//Route::get('/slide-server-member-role-add/{guild_id}/{role_id}', 'ServerController@getSlideRoleSettings');
//Route::get('/slide-server-member-role-add/{guild_id}/{user_id}', 'ServerController@memberAddRole');

Route::post('/save-server-settings', 'ServerController@updateShop');

Route::post('/save-go-live', 'ServerController@updateStatus');
