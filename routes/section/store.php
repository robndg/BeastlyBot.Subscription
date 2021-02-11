
<?php

use App\DiscordStore;
use App\ProductPlanController;
use App\Shop;
use App\User;
use App\StripeHelper;
use App\DiscordHelper;
use App\ProductRole;
use App\Price;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Products\DiscordRoleProduct; 
use App\Products\Plans\DiscordPlan;


Route::get('/guild/123/product', 'StoreController@getStoreProduct'); // * //
