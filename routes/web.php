<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::stripeWebhooks('stripe_webhooks/connect');
//Route::stripeWebhooks('stripe_webhooks/{configKey}');

Route::get('/impersonate/{id}', 'UserController@impersonate');
//Route::domain('beastly.app')->group(function ($router) {
    require_once __DIR__ . "/section/site.php"; // * main //
//});

require_once __DIR__ . "/section/store.php"; // * main // // product/front page requires auth

require_once __DIR__ . "/section/auth.php";

/**
 * All routes that require authentication
 */


Route::group(['middleware' => ['auth', 'web']], function () {

    require_once __DIR__ . "/section/dash.php"; // * main //

    require_once __DIR__ . "/section/paypal.php";
    require_once __DIR__ . "/section/admin.php";
});


/* test */
