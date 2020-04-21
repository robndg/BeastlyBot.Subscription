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

Route::get('/dashboard','UserController@getDashboard');
