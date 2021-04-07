@extends('layouts.dash')

@section('title', 'Dashboard Guild Settings')

@section('content')
<div class="container-fluid"><!-- container div -->

    <div class="desktop-header"> <!-- header div -->
        <div class="card card-block topnav-left">
            <div class="card-body write-card">
                <div class="d-flex align-items-center justify-content-between">
                    <h4>{{ $guild->name }}: Store Settings</h4>
                    <div>
                    <a href="/dashboard/{{ $guild_id }}" class="btn btn-outline-primary svg-icon">
                        <svg  width="20" class="svg-icon" id="new-note-back" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                        <span>Back</span>
                    </a>
                    <button type="button" class="btn btn-success ml-2" id="settings-save-button" onclick="saveGuildStoreSettings()" style="display:none">Save Changes</button> <!-- TODO: check vars jquery for changes -->
                    </div>
                </div>
            </div>
        </div>
        @include('partials.dash.topnav-right')
    </div>

    <!-- start page content -->

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
                                    <img class="crm-profile-pic avatar-100 save-target" data-save-settings="store_image" data-save="src" src="{{ $settings->store_image }}" data-original="{{ $settings->store_image }}" data-new="" alt="server-pic">
                                    <div class="crm-p-image bg-primary">
                                       <i class="las la-sync upload-button"></i>
                                       <input class="file-upload" type="button" id="settings-store_image"> <!-- TODO: ajax get new store image -->
                                    </div>
                                 </div>
                              </div>
                           <div class="img-extension mt-3">
                              <div class="d-inline-block align-items-center">
                                    <span>Store Image</span>
                                 <!--<a href="javascript:void();">Refresh</a>-->
                              </div>
                           </div>
                           </div>
                           <div class="form-group">
                              <label for="furl">Store Name:</label>
                              <input type="text" class="form-control save-target" id="settings-store_name" data-save-settings="store_name" data-original="{{ $settings->store_name }}" data-new="" data-save="text" placeholder="{{ $settings->store_name }}" value="{{ $settings->store_name }}">
                           </div>
                           <div class="form-group">
                            @if($settings->premium == 0)
                              <label for="furl">Store URL: <span class="btn btn-sm btn-outline-info pr-1 go-premium-button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Go Premium" onclick="goPremiumButton()"><i class="las la-certificate"></i></span></label>
                              <input type="text" class="form-control save-target" id="settings-url_slug" data-save-settings="url_slug" data-original="{{ $settings->url_slug }}" data-new="" data-save="text" data-premium="true" placeholder="{{ Str::title(str_replace(' ', '-', $settings->store_name)) }}" value="{{ $settings->url_slug }}">
                            @else
                            <label for="furl">Store URL: <span class="btn btn-sm btn-info pr-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Premium"><i class="las la-certificate"></i></span></label>
                              <input type="text" class="form-control save-target" id="settings-url_slug" data-save-settings="url_slug" data-original="{{ $settings->url_slug }}" data-new="" data-save="text" data-premium="true" placeholder="{{ $guild_id }}" value="{{ $settings->url_slug }}">
                            @endif
                           </div>
                           <div class="form-group">
                              <label>Store Access:</label>
                              <select class="form-control save-target" id="settings-members_only" data-save-settings="members_only" data-original="{{ $settings->members_only }}" data-new="" data-save="select">
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
                           
                              <div class="row">
                                 <div class="form-group col-md-12">
                                    <label for="fname">Introduction:</label>
                                    <input type="text" class="form-control save-target" id="settings-description" data-save-settings="description" data-original="{!! $settings->description !!}" data-new="" data-save="text" placeholder="Store Description" value="{!! $settings->description !!}">
                                 </div>
                                 <div class="form-group col-md-12">
                                    <label for="lname" class="mb-0">{{ 'What is ' . $settings->store_name . ' About?' }}</label>
                                    <style>
                                    .ql-toolbar.ql-snow{
                                        border: none;
                                        top: 12px;
                                    }
                                    .ql-container.ql-snow{
                                        color: #abaebf!important;
                                        background: #272735!important;
                                        border-color: #404A51!important;
                                        padding: 10px!important;
                                    }
                                    </style>
                                     <div class="card card-transparent card-block card-stretch event-note mb-0 mt-0">
                                        <div class="card-body px-0 bukmark">
                                            <div class="d-flex align-items-center justify-content-between pb-2 mb-3">
                                                <div class="quill-tool">
                                                </div>
                                            </div>
                                            <div class="card-body rounded ql-dark" id="quill-toolbar1" style="border: none;">
                                                @if($settings->about){!! $settings->about !!}{{--@else{!!'<h3>What is ' . $settings->store_name . ' About?'!!}--}}@endif</p>
                                            </div>
                                        </div>
                                    </div>
                                    <textarea type="text" class="form-control save-target d-none" id="settings-about" data-save-settings="about" data-original="{!! $settings->about !!}" data-new="" data-save="text" rows="3" placeholder="What is {{ $settings->store_name }} About?">{!! $settings->description !!}</textarea>
                                 </div>

                                <div class="form-group col-md-12">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-primary">
                                        Cancel Subscriptions On Exit:
                                            <button type="button" class="btn {{ $settings->cancel_subscriptions_on_exit ? 'btn-info' : 'btn-primary' }} save-target" id="settings-cancel_subscriptions_on_exit" data-save-settings="cancel_subscriptions_on_exit" data-original="{{ $settings->cancel_subscriptions_on_exit }}" data-new="" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-success">
                                        Disable Public Downgrades:
                                            <button type="button" class="btn {{ $settings->disable_public_downgrades ? 'btn-info' : 'btn-primary' }} save-target" id="settings-disable_public_downgrades" data-save-settings="disable_public_downgrades" data-original="{{ $settings->disable_public_downgrades }}" data-new="" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-success d-none">
                                        Refunds Enabled:
                                            <button type="button" class="btn {{ $settings->refunds_enabled ? 'btn-info' : 'btn-primary' }} save-target" id="settings-refunds_enabled" data-save-settings="refunds_enabled" data-original="{{ $settings->refunds_enabled }}" data-new="" data-save="button"><i class="ri-radio-button-fill pr-0 mr-0"></i></button>
                                        </li>
                                        {{-- <li class="list-group-item d-flex justify-content-between align-items-center iq-bg-danger">
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
                                        </li>--}}


                                        
                                        <!-- if yes show other fields -->
                                    </ul>
                                </div>
                                 <div class="form-group col-md-12">
                                    <label for="lname">Terms of Service:</label>
                                    <textarea type="text" class="form-control save-target" rows="2" id="settings-terms_of_service" data-save-settings="terms_of_service" data-save="text" data-original="{!! $settings->terms_of_service !!}" data-new="" placeholder="TODO: will fill in with buttons above clicked and stuff for them">{!! $settings->terms_of_service !!}</textarea>
                                 </div>
                                 
                              </div>
                              
                              {{--<button type="button" class="btn btn-success float-right" id="settings-save-button" style="display:none">Save Changes</button> <!-- moved to top --> --}}
                          
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

/* TODO: after ajax new image
$(document).on('change', '[data-change="src"]', function (e) {
    const value = $(this).val()
    const src = value.data('save-target');
    //if($(this).attr('src') == 'store_image') {
        
        $(this).attr('data-new', new_save_setting);
   // }
})*/
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



@endsection