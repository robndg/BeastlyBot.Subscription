@extends('layouts.dash')

@section('title', 'Dashboard Guild Settings')

@section('styles')
<style>
    #note-icon .btn-product-role-name{
        display:none;
        visibility:0;
    }


@-webkit-keyframes shadowx {
	from {
		-webkit-transform: translate(0, -1.25vmin) scale(1, 1);
		transform: translate(0, -1.25vmin) scale(1, 1);
		background-color: rgba(0, 0, 0, 0.1);
	}
	to {
		-webkit-transform: translate(0, 0) scale(1.3, 1);
		transform: translate(0, 0) scale(1.3, 1);
		background-color: rgba(0, 0, 0, 0.05);
	}
}

@keyframes shadowx {
	from {
		-webkit-transform: translate(0, -1.25vmin) scale(1, 1);
		transform: translate(0, -1.25vmin) scale(1, 1);
		background-color: rgba(0, 0, 0, 0.1);
	}
	to {
		-webkit-transform: translate(0, 0) scale(1.3, 1);
		transform: translate(0, 0) scale(1.3, 1);
		background-color: rgba(0, 0, 0, 0.05);
	}
}

@-webkit-keyframes hoverx {
	from {
		-webkit-transform: translate(0, 0);
		transform: translate(0, 0);
	}
	to {
		-webkit-transform: translate(0, -1.25vmin);
		transform: translate(0, -1.25vmin);
	}
}

@keyframes hoverx {
	from {
		-webkit-transform: translate(0, 0);
		transform: translate(0, 0);
	}
	to {
		-webkit-transform: translate(0, -1.25vmin);
		transform: translate(0, -1.25vmin);
	}
}

@-webkit-keyframes beat {
	0% {
		background-color: rebeccapurple;
		box-shadow: 0 0 0 0 rgba(220, 20, 60, 0.3);
	}
	50%, 70% {
		background-color: powderblue;
		box-shadow: 0 0 10vmin 12.5vmin rgba(176, 224, 230, 0);
	}
	100% {
		background-color: rebeccapurple;
	}
}

@keyframes beatx {
	0% {
		background-color: rebeccapurple;
		box-shadow: 0 0 0 0 rgba(220, 20, 60, 0.3);
	}
	50%, 70% {
		background-color: powderblue;
		box-shadow: 0 0 10vmin 12.5vmin rgba(176, 224, 230, 0);
	}
	100% {
		background-color: rebeccapurple;
	}
}

@-webkit-keyframes wavex {
	from {
		-webkit-transform: rotate(15deg);
		transform: rotate(15deg);
	}
	to {
		-webkit-transform: rotate(80deg);
		transform: rotate(80deg);
	}
}

@keyframes wavex {
	from {
		-webkit-transform: rotate(15deg);
		transform: rotate(15deg);
	}
	to {
		-webkit-transform: rotate(80deg);
		transform: rotate(80deg);
	}
}

@-webkit-keyframes wobblex {
	0% {
		-webkit-transform: translate(-0.25vmin, 0);
		transform: translate(-0.25vmin, 0);
		background-color: crimson;
		box-shadow: 0 0 0 0 rgba(220, 20, 60, 0.3);
	}
	50%, 70% {
		-webkit-transform: translate(0.25vmin, 0);
		transform: translate(0.25vmin, 0);
		background-color: mediumvioletred;
		box-shadow: 0 0 10vmin 12.5vmin rgba(220, 20, 60, 0);
	}
	100% {
		-webkit-transform: translate(-0.25vmin, 0);
		transform: translate(-0.25vmin, 0);
		background-color: rebeccapurple;
	}
}

@keyframes wobblex {
	0% {
		-webkit-transform: translate(-0.25vmin, 0);
		transform: translate(-0.25vmin, 0);
		background-color: crimson;
		box-shadow: 0 0 0 0 rgba(220, 20, 60, 0.3);
	}
	50%, 70% {
		-webkit-transform: translate(0.25vmin, 0);
		transform: translate(0.25vmin, 0);
		background-color: mediumvioletred;
		box-shadow: 0 0 10vmin 12.5vmin rgba(220, 20, 60, 0);
	}
	100% {
		-webkit-transform: translate(-0.25vmin, 0);
		transform: translate(-0.25vmin, 0);
		background-color: rebeccapurple;
	}
}

.xx {
	display: block;
	position: relative;
	-webkit-animation: hoverx 1500ms ease-in-out alternate infinite;
	animation: hoverx 1500ms ease-in-out alternate infinite;
}

.xx-head {
	width: 25vmin;
	height: 12.5vmin;
	display: block;
	position: relative;
	border-radius: 6.25vmin;
	background-color: #27262c;
	box-shadow: 0 0 0 3.25vmin white inset;
	transition: -webkit-transform ease-in-out 350ms;
	transition: transform ease-in-out 350ms;
	transition: transform ease-in-out 350ms, -webkit-transform ease-in-out 350ms;
}

