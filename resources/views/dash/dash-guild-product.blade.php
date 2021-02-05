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
                                <form action="">
                                    
                                    <div class="form-group">
                                        <label class="label-control">Role</label>
                                        <div id="icon-button">
                                        @foreach($roles as $role) 
                                            @if($role->name !== '@everyone' && !$role->managed)
                                            <button class="btn btn-outline-primary ml-1 btn-product-role" type="button" data-product-role-id="{{ $role->id }}" data-product-role-name="{{ $role->name }}" data-product-role-color="{{ dechex($role->color) }}" data-change="click" data-custom-target="#product-role" style="border-color: #{{ dechex($role->color) }}; color: #{{ dechex($role->color) }}">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                </svg>
                                                <!-- <i class="icon-discord mr-2" aria-hidden="true"></i> -->
                                                <span class="text-white btn-product-role-name">{{ $role->name }}</span>
                                            </button>
                                            @endif
                                        @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Title</label>
                                        <input type="text" class="form-control" name="title" id="product-role" placeholder="Example Role" value="" data-change="input" data-custom-target="#note-title">
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Description</label>
                                        <textarea type="text" class="form-control" name="description" rows="2" data-change="input" data-custom-target="#note-description" placeholder="This role gives you instant access to incredible chat rooms"></textarea>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="label-control d-block">Max Subscribers</label>
                                        <div class="btn-group btn-group-toggle"> 
                                            <button type="button" class="button btn button-icon btn-primary btn-max-off active" onclick="turnOffMax()">Off</button>
                                            <button type="button" class="button btn button-icon btn-primary btn-max-on" onclick="turnOnMax()">On</button>
                                        </div>
                                        <div class="btn-group btn-group-toggle" id="max-toggler" style="display:none"> 
                                            <button type="button" class="button btn button-icon btn-info input-number-decrement">-</button>
                                            <input class="form-control btn button bg-primary input-number" type="text" value="" min="0" max="100000" data-change="input" data-custom-target="#max-members">
                                            <button type="button" class="button btn button-icon btn-info input-number-increment">+</button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="label-control">Subscription Price</label>
                                        <div>
                                            <div class="form-row">
                                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control input-money" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge badge-primary">1 Day</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control input-money" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge badge-primary">1 Week</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control input-money" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge badge-primary">1 Month</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control input-money" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="badge badge-primary">1 Year</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row d-flex align-items-center justify-content-between">
                                        <div class="form-group col">
                                            <label class="label-control">Start Date</label>
                                            <input type="date" class="form-control" name="reminder_date" value="{{ date('Y-m-d') }}" data-change="input" data-custom-target="#note-reminder-date">
                                        </div>
                                        <div class="form-group col d-none">
                                            <label class="label-control">End Date</label>
                                            <input type="date" class="form-control" name="end_date" value="2021-05-01" data-change="input" data-custom-target="#note-reminder-date">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Availability</label>
                                        <div>
                                            <select name="priority" id="" class="form-control" data-change="select" data-custom-target="color">
                                                <!--<option value="danger">Archived</option>-->
                                                <option value="success">Guild Access</option>
                                                <option value="info" selected>Everyone</option>
                                                <option value="purple">Members Only</option>
                                                <!--<option value="warning">Specific Member</option>-->
                                                <option value="primary">Archived Product</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Pricing Plans</label>
                                        <div id="icon-button">
                                            <button class="btn btn-outline-primary ml-1 active" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                            <button class="btn btn-outline-primary ml-1" type="button" data-change="click" data-custom-target="#note-icon">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <button type="reset" class="btn btn-outline-primary" data-reset="note-reset">
                                        <svg width="20" class="svg-icon" id="new-note-reset" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                        </svg>
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary ml-1">
                                        <svg width="20" class="svg-icon" id="new-note-save" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Save
                                    </button>
                                    
                                </form>
                            </div>

                            <!-- example card -->
                            <div class="col-md-4" id="default">
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
                                        <p class="mb-3 text-ellipsis short-6" id="note-description">This role gives you instant access to incredible chat rooms</p>
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
                    <p class="mb-3 text-ellipsis short-6" id="note-description">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
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

<script>

        $(document).on('change', '[data-change="radio"]', function (e) {
            const value = $(this).val()
            if($(this).attr('data-custom-target') == 'product-role') {
                console.log($(this));
                const rolename = $(this).attr('data-product-role-name')
                $('#product-title').val(rolename)
                console.log(rolename)
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
        </script>
@endsection