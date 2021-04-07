<?php

use App\DiscordStore;
use App\ProductPlanController;
use App\Shop;
use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\StripeHelper;

/*Route::post('/refresh-roles/{guild_id}', function($guild_id) {
    Cache::forget('roles_' . $guild_id);
    return redirect('/server/' . $guild_id);
});*/

Route::get('/dashboard', 'DashController@getDash'); // * Page: Dash Guilds + More //

Route::get('/dashboard/{guildid}', 'DashController@getDashGuild'); // * Page: Dash Guild //

Route::get('/dashboard/{guildid}/product', 'DashController@getDashGuildProduct'); // * Page: Dash Guild Product Role //

Route::post('/bknd00/saveGuildProductRole', 'DashController@saveGuildProductRole'); // * Save: Dash Guild Product Role //

Route::post('/bknd00/returnNewStore', 'DashController@returnNewStore'); // * Redirect: Latest Guild Added Page //

Route::post('/bknd00/returnCheckPremium', 'DashController@returnCheckPremium');

Route::post('/bknd00/saveGuildProductRolePrices', 'ProductPlanController@create'); // * Save: Dash Guild Product Role Prices //

Route::get('/dashboard/{guildid}/settings', 'DashController@getDashGuildStoreSettings'); // * Page: Dash Guild Store Settings //

Route::post('/bknd00/saveGuildSettings', 'StoreSettingsController@saveGuildSettings'); // * Save: Dash Guild Save Settings //

Route::get('/dashboard/{guildid}/settings/bot', 'DashController@getDashGuildStoreSettingsBot'); // * Page: Dash Guild Store Settings Bot //

//Route::get('/server/{id}', 'ServerController@getServerPage');



Route::get('/connect_stripe', 'StripeConnectController@connect');