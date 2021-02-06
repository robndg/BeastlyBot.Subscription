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

Route::get('/dashboard', 'DashController@getDash'); // * //

Route::get('/dashboard/{guildid}', 'DashController@getDashGuild'); // * //

Route::get('/dashboard/{guildid}/product', 'DashController@getDashGuildProduct'); // * //

Route::post('/bknd00/saveGuildProductRole', 'DashController@saveGuildProductRole'); // * //

Route::post('/bknd00/returnNewStore', 'DashController@returnNewStore'); // * //

//Route::get('/server/{id}', 'ServerController@getServerPage');