.xx-head::before {
	content: '';
	display: block;
	width: 4vmin;
	height: 4vmin;
	position: absolute;
	top: -6vmin;
	left: 10.5vmin;
	border-radius: 50%;
	background-color: white;
	box-shadow: 0 1vmin 0 -1.5vmin white, 0 0.5vmin 0 -1.5vmin white, 0 1vmin 0 -1.5vmin white, 0 1.5vmin 0 -1.5vmin white, 0 2vmin 0 -1.5vmin white, 0 2.5vmin 0 -1.5vmin white, 0 3vmin 0 -1.5vmin white, 0 3.5vmin 0 -1.5vmin white, 0 4vmin 0 -1.5vmin white;
}

.xx-head::after {
	content: '';
	display: block;
	width: 3.75vmin;
	height: 3.75vmin;
	position: absolute;
	top: 4.5vmin;
	left: 4.5vmin;
	border-radius: 1.875vmin;
	background-color: powderblue;
	box-shadow: 12.25vmin 0 powderblue;
	transition: inherit;
}

.xx-head:hover {
	-webkit-transform: rotate(15deg) translate(2.5vmin, 0);
	transform: rotate(15deg) translate(2.5vmin, 0);
}

.xx-head:hover::after {
	-webkit-transform: scale(1, 0.1);
	transform: scale(1, 0.1);
}

.xx-body {
	width: 20vmin;
	height: 22.5vmin;
	position: absolute;
	top: 15vmin;
	left: 2.5vmin;
	display: block;
	overflow: hidden;
	border-radius: 50% 50% 50% 50% / 30% 30% 70% 70%;
	background: white;
}

.xx-body::after {
	content: '';
	display: block;
	width: 3.75vmin;
	height: 3.75vmin;
	position: absolute;
	top: 6.25vmin;
	left: 12vmin;
	border-radius: 50%;
	-webkit-animation: beatx 4500ms linear infinite;
	animation: beatx 4500ms linear infinite;
}

.xx-body:hover::after {
	-webkit-animation: wobblex 1000ms linear infinite;
	animation: wobblex 1000ms linear infinite;
}

.xx-hand {
	width: 8.5vmin;
	height: 8.5vmin;
	position: absolute;
	top: 7.5vmin;
	left: 21.25vmin;
	display: block;
	border-radius: 50%;
	-webkit-transform-origin: 50% 12vmin;
	transform-origin: 50% 12vmin;
	box-shadow: 0 7.5vmin 0 -2.5vmin white;
	-webkit-animation: wavex 1000ms alternate ease-in-out infinite;
	animation: wavex 1000ms alternate ease-in-out infinite;
}

.xx-hand::after {
	content: '';
	display: block;
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	-webkit-clip-path: polygon(0% 0%, 50% 50%, 100% 0%, 100% 100%, 0% 100%);
	clip-path: polygon(0% 0%, 50% 50%, 100% 0%, 100% 100%, 0% 100%);
	border-radius: 50%;
	box-shadow: 0 0 0 2.5vmin white inset;
}

