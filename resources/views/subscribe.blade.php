@extends('layouts.app-zero2')

@section('title', 'Shop')

@section('metadata')
    <meta name="description"
          content="Shop at our discord server. Purchase roles and be an exclusive member."> <!-- server description -->
    <meta name="keywords" content="{{ $guild_id }}"> <!-- server name -->
    <meta name="author" content="BeastlyBot">
@endsection

@section('content')

{{--V1
@if(App\DiscordStore::where('id', '=', $guild_id)->value('owner_id'))
@php

$get_owner_id = App\Shop::where('id', $guild_id)->value('owner_id');
$owner_array = App\User::where('id', $get_owner_id)->get()[0];

$shop_url = App\Shop::where('id', $guild_id)->value('url');

@endphp
@endif
--}}

@if(auth()->user()->getDiscordHelper()->ownsGuild($guild_id))
    @if((!App\DiscordStore::where('guild_id', $guild_id)->get()[0]->live) || $owner_array->error == '2')
        <div class="bg-dark-4 text-white text-center font-size-16 font-weight-500 w-200 mx-auto card m-0 mb-30">
            <a class="card-body p-5 text-white" href="/server/{{ $guild_id }}{{ (!auth()->user()->canAcceptPayments()) ? '#ready' : '' }}">
        <!-- <p>You are viewing this as a <span class="badge badge-primary">Test</span> session <span class="font-weight-100">(only you can see this page)</span>.
            To open this store <a href="/server/{{ $guild_id }}" class="text-white">set your server to <span class="badge badge-success">Live</span> on the dashboard.</a></p>-->
            Store mode: <span class="btn btn-primary btn-sm font-size-14 ml-2">Test</span>
            </a>
        </div>
    @else
    <div class="bg-dark-4 text-white text-center font-size-16 font-weight-500 w-200 mx-auto card m-0 mb-30">
            <a class="card-body p-5 text-white" @if($owner_array->error != '1')href="/server/{{ $guild_id }}" @else href="/dashboard" @endif>
        <!-- <p>You are viewing this as a <span class="badge badge-primary">Test</span> session <span class="font-weight-100">(only you can see this page)</span>.
            To open this store <a href="/server/{{ $guild_id }}" class="text-white">set your server to <span class="badge badge-success">Live</span> on the dashboard.</a></p>-->
            Store mode: <span class="btn btn-success btn-sm font-size-14 ml-2">@if($owner_array->error != '1')Live @else Error @endif</span>
            </a>
        </div>
    @endif
@else
    <div href="/account/subscriptions" class="bg-dark-4 text-white text-center font-size-16 font-weight-500 w-200 mx-auto card m-0 mb-30">
        <div class="card-body p-5 text-white">
       <!-- <p>You are viewing this as a <span class="badge badge-primary">Test</span> session <span class="font-weight-100">(only you can see this page)</span>.
        To open this store <a href="/server/{{ $guild_id }}" class="text-white">set your server to <span class="badge badge-success">Live</span> on the dashboard.</a></p>-->
            <a href="/account/subscriptions" class="btn btn-dark btn-sm font-size-14 ml-2">My Subscriptions</a>
        </div>
    </div>
@endif

    <div class="h-250 draw-grad-up">
        <div class="text-center blue-grey-800 m-0 mt-50">
            <a class="avatar avatar-xxl" href="javascript:void(0)">
                <img id="guild_icon"
                     src="{{ asset('block-grey.png') }}"
                     alt="...">
            </a>
            <div class="font-size-50 mb-5 blue-grey-100" id="guild_name">Shop Offline</div>
            <ul class="list-inline font-size-14 text-center">
                <li class="list-inline-item blue-grey-100 text-center">
                    <i class="icon wb-heart mr-5" aria-hidden="true"></i> <span id="guild_member_count">...</span>
                    Members
                </li>
            </ul>
            <span><button type="button" class="btn btn-sm btn-round btn-dark btn-icon mb-10" id="btn_copy-url" data-toggle="tooltip" data-original-title="Copy Link" data-placement="right"><i class="wb-link"></i></button></span>
        </div>
    </div>
    <div class="container">

        <div class="row">
            <div class="col-xl-2 col-lg-1 col-md-12 order-1 order-lg-2">
                <div class="d-flex justify-content-center pt-40 pt-sm-60">
                    <div class="xx banner-image">
                        <div class="xx-head"></div>
                        <div class="xx-body"></div>
                        <div class="xx-hand"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12 order-2 order-lg-1">

                <div class="row pt-30">
                    <div class="col-md-12">
                        <div class="container">

                            <div class="panel-body p-0">
                                <div class="panel-group" id="accordian_main" aria-multiselectable="true" role="tablist">
                                </div>
                                {{--<table class="table">
                                    <tbody id="roles-table"></tbody>
                                </table>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--
@if(auth()->user()->getDiscordHelper()->ownsGuild($guild_id))
    @if(auth()->user()->plan_sub_id !== null)
        @include('partials/clear_script')
    @endif
@endif
--}}
<input readonly type="text" value="https://beastly.store/{{ App\DiscordStore::where('guild_id', $guild_id)->value('url') }}" id="input_copy-url" style="opacity:0">

