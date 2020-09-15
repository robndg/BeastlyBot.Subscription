<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

/*Route::get('/help', function () { //
    return view('/help/home');
});*/

Route::get('/slide-ticket-create', 'TicketsController@create');
Route::post('/bknd000/new-ticket-post', 'TicketsController@store')->name('newTicketPost');

Route::get('/slide-tickets-list', 'TicketsController@userTickets');