.xx::after {
	content: '';
	display: block;
	width: 15vmin;
	height: 3.75vmin;
	position: absolute;
	top: 40vmin;
	left: 5vmin;
	border-radius: 50%;
	-webkit-animation: shadowx 1500ms ease-in-out alternate infinite;
	animation: shadowx 1500ms ease-in-out alternate infinite;
}
</style>
<span id="css-changes">
</span>
@endsection
@section('content')
<div class="container-fluid"><!-- container div -->

    <div class="desktop-header"> <!-- header div -->
        <div class="card card-block topnav-left">
            <div class="card-body write-card">
                <div class="d-flex align-items-center justify-content-between">
                    <h4>{{ $guild->name }}: Bot Settings</h4>
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
    
            <div class="col-xl-8 col-lg-7">
                  <div class="card">
                     <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                           <h4 class="card-title">Server Bot</h4>
                        </div>
                     </div>
                     <div class="card-body">
                        <div class="new-user-info">
                           <form>
                              <div class="row">
                                
                                 <div class="form-group col-md-12">
                                    <label for="lname">Bot Welcome Message</label>
                                    <textarea type="text" class="form-control save-target" data-save-settings="about" data-save="textarea" rows="2" id="settings-about" placeholder="Discord User has subscribed to Role Name in {{ $settings->store_name }}! Congratulations.">{!! $settings->welcome_message !!}</textarea>
                                 </div>

                                 <div class="form-group col-md-12">
                                        <label class="label-control">Beastly Eyes Color</label>
                                        <div id="icon-button">
                                            <button class="btn btn-outline-info ml-1" type="button" data-change="click" data-custom-target="eyes-buttons">
                                                <svg width="23" class="svg-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                                </svg>
                                            </button>
                                        
                                         
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#b0e0e6; color:#b0e0e6;" type="button" data-change="eyes" data-custom-target="#b0e0e6">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#fb1640; color:#fb1640;" type="button" data-change="eyes" data-custom-target="#fb1640">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#fd47d6; color:#fd47d6;" type="button" data-change="eyes" data-custom-target="#fd47d6">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#6347fd; color:#6347fd;" type="button" data-change="eyes" data-custom-target="#6347fd">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#47fd98; color:#47fd98;" type="button" data-change="eyes" data-custom-target="#47fd98">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#fafd47; color:#fafd47;" type="button" data-change="eyes" data-custom-target="#fafd47">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                            <button class="btn btn-outline-light ml-1 eyes-buttons" style="display:none; border-color:#fd6847; color:#fd6847;" type="button" data-change="eyes" data-custom-target="#fd6847">
                                                <i class="las la-meh-rolling-eyes mr-0"></i>
                                            </button>
                                        </div>
                                    </div>
                                <div class="form-group col-md-12 pt-2">
                                    <ul class="list-group">
                                        {{--<li class="list-group-item d-flex justify-content-between align-items-center iq-bg-primary">
                                        <span>Show Beastly: <span class="badge-primary badge-sm">Premium <i class="las la-certificate"></i></span></span>
                                            <button type="button" class="btn btn-info save-target" id="settings-cancel_subscriptions_on_exit" data-save-settings="cancel_subscriptions_on_exit" data-save="button"><i class="ri-radio-button-fill pr-0"></i></button>
                                        </li>--}}
                                        
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-danger">
                                        <span>Show Beastly: <span class="btn btn-sm btn-outline-info pr-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Go Premium"><i class="las la-certificate"></i></span></span>
                                            <div class="btn-group btn-group-toggle"> 
                                                <button type="button" class="button btn button-icon btn-primary btn-max-off disabled" onclick="turnBeastlyOff()">Hide</button>
                                                <button type="button" class="button btn button-icon btn-info btn-max-on active disabled" onclick="turnBeastlyOn()">Show</button>
                                            </div>
                                        </li>
                                       
                                        <!-- if yes show other fields -->
                                    </ul>
                                </div>
                                 <div class="form-group col-md-12 d-none">
                                    <label for="lname">Terms of Service:</label>
                                    <textarea type="text" class="form-control" rows="2" id="settings-terms_of_service" data-save-settings="terms_of_service" data-save="textarea" placeholder="">{!! $settings->terms_of_service !!}</textarea>
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
                              <button type="submit" class="btn btn-success float-right save-changes-button" style="display:none">Save Changes</button> <!-- TODO: check vars jquery for changes -->
                           </form>
                        </div>
                     </div>
                  </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                  <!---<div class="card">
                     <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                           <h4 class="card-title">Beastly Bot</h4>
                        </div>
                     </div>
                     <div class="card-body">-->
                        <div class="custom-beastly" style="margin-top: 100px;">
                        <div class="d-flex justify-content-center">
                            <div class="xx banner-image">
                                <div class="xx-head"></div>
                                <div class="xx-body"></div>
                                <div class="xx-hand"></div>
                            </div>
                        </div>
                        </div>
                        
                     <!--</div>
                  </div>-->
            </div>


         </div>



    <!-- end page content -->

</div><!-- end container div -->

@endsection('content')

@section('scripts')


<script>

var eyes_color = "{{ $settings->eyes_color }}";
var save_eyes_color = false;

var eyes_shown = false;
$(document).on('click', '[data-change="click"]', function (e) {
    //$('.btn-product-role').removeClass('active');
    const value = $(this).val()
    if($(this).attr('data-custom-target') == 'eyes-buttons') {
       if(eyes_shown){
        $('.eyes-buttons').hide();
        eyes_shown = false;
        console.log('Eyes Buttons Hide')
       }else{
        $('.eyes-buttons').slideDown();
        eyes_shown = true;
        console.log('Eyes Buttons Shown')
       }
    }
    
});

$(document).on('click', '[data-change="eyes"]', function (e) {
    //$('.btn-product-role').removeClass('active');
    const value = $(this).val();
    console.log('Eyes button');
    if($(this).attr('data-custom-target') != eyes_color) {
        console.log('Eyes Color Changed');
        var save_eyes_color = $(this).attr('data-custom-target');
        console.log(save_eyes_color);
        $('#css-changes').empty();
        $( "<style>.custom-beastly .xx-head::after { background-color: " + save_eyes_color + "; box-shadow: 12.25vmin 0 " + save_eyes_color + ";  }</style>" ).appendTo( "#css-changes" );
       /* $('.xx-head::after').css(
        {
            'background-color': save_eyes_color,
            'box-shadow': '12.25vmin 0 ' + save_eyes_color
        }
        )*//*.css("background-color", save_eyes_color)*//*.css("box-shadow", "12.25vmin 0 " + save_eyes_color);*/
        $('.save-changes-button').slideDown();
    }else{
        $('#css-changes').empty();
        $('.save-changes-button').slideUp();
    }
    
});

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