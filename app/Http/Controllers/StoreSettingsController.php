<?php

namespace App\Http\Controllers;

use App\StoreSettings;
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

class StoreSettingsController extends Controller
{

    public function saveGuildSettings(Request $request){

        $discord_store_uuid = $request->discord_store_id;
        $discord_store = DiscordStore::where('UUID', $discord_store_uuid)->first();
        // TODO: add check if store owner
        $store_settings = StoreSettings::where('store_type', 1)->where('store_id', $discord_store->id)->first();

        foreach ($request->all() as $key => $setting){
            if($key != '_token' && $key != 'discord_store_id' && $key != 'premium' && $key != 'store_id' && $key != 'store_type' && $setting != null){
                // Premium Store
                if(($store_settings->premium == 1 && $key == 'url_slug') || ($store_settings->premium == 1 && $key == 'show_beastly')){  
                // Premium Settings
                    // Premium URL Slug
                    if($key == 'url_slug'){
                        $string_discord_url = Str::title(str_replace(' ', '-', $store_settings->store_name));
                        // Unique or set check
                        if(($store_settings->url_slug != $string_discord_url && ($setting != $discord_store->guild_id || $setting != $string_discord_url)) && StoreSettings::where('url_slug', 'LIKE', '%' . $string_discord_url . '%')/*->where('store_id', '!=', $store_settings->store_id)*/->get()->count() > 0){
                            $counted_url =  StoreSettings::where('url_slug', 'LIKE', '%' . $string_discord_url . '%')/*->where('store_id', '!=', $store_settings->store_id)*/->count();
                            $count_string = '_' . $store_settings->store_id;//$counted_url; /**  Best to use primary key instead of count if other close server close name **/
                            $store_settings->url_slug = $string_discord_url . $count_string; // = Premium URL store name + _count int
                        }else{
                            if($setting == $string_discord_url){
                                $store_settings->url_slug = $string_discord_url; // = Premium URL Store_Mame
                            }else{
                                $store_settings->url_slug = $discord_store->guild_id; // = Premium URL guild_id
                            }
                        }
                    // Other Premium Settings
                    }else{
                        $store_settings->$key = $setting; // data-new premium
                    }
                }else{
                    // Regular Settings
                    $store_settings->$key = $setting; // data-new
                }
            }
        }
        $store_settings->save();

        return response()->json(['success' => true, 'msg' => 'Saved Store Settings.']);

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
    public function create()
    {
        //
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
     * @param  \App\StoreSettings  $storeSettings
     * @return \Illuminate\Http\Response
     */
    public function show(StoreSettings $storeSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StoreSettings  $storeSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(StoreSettings $storeSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StoreSettings  $storeSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreSettings $storeSettings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StoreSettings  $storeSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreSettings $storeSettings)
    {
        //
    }
}
