@extends('layouts.dash')

@section('title', 'Dashboard Guild Product')

@section('styles')
<style>
    #note-icon .btn-product-role-name{
        display:none;
        visibility:0;
    }
</style>
@endsection
@section('content')
<div class="container-fluid"><!-- container div -->

    <div class="desktop-header"> <!-- header div -->
        <div class="card card-block topnav-left">
            <div class="card-body write-card">
                <div class="d-flex align-items-center justify-content-between">
                    <h4>{{ $guild->name }}: New Product</h4>
                    <a href="/dashboard/{{ $guild_id }}" class="btn btn-outline-primary svg-icon">
                        <svg  width="20" class="svg-icon" id="new-note-back" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>
            </div>
        </div>
        @include('partials.dash.topnav-right')
    </div>

    <!-- start page content -->
    <div class="row">
            <div class="col-lg-12">                
                <div class="card card-block card-stretch pb-2">
                    <div class="card-body write-card pb-4">
                        <div class="row">
                            <div class="col-md-8">
                                
                                    
                                    <div class="form-group">
                                        <label class="label-control">Role</label>
                                        <div id="icon-button">
                                        @foreach($roles as $role) 
                                            @if($role->name !== '@everyone' && !$role->managed)
                                            <button class="btn btn-outline-primary ml-1 btn-product-role @if(isset($product_role)) {{ $product_role->role_id == $role->id ? 'active' : '' }} @endif" type="button" data-change="product-role" data-product-role-id="{{ $role->id }}" data-product-role-name="{{ $role->name }}" data-product-role-color="{{ dechex($role->color) }}" data-custom-target="#product-role-id" style="border-color: #{{ dechex($role->color) }}; color: #{{ dechex($role->color) }}">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                </svg>
                                                <!-- <i class="icon-discord mr-2" aria-hidden="true"></i> -->
                                                <span class="text-white btn-product-role-name">{{ $role->name }}</span>
                                            </button>
                                            @endif
                                        @endforeach
                                        <input type="hidden" id="product-role-id" value="{{ $product_role->id ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Title</label>
                                        <input type="text" class="form-control" name="title" id="product-title" placeholder="@if(isset($product_role)){{ $product_role->title }}@else{{'Example Role'}}@endif" value="@if(isset($product_role)){{ $product_role->title }}@endif" data-change="input" data-custom-target="#note-title">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Description</label>
                                        <textarea type="text" class="form-control" id="input-description" name="description" rows="2" data-change="input" data-custom-target="#note-description" placeholder="@if(isset($product_role)){{ $product_role->description }}@else{{ 'Example description... this role gives you instant access to incredible chat rooms' }}@endif">@if(isset($product_role)){!! $product_role->description ?? '' !!}@endif</textarea>{{-- {!! nl2br(e($product_role->description), false) ?? '' !!} --}}
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="label-control d-block">Max Subscribers</label>
                                        <div class="btn-group btn-group-toggle"> 
                                            <button type="button" class="button btn button-icon btn-primary btn-max-off {{ $product_role ?? 'active' }} @if(isset($product_role)) @if($product_role->max_sales == NULL) {{'active'}} @endif @endif" onclick="turnOffMax()">Off</button>
                                            <button type="button" class="button btn button-icon btn-primary btn-max-on @if(isset($product_role)) @if($product_role->max_sales >= 0)active @endif @endif" onclick="turnOnMax()">On</button>
                                        </div>
                                        <div class="btn-group btn-group-toggle" id="max-toggler" @if(isset($product_role)) @if($product_role->max_sales == NULL) style="display:none" @endif @else style="display:none" @endif> 
                                            <button type="button" class="button btn button-icon btn-info input-number-decrement">-</button>
                                            <input class="form-control btn button bg-primary input-number" type="text" value="@if(isset($product_role)) {{ $product_role->max_sales ?? '' }}@endif" min="0" max="100000" data-change="input" data-custom-target="#max-members" id="max_sales">
                                            <button type="button" class="button btn button-icon btn-info input-number-increment">+</button>
                                        </div>
                                    </div>

                                   
                                    <div class="form-row d-flex align-items-center justify-content-between">
                                        <div class="form-group col">
                                            <label class="label-control">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="reminder_date" value="@if(isset($product_role)){{ (explode(' ',trim($product_role->start_date))[0]) ?? date('Y-m-d') }}@else{{ date('Y-m-d') }}@endif" data-change="input" data-custom-target="#note-reminder-date"> 
                                        </div>
                                        <div class="form-group col">
                                            <label class="label-control">Start Time</label>
                                            <input type="time" class="form-control" id="start_time" name="reminder_time" value="@if(isset($product_role)){{ (explode(' ',trim($product_role->start_date))[1]) ?? date('H:i:s')}}@else{{ date('H:i:s') }}@endif" data-change="input" data-custom-target="#note-reminder-time">
                                        </div>
                                        <div class="form-group col d-none">
                                            <label class="label-control">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" value="" data-change="input" data-custom-target="#note-reminder-date">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Availability</label>
                                        <div>
                                            <select name="priority" id="input-access" class="form-control" data-change="select" data-custom-target="color">
                                                <!--<option value="danger">Archived</option>-->
                                                <option value="1" data-color-value="success" @if(isset($product_role)) {{ $product_role->access == 1 ? 'selected' : '' }} @endif>Guild Access</option>
                                                <option value="2" data-color-value="info"  @if(isset($product_role)) {{ $product_role->access == 2 ? 'selected' : '' }} @else selected @endif>Everyone</option>
                                                <option value="3" data-color-value="purple" @if(isset($product_role)) {{ $product_role->access == 3 ? 'selected' : '' }} @endif>Members Only</option>
                                                <!--<option value="warning">Specific Member</option>-->
                                                <option value="0" data-color-value="primary" @if(isset($product_role)) {{ $product_role->access == 0 ? 'selected' : '' }} @endif>Archived Product</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                 
                                    
                                    <button type="reset" class="btn btn-outline-primary" data-reset="note-reset">
                                        <svg width="20" class="svg-icon" id="new-note-reset" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                        </svg>
                                        Reset
                                    </button>
                                    <button type="button" class="btn btn-primary ml-1" onclick="saveGuildProductRole()">
                                        <svg width="20" class="svg-icon" id="new-note-save" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Save
                                    </button>
                                    
                             
                            </div>

                            <!-- example card -->
                            <div class="col-md-4" id="default">
                                <div class="card card-block card-stretch card-height card-bottom-border-info beast-detail blur-shadow" id="update-note">
                                    <div class="card-header d-flex justify-content-between pb-1">
                                        <div class="icon iq-icon-box-2 icon-border-info rounded" id="note-icon">
                                            <svg width="23" class="svg-icon" id="iq-main-01" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="card-header-toolbar d-flex align-items-center">
                                            <div class="dropdown">
                                                <span class="dropdown-toggle dropdown-bg" id="dropdownMenuButton4"
                                                    data-toggle="dropdown" aria-expanded="false" role="button">
                                                    <i class="ri-more-fill"></i>
                                                </span>
                                                <div class="dropdown-menu dropdown-menu-right"
                                                    aria-labelledby="dropdownMenuButton4" style="">
                                                    <a href="#" class="dropdown-item new-note1" data-toggle="modal" data-custom-target="#new-note1"><i class="ri-eye-line mr-3"></i>View</a>
                                                    <a class="dropdown-item" href="#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body rounded">
                                        <h4 class="card-title text-ellipsis short-1" id="note-title">Example Role</h4>
                                        <p class="mb-3 text-ellipsis short-6" id="note-description">Create a product first then set prices</p>

                                       
                                        @if(isset($product_role) && auth()->user()->payment_processor == 0)
                                        <div class="alert  bg-success" role="alert">
                                            <div class="iq-alert-icon">
                                                <i class="ri-alert-line"></i>
                                            </div>
                                            <div class="iq-alert-text"><b>Add payout</b> with your <a href="{{ \App\StripeHelper::getConnectURL() }}">Stripe Account</a></div>
                                        </div>
                                        @endif
                                        <div class="form-group">
                                        <label class="label-control">Subscription Price</label>
                                    

                                      {{--  @foreach (["Day", "Week", "Month", "Year"] as $price_interval)
                                            @if($product_role->prices()->where('status', 1)->where('interval', $price_interval)->exists())
                                                @foreach($product_role->prices()->where('status', 1)->where('interval', $price_interval)->get() as $price)
                                                    
                                                    
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-day">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="{{$price->interval}}" data-change="input-money" data-custom-target="#input-money-1-{{$price->interval}}" data-price-target="#enable-money" data-price-interval="{{$price->interval}}" data-price-interval-str="{{$price_interval}}" value="{{$price->price}}" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">{{$price_interval}}</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-{{ strtolower($price_interval) }}" @if($price_interval != "Month") style="display:none" @endif>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control input-money" id="{{ strtolower($price_interval) }}" data-change="input-money" data-custom-target="#input-money-1-{{ strtolower($price_interval) }}" data-price-target="#enable-money" data-price-interval="{{ strtolower($price_interval) }}" data-price-interval-str="{{ $price_interval }}" value="" min="1" max="100000" aria-label="(leave 0 to disable)" @if(!isset($product_role)){{'disabled'}}@endif>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge badge-primary">{{ $price_interval }}</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach--}}
                                            <div>
                                                <div class="form-row">
                                                    <div class="form-group col-sm-12 col-lg-5">
                                                        <div>
                                                            <select name="select-interval" id="" class="form-control" data-change="select-change-interval" data-custom-target="select-interval" @if(!isset($product_role)){{'disabled'}}@endif>
                                                                <!--<option value="danger">Archived</option>-->
                                                                <option value="day">Daily</option>
                                                                <option value="week">Weekly</option>
                                                                <option value="month" selected>Monthly</option>
                                                                <!--<option value="warning">Specific Member</option>-->
                                                                <option value="year">Yearly</option>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-day" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="day" data-change="input-money" data-custom-target="#input-money-1-day" data-price-target="#enable-money" data-price-interval="day" data-price-interval-str="Day" @if($product_role->prices()->where('status', 1)->where('interval', 'day')->exists()) value="{{ number_format(($product_role->prices()->where('status', 1)->where('interval', 'day')->first()->price/100),2) }}" @else value="" @endif min="1" max="100000" aria-label="(leave 0 to disable)" @if(!isset($product_role)){{'disabled'}}@endif>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">Day</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-week" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="week" data-change="input-money" data-custom-target="#input-money-1-week" data-price-target="#enable-money" data-price-interval="week" data-price-interval-str="Week" @if($product_role->prices()->where('status', 1)->where('interval', 'week')->exists()) value="{{ number_format(($product_role->prices()->where('status', 1)->where('interval', 'week')->first()->price/100),2) }}" @else value="" @endif min="1" max="100000" aria-label="(leave 0 to disable)" @if(!isset($product_role)){{'disabled'}}@endif>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">Week</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-month">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="month" data-change="input-money" data-custom-target="#input-money-1-month" data-price-target="#enable-money" data-price-interval="month" data-price-interval-str="Month" @if($product_role->prices()->where('status', 1)->where('interval', 'month')->exists()) value="{{ number_format(($product_role->prices()->where('status', 1)->where('interval', 'month')->first()->price/100),2) }}" @else value="" @endif min="1" max="100000" aria-label="(leave 0 to disable)" @if(!isset($product_role)){{'disabled'}}@endif>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">Month</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="input-money-1-year" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="year" data-change="input-money" data-custom-target="#input-money-1-year" data-price-target="#enable-money" data-price-interval="year" data-price-interval-str="Year" @if($product_role->prices()->where('status', 1)->where('interval', 'year')->exists()) value="{{ number_format(($product_role->prices()->where('status', 1)->where('interval', 'year')->first()->price/100),2) }}" @else value="" @endif min="1" max="100000" aria-label="(leave 0 to disable)" @if(!isset($product_role)){{'disabled'}}@endif>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">Year</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <div class="d-flex align-items-center justify-content-between mb-3 group-button-prices">
                                                       
                                                            {{-- @foreach($product_role->prices()->get() as $price) 
                                                                <button type="button" class="button btn btn-sm badge badge-info button-prices button-price-{{ $price->interval }}" data-button-price-interval="{{ $price->interval }}" data-button-price-price="{{ number_format(($price->price/100),2) }}">{{ $price->interval }}</button>
                                                            @endforeach --}}
                                                            @foreach (["Day", "Week", "Month", "Year"] as $price_interval) <!-- TODO Rob: make this cleaner and work -->
                                                                @if($product_role->prices()->where('interval', $price_interval)->where('status', '>=', 1)->exists())
                                                                <button type="button" class="button btn btn-sm badge @if($product_role->prices()->where('interval', $price_interval)->first()->status == 2) {{ 'badge-primary' }} @else {{'badge-info'}} @endif button-prices button-price-{{ $price_interval }}" data-button-price-interval="{{ $price_interval }}">{{ $price_interval }}</button>
                                                                @else
                                                                <button type="button" class="button btn btn-sm badge badge-info button-prices button-price-{{ $price_interval }} d-none" data-button-price-interval="{{ $price_interval }}">{{ $price_interval }}</button>

                                                                @endif
                                                            @endforeach
                                                            <!--<span class="badge badge-info">1 Day</span><span class="badge badge-info">1 Week</span><span class="badge badge-info">1 Month</span><span class="badge badge-info">1 Year</span>-->
                                                        </div>
                                                        <div class="input-group mb-3">

                                                            <button type="submit" class="btn btn-primary btn-block" @if(!isset($product_role)){{'disabled'}}@else onclick="updatePrices('{{ $product_role->id }}')" @endif>
                                                                Save Prices
                                                            </button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                         
                                      


                                    </div>
                                    
                                    <div class="card-footer">
                                      
                                        <div class="d-flex align-items-center justify-content-between note-text note-text-info"> 
                                            <a href="#" class=""><i class="las la-user-friends mr-2 font-size-20"></i>Everyone</a>
                                            <a href="#" class=""><i class="las la-calendar mr-2 font-size-20"></i><span id="note-reminder-date">Today</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
        <!--  -->
        <div class="default-note d-none">
            <div class="card card-block card-stretch card-height card-bottom-border-info note-detail" id="update-note">
                <div class="card-header d-flex justify-content-between pb-1">
                    <div class="icon iq-icon-box-2 icon-border-info rounded" id="note-icon">
                        <svg width="23" class="svg-icon" id="iq-main-01" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="card-header-toolbar d-flex align-items-center">
                        <div class="dropdown">
                            <span class="dropdown-toggle dropdown-bg" id="dropdownMenuButton4"
                                data-toggle="dropdown" aria-expanded="false" role="button">
                                <i class="ri-more-fill"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenuButton4" style="">
                                <a href="#" class="dropdown-item new-note1" data-toggle="modal" data-custom-target="#new-note1"><i class="ri-eye-line mr-3"></i>View</a>
                                <a class="dropdown-item" href="#"><i class="las la-trash-alt mr-3"></i>Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body rounded">
                    <h4 class="card-title text-ellipsis short-1" id="note-title">Example Role</h4>
                    <p class="mb-3 text-ellipsis short-6" id="note-description">Create a product first then set prices</p>
                </div>
                <div class="card-footer">
                    <div class="d-flex align-items-center justify-content-between note-text note-text-info"> 
                        <a href="#" class=""><i class="las la-user-friends mr-2 font-size-20" id="max-members"></i>Only Me</a>
                        <a href="#" class=""><i class="las la-calendar mr-2 font-size-20"></i><span id="note-reminder-date">01 Jan 2021</span></a>
                    </div>
                </div>
            </div>
        </div>
        <!--  -->

    </div> <!-- end row -->
        



    <!-- end page content -->

</div><!-- end container div -->

@endsection('content')

@section('scripts')


<script>

$(document).on('change', '[data-change="input-money"]', function (e) {

    const value = $(this).val()
    if($(this).attr('data-price-target') == '#enable-money') {
        const price_interval = $(this).attr('data-price-interval')
        const price = $(this).val();
        if(price >= 1){
            $(`.button-price-${price_interval}`).removeClass('d-none');
        }else{
            $(`.button-price-${price_interval}`).addClass('d-none');
        }
        
        /*const price_interval_str = $(this).attr('data-price-interval-str')
        
        //$('.select-interval-blocks').hide();
        console.log(value);
        add_button = `<button type="button" class="button btn btn-sm badge badge-info button-prices button-price-${price_interval}" data-button-price-interval="${price_interval}" data-button-price-price="${price}">${price_interval_str}</button>`
       $('.group-button-prices').append(add_button)*/
    }
})

$(document).on('click', '[data-change="product-role"]', function (e) {
    $('.btn-product-role').removeClass('active');
    const value = $(this).val()
    if($(this).attr('data-custom-target') == '#product-role-id') {
        const roleid = $(this).attr('data-product-role-id')
        $('#product-role-id').val(roleid)
        const rolename = $(this).attr('data-product-role-name')
        $('#product-title').val(rolename)
        $('#note-title').val(rolename)
        $(this).addClass('active');
        console.log(roleid)
    }
})
$(document).on('change', '[data-change="select-change-interval"]', function (e) {
    const value = $(this).val()
    if($(this).attr('data-custom-target') == 'select-interval') {
        $('.select-interval-blocks').hide();
        console.log(value);
        $(`#input-money-1-${value}`).show();
    }
})

$(document).on('change', '[data-change="radio"]', function (e) {
    const value = $(this).val()
    const color = value.data('product-role-color');
    if($(this).attr('data-custom-target') == 'color') {
        $('#note-icon').attr('class',' ')
        $('#update-note').attr('class', ' ')

        $('#note-icon').addClass(`icon iq-icon-box-2 icon-border-${value} rounded`)/*.css(`border-color: #${color}`)*/

        $('#update-note').addClass(`card card-block card-stretch card-height card-bottom-border-${value} note-detail`)
    }
})

$(document).on('change', '[data-change="select"]', function (e) {
    const value = $(this).val()
    console.log('ts')
    const color = value.attr("data-color-value");
    console.log(color)
    if($(this).attr('data-custom-target') == 'color') {
        
        $('#note-icon').attr('class',' ')
        $('#update-note').attr('class', ' ')
        $('#note-icon').addClass(`icon iq-icon-box-2 icon-border-${color} rounded`)
        $('#update-note').addClass(`card card-block card-stretch card-height card-bottom-border-${color} note-detail`)
    }
})
 
/* $(document).on('change', '[data-change="select"]', function (e) {
     const value = $(this).val()
     console.log('ts')
     if($(this).attr('data-custom-target') == 'color') {
         $('#note-icon').attr('class',' ')
         $('#update-note').attr('class', ' ')
         $('#note-icon').addClass(`icon iq-icon-box-2 icon-border-${value} rounded`)
         $('#update-note').addClass(`card card-block card-stretch card-height card-bottom-border-${value} note-detail`)
     }
})*/

console.log(new Date($('#start_date').val() + "T" + $('#start_time').val()));

var product_uuid = '{{ $product_role->id ?? 0 }}'

function saveGuildProductRole(product_uuid) {

    $.ajax({
        url: '/bknd00/saveGuildProductRole',
        type: 'POST',
        data: {
            'id': '{{ $product_role->id ?? 0 }}',
            'discord_store_id': '{{ $shop->UUID }}',
            'role_id': $('#product-role-id').val(),//$product_id,
            'title': $('#product-title').val(),
            'description': $('#input-description').val(),
            'access': $('#input-access').val(),
            'start_date': $('#start_date').val(),//new Date($('#start_date').val() + "T" + $('#start_time').val()).toLocaleDateString(),//$('#start_date').val(),
            'start_time': $('#start_time').val(),
            'end_date': $('#end_date').val(),
            'max_sales': $('#max_sales').val(),
            _token: '{{ csrf_token() }}'
        },
    }).done(function (msg) {
        console.log(msg);
        if(!msg['success']){
            Swal.fire({
                title: 'Product not Saved',
                text: msg['message'],
                type: 'info',
                showCancelButton: false,
                showConfirmButton: true,
            });
        }else{
            if(product_uuid == 0){
            product_uuid = msg['product_uuid'];
                var url = document.location.href+"?uuid=" + product_uuid;
                document.location = url;
            }
            Swal.fire({
                title: 'Product Saved!',
               // text: "Awesome... add some prices",
                type: 'success',
                showCancelButton: false,
                showConfirmButton: true,
            });
           

           

        
           
            //window.location.href = '/dashboard/' + msg['store'].guild_id
        }
    })
}
</script>

<script>


var product_uuid = '{{ $product_role->id ?? 0 }}'

    // TODO: For now we close the slide but we need to turn off the switcheries
    function updatePrices(product_role_id) {
        Toast.fire({
            title: 'Processing....',
            text: '',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: () => !Toast.isLoading(),
            //target: document.getElementById('slider-div')
        });
        Toast.showLoading();
        @if(isset($product_role))
        $.ajax({
            url: '/bknd00/saveGuildProductRolePrices',
            type: 'POST',
            data: {
                'price_interval_day': $('#day').val(),
                'price_interval_week': $('#week').val(),
                'price_interval_month': $('#month').val(),
                'price_interval_year': $('#year').val(),
                'product_id': product_role_id,
                //'role_id': role_id,
                //'role_name': Global.role_name,
                //'guild_id': guild_id,
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            if (msg['success']) {
                Toast.fire({
                    title: 'Success!',
                    text: msg['msg'],
                    type: 'success',
                    showCancelButton: false,
                    //target: document.getElementById('slider-div')
                });
            } else {
                Swal.fire({
                    title: 'Oops!',
                    text: msg['msg'],
                    type: 'warning',
                    showCancelButton: false,
                    target: document.getElementById('slider-div')
                });
            }
        });
        @endif
    }

</script>

<script>
(function() {
 
 window.inputNumber = function(el) {

   var min = el.attr('min') || false;
   var max = el.attr('max') || false;

   var els = {};

   els.dec = el.prev();
   els.inc = el.next();

   el.each(function() {
     init($(this));
     //changeMax($(this).val())
   });

   function init(el) {

     els.dec.on('click', decrement);
     els.inc.on('click', increment);

     function decrement() {
       var value = el[0].value;
       value--;
       if(!min || value >= min) {
         el[0].value = value;
         if(value == 0){

             turnOffMax()
         }
       }
     }

     function increment() {
       var value = el[0].value;
       value++;
       if(!max || value <= max) {
         el[0].value = value++;
       }
     }
   }
 }
})();

inputNumber($('.input-number'));

function turnOffMax(){
    $('.input-number').val("");
    $('#max-toggler').hide();
    $('.btn-max-on').removeClass('active');
    $('.btn-max-off').addClass('active');
}
function turnOnMax(){
    $('.input-number').val(1);
    $('#max-toggler').show();
    $('.btn-max-off').removeClass('active');
    $('.btn-max-on').addClass('active');
}

function changeMax(number) {
    const numberMax = number;
    if(numberMax == 0){
        return `Everyone`;
    }else{
        return `${numberMax} Members`
    }
    console.log(numberMax);
}
</script>


@endsection