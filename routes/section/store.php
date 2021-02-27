
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


// Store Page


/* Auths */

Route::group(['middleware' => ['auth', 'web']], function () {
    // Store Product Page
    Route::get('/shop/{shop_title}/{product_title}', 'StoreController@getStoreProduct'); // * page //
    // Store Product Page Setup Order
    Route::post('/bknd00/setup-order', 'StoreCustomerController@setupOrder'); // * post bknd00 //
});

