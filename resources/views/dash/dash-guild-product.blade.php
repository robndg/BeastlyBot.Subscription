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
                                        <textarea type="text" class="form-control" id="input-description" name="description" rows="2" data-change="input" data-custom-target="#note-description" placeholder="This role gives you instant access to incredible chat rooms"></textarea>
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

                                   
                                    <div class="form-row d-flex align-items-center justify-content-between">
                                        <div class="form-group col">
                                            <label class="label-control">Start Date</label>
                                            <input type="date" class="form-control" name="reminder_date" value="{{ date('Y-m-d') }}" data-change="input" data-custom-target="#note-reminder-date"> <!-- TODO make for hours too -->
                                        </div>
                                        <div class="form-group col d-none">
                                            <label class="label-control">End Date</label>
                                            <input type="date" class="form-control" name="end_date" value="2021-05-01" data-change="input" data-custom-target="#note-reminder-date">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="label-control">Availability</label>
                                        <div>
                                            <select name="priority" id="input-access" class="form-control" data-change="select" data-custom-target="color">
                                                <!--<option value="danger">Archived</option>-->
                                                <option value="success">Guild Access</option>
                                                <option value="info" selected>Everyone</option>
                                                <option value="purple">Members Only</option>
                                                <!--<option value="warning">Specific Member</option>-->
                                                <option value="primary">Archived Product</option>
                                                
                                            </select>
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
                                        <p class="mb-3 text-ellipsis short-6" id="note-description">This role gives you instant access to incredible chat rooms</p>

                                       
                                    

                                        <div class="form-group">
                                        <label class="label-control">Subscription Price</label>
                                            <div>
                                                <div class="form-row">
                                                    <div class="form-group col-sm-12 col-lg-5">
                                                        <div>
                                                            <select name="select-interval" id="" class="form-control" data-change="select-change-interval" data-custom-target="select-interval">
                                                                <!--<option value="danger">Archived</option>-->
                                                                <option value="select-1-day">Daily</option>
                                                                <option value="select-1-week">Weekly</option>
                                                                <option value="select-1-month" selected>Monthly</option>
                                                                <!--<option value="warning">Specific Member</option>-->
                                                                <option value="select-1-year">Yearly</option>
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="select-1-day" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="input-money-1-day" data-change="input" data-custom-target="#enable-money-1-day" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">1 Day</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="select-1-week" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="input-money-1-week" data-change="input" data-custom-target="#enable-money-1-week" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">1 Week</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="select-1-month">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="input-money-1-month" data-change="input" data-custom-target="#enable-money-1-month" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">1 Month</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-lg-7 select-interval-blocks" id="select-1-year" style="display:none">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control input-money" id="input-money-1-year" data-change="input" data-custom-target="#enable-money-1-year" value="" min="1" max="100000" aria-label="(leave 0 to disable)">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">USD</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="badge badge-primary">1 Year</span> <button type="button" class="btn-sm btn btn-primary d-none">Dates</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <span class="badge badge-info">1 Day</span><span class="badge badge-info">1 Week</span><span class="badge badge-info">1 Month</span><span class="badge badge-info">1 Year</span>
                                                        </div>
                                                        <div class="input-group mb-3">

                                                            <button type="submit" class="btn btn-primary btn-block">
                                                                <svg width="20" class="svg-icon" id="new-note-save" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                                                </svg>
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


        $(document).on('change', '[data-change="select-change-interval"]', function (e) {
            const value = $(this).val()
            if($(this).attr('data-custom-target') == 'select-interval') {
                $('.select-interval-blocks').hide();
                console.log(value);
                $(`#${value}`).show();
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


@section('scripts')
<script>

var product_uuid = {{ $product_role->uuid ?? 0 }}

function saveProduct()

    $.ajax({
        url: '/bknd00/saveGuildProductRole',
        type: 'POST',
        data: {
            'id' => {{ $product_role->uuid ?? 0 }},
            'discord_store_id': '{{ $guild_id }}',
            'role_id': $role_id,//$product_id,
            'description': $('#input-description').val(),
            'active': $('#input-active').val(),
            'start_date': $start_date,
            'end_date': $end_date,
            'max_sales': $max_sales,
            _token: '{{ csrf_token() }}'
        },
    }).done(function (msg) {
        if(!msg['success']){
            Swal.fire({
                title: 'Count not save, try again',
                //text: "Awesome. Loading your store front...",
                type: 'info',
                showCancelButton: false,
                showConfirmButton: true,
            });
        }else{
            Swal.fire({
                title: 'Bot Found!',
                text: "Awesome. Loading your store front...",
                type: 'success',
                showCancelButton: false,
                showConfirmButton: true,
            });

            product_uuid = msg['product_uuid'];
           
            //window.location.href = '/dashboard/' + msg['store'].guild_id
        }
    })
</script>

                    @endsection