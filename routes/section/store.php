
<?php

use App\DiscordStore;
use App\ProductPlanController;
use App\Shop;
use App\User;
use App\StripeHelper;
use App\DiscordHelper;
use App\ProductRole;
use App\Price;
use App\StoreCustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Products\DiscordRoleProduct; 
use App\Products\Plans\DiscordPlan;

Route::group(['middleware' => ['auth', 'web']], function () {

});

// routes thru beastly.store/welcome/storename
Route::get('/welcome/{store_slug}', 'StoreController@getStoreWelcome'); // * page *//

/* Auths */

Route::group(['middleware' => ['auth', 'web']], function () {
    //Route::get('/checkout-subscription-success/{id}', 'StoreController@checkoutSuccess');
    
    // Store Page
    Route::get('/shop/{shop_title}', 'StoreController@getStoreFront'); // * page //
    // Store Product Page
    Route::get('/shop/{shop_title}/{product_title}', 'StoreController@getStoreProduct'); // * page //
    // Store Product Page Setup Order
    Route::post('/bknd00/setup-order', 'StoreCustomerController@setupOrder'); // * post bknd00 //
    // Store User Redirect Order
    Route::get('/checkout-subscription-success/{id}/{cus_id}/{prod_id}', 'StoreCustomerController@checkoutSubscriptionSuccessRole');
});

