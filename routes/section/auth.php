<?php

use Illuminate\Support\Facades\Route;

Route::get('/discord_login', function () {
    // Illuminate\Support\Facades\Session::flush();
    return view('discord_login');
})->name('discord_login');

Route::get('/discord_oauth', 'DiscordOAuthController@connect');

Route::get('/logout', function () {
    // Illuminate\Support\Facades\Session::flush();
    auth()->logout();
    return redirect('/');
})->name('logout');

Route::get('/dashboard', function() {
    $stripe_helper = auth()->user()->getStripeHelper();

    // get all active subscriptions for user and put into cleaned up array
    $subscriptions = array();
    foreach ($stripe_helper->getSubscriptions() as $subscription) {
        $subscriptions[$subscription->id] = $subscription->toArray();
    }

    return view('dashboard')->with('subscriptions', $subscriptions)->with('balance', $stripe_helper->getBalance());
});
