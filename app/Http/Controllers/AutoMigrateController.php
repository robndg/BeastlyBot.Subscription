<?php

namespace App\Http\Controllers;

use App\AutoMigrate;
use Illuminate\Http\Request;

class AutoMigrateController extends Controller
{

    /* Master Plan */
    // For subscription in subscriptions {1) Cancel Subscription 2) Create new subscription}

    /* Steps */
    // For Setup: 1 got subscriptions, // 2 kicks bot, maybe cancel subs 
    // For DB: 3 products creating (3.5 error check array) // 4 prices creating (with assigned diff users if diff price) (4.5 error array) // 5 subscriptions table created (5.5 error check array) 
    // For Ending: 6 stripe subscriptions cancelled, create (6.5 error) // 7 confirming // 8 complete // 9 user agrees to terms // 10 done, redirect to dash or store


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AutoMigrate  $autoMigrate
     * @return \Illuminate\Http\Response
     */
    public function show(AutoMigrate $autoMigrate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AutoMigrate  $autoMigrate
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoMigrate $autoMigrate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AutoMigrate  $autoMigrate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AutoMigrate $autoMigrate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AutoMigrate  $autoMigrate
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoMigrate $autoMigrate)
    {
        //
    }
}
