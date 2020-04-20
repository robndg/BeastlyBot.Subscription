<?php

use Illuminate\Support\Facades\Route;

Route::get('/checkout-success', 'OrderController@checkoutSuccess');

Route::get('/checkout-cancel', 'OrderController@checkoutCancel');

Route::get('/slide-invoice/{id}', 'OrderController@getInvoiceSlide');

