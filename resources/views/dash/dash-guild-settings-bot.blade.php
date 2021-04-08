@extends('layouts.dash')

@section('title', 'Dashboard Guild Settings')

@section('styles')

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
                                            <button class="btn btn-outline-info ml-1" type="button" data-change="click" data-custom-target="eyes-buttons" id="settings-eyes_color" data-save-settings="eyes_color" data-original="{{ $settings->eyes_color}}" data-new="" data-save="button">
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
                                            <button class="btn btn-outline-light ml-1 eyes-buttons save-target" style="display:none; border-color:#fd6847; color:#fd6847;" type="button" data-change="eyes" data-custom-target="#fd6847">
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
var showSave = false;

function showShowButton(){
    showSave = true;
    $('#settings-save-button').slideDown();

}

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
    const current_save_settings = $(this).attr('data-original');
    var name_save_settings = $(this).attr('data-save-settings');
    if($(this).attr('data-custom-target') != current_save_settings) {
        console.log('Eyes Color Changed');
        var save_eyes_color = $(this).attr('data-custom-target');
        console.log(save_eyes_color);
        $('#css-changes').empty();
        $( "<style>.custom-beastly .xx-head::after { background-color: " + save_eyes_color + "; box-shadow: 12.25vmin 0 " + save_eyes_color + ";  }</style>" ).appendTo( "#css-changes" );
        $('#settings-'+name_save_settings).attr('data-new', save_eyes_color);
       /* $('.xx-head::after').css(
        {
            'background-color': save_eyes_color,
            'box-shadow': '12.25vmin 0 ' + save_eyes_color
        }
        )*//*.css("background-color", save_eyes_color)*//*.css("box-shadow", "12.25vmin 0 " + save_eyes_color);*/
        showShowButton();
    }else{
        $('#css-changes').empty();
        //$('.save-changes-button').slideUp();
    }
    
});

// Removed will just show save if anything data-new changed.

/*function checkShowSave(){
    if(description || about || cancel_subscriptions_on_exit || disable_public_downgrades || refunds_enabled){
        showSave = true;
        console.log("checkShowSave: true save button");
    }else{
        showSave = false;
        console.log("checkShowSave: false save button");
    }
}*/


