@extends('layouts.dash')

@section('title', 'Dashboard Guild Settings')

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
                    <h4>{{ $guild->name }}: Store Settings</h4>
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
    <!--protected $fillable = ['store_type', 'store_id', 'store_image', 'store_name', 'url_slug', 'description', 'about', 'members_only', 
    // bot settings page 'welcome_message', 'welcome_message_settings', 
    'refunds_enabled', 'refunds_terms', 'refunds_days', 'recurring_referrals', 'referral_percent_fee', 'cancel_subscriptions_on_exit', 'disable_public_downgrades', 'terms_of_service', 
    'premium', 'remove_network', 'main_color', 'secondary_color', 'show_beastly', 'eyes_color', 'allow_featured', 'metadata']; -->

    <div class="row">
            <div class="col-xl-3 col-lg-4">
                  <div class="card">
                     <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                           <h4 class="card-title">Store Settings</h4>
                        </div>
                     </div>
                     <div class="card-body">
                        <form>
                           <div class="form-group text-center">
                              <div class="d-flex justify-content-center">
                                 <div class="crm-profile-img-edit">
                                    <img class="crm-profile-pic avatar-100 save-target" data-save-settings="store_image" data-save="src" src="{{ $settings->store_image }}" alt="server-pic">
                                    <div class="crm-p-image bg-primary">
                                       <i class="las la-sync upload-button"></i>
                                       <input class="file-upload" type="button" id="settings-store_image">
                                    </div>
                                 </div>
                              </div>
                           <div class="img-extension mt-3">
                              <div class="d-inline-block align-items-center">
                                    <span>Store Image</span>
                                 <a href="javascript:void();">Refresh</a>
                              </div>
                           </div>
                           </div>
                           <div class="form-group">
                              <label for="furl">Store Name:</label>
                              <input type="text" class="form-control save-target" id="settings-store_name" data-save-settings="store_name" data-save="text" placeholder="{{ $settings->store_name }}" value="{{ $settings->store_name }}">
                           </div>
                           <div class="form-group">
                              <label for="furl">Store URL:</label>
                              <input type="text" class="form-control save-target" id="settings-url_slug" data-save-settings="url_slug" data-save="text" placeholder="{{ Str::title(str_replace(' ', '-', $settings->store_name)) }}" value="{{ $settings->url_slug }}">
                           </div>
                           <div class="form-group">
                              <label>Store Access:</label>
                              <select class="form-control save-target" id="settings-members_only" data-save-settings="members_only" data-save="select">
                                 <option value="0" selected>Everyone</option>
                                 <option value="1">Members Only</option>
                              </select>
                           </div>
                        </form>
                     </div>
                  </div>
            </div>
            <div class="col-xl-9 col-lg-8">
                  <div class="card">
                     <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                           <h4 class="card-title">Store Information</h4>
                        </div>
                     </div>
                     <div class="card-body">
                        <div class="">
                           <form>
                              <div class="row">
                                 <div class="form-group col-md-12">
                                    <label for="fname">Description:</label>
                                    <input type="text" class="form-control save-target" data-save-settings="description" data-save="text" id="settings-description" placeholder="Store Description" value="{{ $settings->description }}">
                                 </div>
                                 <div class="form-group col-md-12">
                                    <label for="lname">About:</label>
                                    <textarea type="text" class="form-control save-target" data-save-settings="about" data-save="textarea" rows="3" id="settings-about" placeholder="What is {{ $settings->store_name }} About?">{!! $settings->description !!}</textarea>
                                 </div>

                                <div class="form-group col-md-12">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-primary">
                                        Cancel Subscriptions On Exit:
                                            <button type="button" class="btn btn-info save-target" id="settings-cancel_subscriptions_on_exit" data-save-settings="cancel_subscriptions_on_exit" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-success">
                                        Disable Public Downgrades:
                                            <button type="button" class="btn btn-primary save-target" id="settings-disable_public_downgrades" data-save-settings="disable_public_downgrades" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-success d-none">
                                        Refunds Enabled:
                                            <button type="button" class="btn btn-info save-target" id="settings-refunds_enabled" data-save-settings="disable_public_downgrades" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-danger">
                                        Refunds Enabled:
                                            <div class="btn-group btn-group-toggle"> 
                                                <button type="button" class="button btn button-icon btn-primary btn-max-off" onclick="turnRefundOff()">No</button>
                                                <button type="button" class="button btn button-icon btn-info btn-max-on" onclick="turnRefundOn()">Yes</button>
                                            </div>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-danger">
                                        Refunds Enabled:
                                            <div>
                                                <select class="form-control save-target" id="settings-refunds_enabled" data-save-settings="cancel_subscriptions_on_exit" data-save="button">
                                                    <option value="1" selected>Enabled</option>
                                                    <option value="0">Disabled</option>
                                                </select>
                                            </div>
                                        </li>


                                        
                                        <!-- if yes show other fields -->
                                    </ul>
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="lname">Terms of Service:</label>
                                    <textarea type="text" class="form-control" rows="2" id="settings-terms_of_service" data-save-settings="terms_of_service" data-save="textarea" placeholder="TODO: will fill in with buttons above clicked and stuff for them">{!! $settings->terms_of_service !!}</textarea>
                                 </div>
                                 
                              </div>
                              {{--<hr>
                              <h5 class="mb-3">Security</h5>
                              <div class="row">
                                 <div class="form-group col-md-12">
                                    <label for="uname">User Name:</label>
                                    <input type="text" class="form-control" id="uname" placeholder="User Name">
                                 </div>
                                 <div class="form-group col-md-6">
                                    <label for="pass">Password:</label>
                                    <input type="password" class="form-control" id="pass" placeholder="Password">
                                 </div>
                                 <div class="form-group col-md-6">
                                    <label for="rpass">Repeat Password:</label>
                                    <input type="password" class="form-control" id="rpass" placeholder="Repeat Password ">
                                 </div>
                              </div>
                              <div class="checkbox">
                                 <label><input class="mr-2" type="checkbox">Enable Two-Factor-Authentication</label>
                              </div>--}}
                              <button type="submit" class="btn btn-success float-right" style="display:none">Save Changes</button> <!-- TODO: check vars jquery for changes -->
                           </form>
                        </div>
                     </div>
                  </div>
            </div>
         </div>



    <!-- end page content -->