@endsection

@section('scripts')


@if(((App\DiscordStore::where('guild_id', $guild_id)->get()[0]->live) && ($owner_array->canAcceptPayments())) || (auth()->user()->getDiscordHelper()->ownsGuild($guild_id)))
@if($owner_array->error != ('1' || '2'))
    <script type="text/javascript">
        var role_descs = JSON.parse('{!! json_encode($descriptions) !!}');

        $(document).ready(function () {
        //$(window).on('load', function() { // speeds it up but breaks special
            socket.emit('is_user_banned', [socket_id, '{{ $guild_id }}', '{{ auth()->user()->DiscordOAuth->discord_id }}']);

            socket.on('res_user_banned_' + socket_id, function(msg) {
                if(msg) {
                    Swal.fire({
                        title: 'You are banned!',
                        text: 'It looks like you are banned from this server... Sorry.',
                        type: 'warning',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowEscapeKey : false,
                        allowOutsideClick: false
                    });
                }
            });

            socket.emit('get_guild_data', [socket_id, '{{ $guild_id }}']);
            socket.emit('get_roles', [socket_id, '{{ $guild_id }}']);
            var guild_id = '{{ $guild_id }}';

            socket.on('res_guild_data_' + socket_id, function (message) {
                var iconURL = message['iconURL'];
                $('#guild_icon').attr('src', iconURL);
                $('#guild_name').text(message['name']);
                $('#guild_member_count').text(message['memberCount']);
            });

            socket.on('res_roles_' + socket_id, function (message) {
                Object.keys(message).forEach(function (key) {
                    var found_desc = false;
                    jQuery.each(role_descs, function (i, val) {
                        if (val.role_id == key) {
                            found_desc = true;
                            $('#accordian_main').append(getHTML2(guild_id, key, message[key]['color'], message[key]['name'], val.description));
                        }
                    });
                    if (!found_desc)
                        $('#accordian_main').append(getHTML2(guild_id, key, message[key]['color'], message[key]['name'], null));

                    socket.emit('get_role_for_sale', [socket_id, guild_id, key]);
                });

                    $.ajax({
                            url: '/get-status-roles',
                            type: 'POST',
                            data: {
                                'roles': message,
                                //'guild_id': guild_id,
                                _token: '{{ csrf_token() }}'
                            },
                    }).done(function (msg) {
                        console.log(msg)

                        Object.keys(msg).forEach(function (role) {
                            role_id = msg[role]['product'];
                            if(msg[role]['active']){
                                $('#role-' + role_id).attr('hidden', false);
                                $('#role-' + role_id).css('visibility', 'visible');
                                $('#role-' + role_id).removeClass('role-disabled');
                            }else{
                                $('#role-' + role_id).remove();
                            }

                        })

                    })
                    $(window).on('load', function() {

                        $.ajax({
                            url: '/get-special-roles',
                            type: 'POST',
                            data: {
                                'roles': message,
                                'guild_id': '{{ $guild_id }}',
                                _token: '{{ csrf_token() }}'
                            },
                        }).done(function (msg) {
                            //var role = msg['role']['plan']['product'];
                            console.log(msg);

                            Object.keys(msg).forEach(function (role) {
                                special_id = msg[role]['plan']['id'];
                                product_name = msg[role]['plan']['amount'];
                                product_nickname = msg[role]['plan']['nickname'];
                                var guild_id = special_id.split('_')[0];
                                var role_id = special_id.split('_')[1];
                                var role_name = product_nickname.split('-')[0];
                                //var link = `/slide-special-purchase/${guild_id}/${role_id}/${special_id}/{{ auth()->user()->DiscordOAuth->discord_id }}`;
                                var link = `/slide-special-purchase/${guild_id}/${role_id}/${special_id}/{{ auth()->user()->getDiscordHelper()->discord_id() }}`;
                                special = `
                                <div class="panel" id="role-${role_id}">
                                    <div class="panel-heading p-20 d-flex flex-row flex-wrap align-items-center justify-content-between" id="heading_${guild_id}" role="tab">
                                        <div class="w-100 hidden-sm-down">
                                        </div>
                                        <div class="text-center">
                                            <a data-toggle="collapse" href="#tab_${role_id}" data-parent="#accordian_main" aria-expanded="true" aria-controls="tab_${role_id}">
                                                <div class="badge badge-primary badge-lg font-size-18 text-white" style="background-color:}"><i class="fab icon-discord mr-2"></i> <span>${role_name} ({{ auth()->user()->getDiscordHelper()->getUsername() }})</span></div>
                                            </a>
                                        </div>
                                        <div class="w-100 hidden-sm-down">
                                            <button data-url="${link}" data-toggle="slidePanel" type="button"
                                            class="btn btn-sm btn-success float-right">Select <i class="icon wb-arrow-right ml-2" ></i>
                                            </button>
                                        </div>
                                        <div class="w-20 hidden-md-up">
                                            <button class="btn btn-success p-1" data-url="${link}" data-toggle="slidePanel">
                                                <i class="icon wb-arrow-right" ></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                `
                                $('#accordian_main').prepend(special);


                            })

                        })

                    });
                });

        function getHTML2(guild_id, role_id, role_color, role_name, role_desc) {
            var link = `/slide-product-purchase/${guild_id}/${role_id}`;
            @if(isset($affiliate))
                link += '?affiliate_id={{ $affiliate->id }}';
            @endif

            if (role_desc !== null) return `
                <div class="panel role-disabled" style="visibility: hidden;" id="role-${role_id}" hidden>
                    <div class="panel-heading p-20 d-flex flex-row flex-wrap align-items-center justify-content-between" id="heading_${guild_id}" role="tab">
                        <div class="w-100 hidden-sm-down">
                            <a class="panel-title" data-toggle="collapse" href="#tab_${role_id}" data-parent="#accordian_main" aria-expanded="false" aria-controls="tab_${role_id}">
                            </a>

                        </div>
                        <div class="text-center">

                            <a data-toggle="collapse" href="#tab_${role_id}" data-parent="#accordian_main" aria-expanded="true" aria-controls="tab_${role_id}">
                                <div class="badge badge-primary badge-lg font-size-18 text-white" style="background-color: ${role_color}"><i class="icon-discord mr-2"></i> <span>${role_name}</span></div>
                            </a>
                        </div>
                        <div class="w-100 hidden-sm-down">
                            <button data-url="${link}" data-toggle="slidePanel" type="button"
                            class="btn btn-sm btn-success float-right">Select <i class="icon wb-arrow-right ml-2" ></i>
                            </button>
                        </div>
                        <div class="w-20 hidden-md-up">
                            <button class="btn btn-success p-1" data-url="${link}" data-toggle="slidePanel">
                                <i class="icon wb-arrow-right" ></i>
                            </button>
                        </div>

                    </div>
                    <div class="panel-collapse collapse" id="tab_${role_id}" aria-labelledby="heading_${guild_id}" role="tabpanel">
                        <div class="panel-body">
                            ${role_desc}
                        </div>
                    </div>
                    `;
            else return `
                <div class="panel role-disabled" style="visibility: hidden;" id="role-${role_id}" hidden>
                    <div class="panel-heading p-20 d-flex flex-row flex-wrap align-items-center justify-content-between" id="heading_${guild_id}" role="tab">
                        <div class="w-100 hidden-sm-down">
                        </div>
                        <div class="text-center">
                            <a data-toggle="collapse" href="#tab_${role_id}" data-parent="#accordian_main" aria-expanded="true" aria-controls="tab_${role_id}">
                                <div class="badge badge-primary badge-lg font-size-18 text-white" style="background-color: ${role_color}"><i class="fab icon-discord mr-2"></i> <span>${role_name}</span></div>
                            </a>
                        </div>
                        <div class="w-100 hidden-sm-down">
                            <button data-url="${link}" data-toggle="slidePanel" type="button"
                            class="btn btn-sm btn-success float-right">Select <i class="icon wb-arrow-right ml-2" ></i>
                            </button>
                        </div>
                        <div class="w-20 hidden-md-up">
                            <button class="btn btn-success p-1" data-url="${link}" data-toggle="slidePanel">
                                <i class="icon wb-arrow-right" ></i>
                            </button>
                        </div>
                    </div>
                    `;
        }
    });
    </script>