$(document).on('change', '[data-save="text"]', function (e) {
    
        var name_save_settings = $(this).attr('data-save-settings');
        const current_save_settings = $(this).attr('data-original');
        if($(this).attr('data-premium')){
            console.log("Premium");
            @if($settings->premium == 0)
            $('.go-premium-button').addClass('btn-info');
            
            setTimeout(function(){ 
                console.log(name_save_settings);
                console.log(current_save_settings);
                $('#settings-'+name_save_settings).val(current_save_settings)
                $('.go-premium-button').removeClass('btn-info');
            }, 750);
            goPremiumButton();
            @else
            var premium_url_string = "{{ Str::title(str_replace(' ', '-', $settings->store_name)) }}";
            var premium_guild_id = "{{ $guild_id }}";
            setTimeout(function(){ 
                if($('#settings-'+name_save_settings).val() == null){
                    $('#settings-'+name_save_settings).val(premium_guild_id);
                    $('#settings-'+name_save_settings).attr('data-new', premium_guild_id);
                }else if($('#settings-'+name_save_settings).val() != premium_url_string && $('#settings-'+name_save_settings).val() != premium_guild_id){
                    setTimeout(function(){ 
                    console.log(name_save_settings);
                    console.log(current_save_settings);
                    $('#settings-'+name_save_settings).val(premium_url_string)
                    $('#settings-'+name_save_settings).attr('data-new', premium_url_string);
                    }, 400);
                }
            }, 100);
            showShowButton();
            @endif
            
        }else{
        var new_save_setting = $(this).attr('data-new');

        if((current_save_settings != new_save_setting) || new_save_setting == null){
            new_save_setting = $(this).val();
            $(this).attr('data-new', new_save_setting);
        }else{
            new_save_setting = $(this).val();
            $(this).attr('data-new', new_save_setting);
        }
            showShowButton();
        }
    
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


</script>

<script>
var showSave = false;
// Removed will just show save if anything data-new changed.
/*
{{-- const settings_description = '{!!$settings->description!!}}' // exmaple settings-refunds_enabled --}}
var description = false;
var description_save = false;

{{-- const settings_about = '{!!$settings->about!!}}' // exmaple settings-refunds_enabled --}}
var about = false;
var about_save = false;

{{-- const settings_cancel_subscriptions_on_exit = '{{$settings->cancel_subscriptions_on_exit}}'
var cancel_subscriptions_on_exit = false;
var cancel_subscriptions_on_exit_save = false;

{{-- const settings_disable_public_downgrades = '{{$settings->disable_public_downgrades}}' --}}
var disable_public_downgrades = false;
var disable_public_downgrades_save = false;

{{-- const settings_refunds_enabled = '{{settings->refunds_enabled}}' --}}
var refunds_enabled = false;
var refunds_enabled_save = false;

{{-- const settings_terms_of_service = '{!!$settings->terms_of_service!!}}' --}}
var terms_of_service = false;
var terms_of_service_save = false;
*/

function showShowButton(){
    showSave = true;
    $('#settings-save-button').slideDown();

}
// Removed will just show save if anything data-new changed.

/*function checkShowSave(){
    if(description || about || cancel_subscriptions_on_exit || disable_public_downgrades || refunds_enabled){
        showSave = true;
        console.log("checkShowSave: true save button");
    }else{
        showSave = false;
        console.log("checkShowSave: false save button");
    }
}*/

$(document).on('click', '[data-save="button"]', function (e) {

const current_save_settings = $(this).attr('data-original');
var new_save_setting = $(this).attr('data-new');

    if((current_save_settings == 1 && new_save_setting  == null) || (new_save_setting == 1)){
        new_save_setting = 0;
        console.log('Button 0');
        $(this).removeClass('btn-info').addClass('btn-primary');
    }else{
        new_save_setting = 1;
        console.log('Button 1');
        $(this).removeClass('btn-primary').addClass('btn-info');
    }
    $(this).attr('data-new', new_save_setting);
    console.log("New Save Setting");
    console.log(new_save_setting);
    showShowButton();

});

$(document).on('change', '[data-save="select"]', function (e) {
    const current_save_settings = $(this).attr('data-original');
    var new_save_setting = $(this).attr('data-new');
    new_save_setting = $(this).val()
    $(this).attr('data-new', new_save_setting);
    console.log("New Select Option");
    console.log(new_save_setting);
    showShowButton();
});

$(document).on('change', '[data-save="text"]', function (e) {
    
        var name_save_settings = $(this).attr('data-save-settings');
        const current_save_settings = $(this).attr('data-original');
        if($(this).attr('data-premium')){
            console.log("Premium");
            @if($settings->premium == 0)
            $('.go-premium-button').addClass('btn-info');
            
            setTimeout(function(){ 
                console.log(name_save_settings);
                console.log(current_save_settings);
                $('#settings-'+name_save_settings).val(current_save_settings)
                $('.go-premium-button').removeClass('btn-info');
            }, 750);
            goPremiumButton();
            @else
            var premium_url_string = "{{ Str::title(str_replace(' ', '-', $settings->store_name)) }}";
            var premium_guild_id = "{{ $guild_id }}";
            setTimeout(function(){ 
                if($('#settings-'+name_save_settings).val() == null){
                    $('#settings-'+name_save_settings).val(premium_guild_id);
                    $('#settings-'+name_save_settings).attr('data-new', premium_guild_id);
                }else if($('#settings-'+name_save_settings).val() != premium_url_string && $('#settings-'+name_save_settings).val() != premium_guild_id){
                    setTimeout(function(){ 
                    console.log(name_save_settings);
                    console.log(current_save_settings);
                    $('#settings-'+name_save_settings).val(premium_url_string)
                    $('#settings-'+name_save_settings).attr('data-new', premium_url_string);
                    }, 400);
                }
            }, 100);
            showShowButton();
            @endif
            
        }else{
        var new_save_setting = $(this).attr('data-new');

        if((current_save_settings != new_save_setting) || new_save_setting == null){
            new_save_setting = $(this).val();
            $(this).attr('data-new', new_save_setting);
        }else{
            new_save_setting = $(this).val();
            $(this).attr('data-new', new_save_setting);
        }
            showShowButton();
        }
    
})

// Removed, will show Save if any data-new

/*function setToggleSaveSettings(name_save_setting){
    console.log('setToggleSaveSettings');
    const element = $('#'+name_save_setting);
    const new_save_setting = element.attr('data-new');
    const original_save_setting = element.attr('data-new');
    if(new_save_setting != current_save_settings){
        if(new_save_setting == 1){
            $('#settings_'+name_save_setting).removeClass('btn-default').addClass('btn-info');
            name_save_settings = new_save_setting;
            console.log("New Save Enabled")
        }else{
            $('#settings_'+name_save_setting).removeClass('btn-info').addClass('btn-default');
            name_save_settings = new_save_setting;
            console.log("New Save Disabled")
        }
        
    }
 
}*/

</script>
<script>

function saveGuildStoreSettings() {
    $('#settings-save-button').html("Saving...");

    $.ajax({
        url: '/bknd00/saveGuildSettings', // TODO Controller: with if's for Premium / if data-new == null skip.
        type: 'POST',
        data: {
            'discord_store_id': '{{ $shop->UUID }}', // get store_settings from shop uuid to hide primary key
            'store_image': $('#settings-store_image').attr('data-new'),
            'store_name': $('#settings-store_name').attr('data-new'),
            'url_slug': $('#settings-url_slug').attr('data-new'),
            'members_only': $('#settings-members_only').attr('data-new'),
            'description': $('#settings-description').attr('data-new'),
            'about':$('#settings-about').attr('data-new'), // TODO: get from new description plugin
            'cancel_subscriptions_on_exit': $('#settings-cancel_subscriptions_on_exit').attr('data-new'),
            'disable_public_downgrades': $('#settings-disable_public_downgrades').attr('data-new'),
            'refunds_enabled': $('#settings-refunds_enabled').attr('data-new'),
            'terms_of_service': $('#settings-terms_of_service').attr('data-new'),
            _token: '{{ csrf_token() }}'
        },
    }).done(function (msg) {
        console.log(msg);
        if(!msg['success']){
            Swal.fire({
                title: 'Store Settings not Saved',
                text: msg['message'],
                type: 'info',
                showCancelButton: false,
                showConfirmButton: true,
            });
            $('#settings-save-button').html("Save Changes");
            $('#settings-save-button').slidUp();
        }else{
           
            Swal.fire({
                title: 'Store Settings Saved',
               // text: "Awesome... add some prices",
                type: 'success',
                showCancelButton: false,
                showConfirmButton: true,
            });
            $('#settings-save-button').html("Save Changes");
            $('#settings-save-button').slidUp();
            // TODO: add view store button if saved
            //window.location.href = '/shop/' + msg['store_slug']
        }
    })
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