</div><!-- end container div -->

@endsection('content')

@section('scripts')


<script>

$(document).on('change', '[data-save="text"]', function (e) {

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

$(document).on('click', '[data-save="button"]', function (e) {
    $('.btn-product-role').removeClass('active');
    const value = $(this).val()
   /* if($(this).attr('data-custom-target') == '#product-role-id') {
        const roleid = $(this).attr('data-product-role-id')
        $('#product-role-id').val(roleid)
        const rolename = $(this).attr('data-product-role-name')
        $('#product-title').val(rolename)
        $('#note-title').val(rolename)
        $(this).addClass('active');
        console.log(roleid)
    }*/
    console.log('Saving Button')
})
$(document).on('change', '[data-change="textarea"]', function (e) {
    const value = $(this).val()
    const textarea = value.data('save-target');
   /* if($(this).attr('data-custom-target') == 'select-interval') {
        $('.select-interval-blocks').hide();
        console.log(value);
        $(`#input-money-1-${value}`).show();
    }*/
    console.log('Saving Text Area')
})

$(document).on('change', '[data-change="src"]', function (e) {
    const value = $(this).val()
    const src = value.data('save-target');
    //if($(this).attr('src') == 'store_image') {
       /* $('#note-icon').attr('class',' ')
        $('#update-note').attr('class', ' ')

        $('#note-icon').addClass(`icon iq-icon-box-2 icon-border-${value} rounded`);

        $('#update-note').addClass(`card card-block card-stretch card-height card-bottom-border-${value} note-detail`)*/
   // }
})

$(document).on('change', '[data-change="select"]', function (e) {
    const value = $(this).val()
    const color = value.data('save-target');
   /* console.log(color)
    if($(this).attr('data-custom-target') == 'color') {
        
        $('#note-icon').attr('class',' ')
        $('#update-note').attr('class', ' ')
        $('#note-icon').addClass(`icon iq-icon-box-2 icon-border-${color} rounded`)
        $('#update-note').addClass(`card card-block card-stretch card-height card-bottom-border-${color} note-detail`)
    }*/
    console.log('Saving Select')
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

//console.log(new Date($('#start_date').val() + "T" + $('#start_time').val()));

var shop_UUID = '{{ $shop->UUID }}';

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
           
            Swal.fire({
                title: 'Product Saved!',
               // text: "Awesome... add some prices",
                type: 'success',
                showCancelButton: false,
                showConfirmButton: true,
            });
            if(product_uuid == 0 || product_uuid  == 'undefined' || product_uuid == null || !product_uuid){
            newProduct_uuid = msg['product_uuid'];
                var url = document.location.href+"?uuid=" + newProduct_uuid;
                document.location = url;
            }
           
           

           

        
           
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