@endif
@endif

<script>
$(function() {
   $('#btn_copy-url').click(function() {
     $('#input_copy-url').focus();
     $('#input_copy-url').select();
     document.execCommand('copy');
     $('#btn_copy-url').attr('data-original-title', 'Copied!').addClass('btn-primary').removeClass('btn-dark');
     $('html .tooltip-inner').text('Copied!')
    setTimeout(function(){
        $('#btn_copy-url').attr('data-original-title', 'Copy Link').addClass('btn-dark').removeClass('btn-primary');
        $('html .tooltip-inner').text('Copy Link')
    }, 1000);
   });
});
</script>
{{--
<script>

        $(document).load(function () {
            socket.emit('get_roles', [socket_id, '{{ $guild_id }}']);
            var guild_id = '{{ $guild_id }}';

            socket.on('res_roles_' + socket_id, function (message) {

                    $.ajax({
                            url: '/get-special-roles',
                            type: 'POST',
                            data: {
                                'roles': message,
                                'guild_id': '{{ $guild_id }}',
                                _token: '{{ csrf_token() }}'
                            },
                    }).done(function (msg) {
                        console.log(msg)

                       /* Object.keys(msg).forEach(function (role) {
                            role_id = msg[role]['product'];
                            if(msg[role]['active']){
                                $('#role-' + role_id).attr('hidden', false);
                                $('#role-' + role_id).css('visibility', 'visible');
                                $('#role-' + role_id).removeClass('role-disabled');
                            }else{
                                $('#role-' + role_id).remove();
                            }

                        })*/

                    })
                })
        });

</script> --}}
@endsection
