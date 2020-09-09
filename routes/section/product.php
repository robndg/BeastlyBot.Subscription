<?php

use App\Shop;
use App\DiscordStore;
use Illuminate\Support\Facades\Route;

Route::get('/slide-product-purchase/{guild_id}/{role_id}', function($guild_id, $role_id) {
    if(\request('affiliate_id') !== null) {
        return view('slide.slide-product-purchase')->with('guild_id', $guild_id)->with('role_id', $role_id)->with('prices', self::getPricesForRole($guild_id, $role_id))->with('affiliate_id', \request('affiliate_id'));
    }
    return view('slide.slide-product-purchase')->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special_id', null)->with('prices', self::getPricesForRole($guild_id, $role_id));

});

Route::get('/slide-special-purchase/{guild_id}/{role_id}/{special_id}/{discord_id}', function($guild_id, $role_id, $special_id, $discord_id) {
    return view('slide.slide-product-purchase')->with('guild_id', $guild_id)->with('role_id', $role_id)->with('special_id', $special_id)->with('prices', self::getPricesForSpecial($guild_id, $role_id, $discord_id));
});

Route::get('/product/{id}', function () {
    return view('subscribe-product');
});
//Route::group(['domain' => 'shop.'.env('APP_URL')], function () {
//Route::group(['domain' => 'beastly.store'], function () {
    Route::get('/shop/{guild_id}', function ($guild_id) {
        if(!DiscordStore::where('url', $guild_id)->exists()) {
            return abort(404);
        }

        $shop = DiscordStore::where('url', $guild_id)->get()[0];
        /* --V1
        return view('subscribe')->with('guild_id', $shop->id)->with('descriptions', \App\RoleDesc::where('guild_id', $guild_id)->get());
        */
        return view('subscribe')->with('guild_id', $shop->guild_id)->with('descriptions', 'asd');
    });
//});

Route::get('/shop/{guild_id}/{affiliate_id}', function ($guild_id, $affiliate_id) {
    if (\App\Affiliate::where('id', $affiliate_id)->exists()) {
        return view('subscribe')->with('guild_id', $guild_id)->with('descriptions', \App\RoleDesc::where('guild_id', $guild_id)->get())
            ->with('affiliate', \App\Affiliate::where('id', $affiliate_id)->get()[0]);
    } else {
        return view('subscribe')->with('guild_id', $guild_id)->with('descriptions', \App\RoleDesc::where('guild_id', $guild_id)->get());
    }
});
Route::post('/get-special-roles', 'ServerController@getSpecialRoles');

Route::post('/process-checkout', 'OrderController@process');

Route::post('/process-special-checkout', 'OrderController@specialProcess');

Route::post('/check-prices', 'ProductController@checkProductPrices');

Route::post('/product', 'ProductController@product');

Route::post('/plan', 'ProductController@plan');