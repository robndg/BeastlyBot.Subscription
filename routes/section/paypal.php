<?php


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\PayPal\PayPalHelper;

Route::get('/paypal/authenticate', function() { return PayPalHelper::performAuthenticate(); });

Route::get('/paypal/checkout', function() { PayPalHelper::performCheckout(); });

