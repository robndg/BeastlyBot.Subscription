<header class="slidePanel-header dual bg-blue-500">
    <div class="slidePanel-actions" aria-label="actions" role="group">
    @if($special)
    <button type="button" class="btn btn-icon btn-pure btn-inverse actions-top icon wb-chevron-right"
        aria-hidden="true" data-url="/slide-server-member/{{ $guild_id }}/{{ $useruser()->DiscordOAuth->discord_id }}" id="back-btn" data-toggle="slidePanel"></button>
      @else
    <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
        aria-hidden="true"></button>
    @endif
    </div>
    <h1>Product Settings @if($special)<span class="text-bold">| {{ $user->getDiscordUsername() }}</span>@endif</h1>
    <p><span id="role_name"></span></p>
</header>

<div class="site-sidebar-tab-content put-long tab-content" id="slider-div">
    <div class="tab-pane fade active show" id="sidebar-userlist">
        <div>

           {{-- <div class="row">
                <div class="col-12">
                    <div class="py-20 d-flex flex-row flex-wrap align-items-center justify-content-between" 
                    data-step="2" data-intro="Enable the role" data-position='bottom'>
                        <h5>Enable Role on Shop</h5>
                        <div id="toggle-switch">
                            <input type="checkbox" class="js-switch" @if($enabled) checked @endif/>
                            <!--<label><h5 class="pl-20">Enable Role on Shop</h5></label>-->
                        </div>
                    </div>
                </div>
            </div> --}}


           <div>
                <div class="row">
                    <div class="col-12">
                    <h5>Subscription Prices</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="row no-space text-center">
                                @for($i = 0; $i < 13; $i++)
                                @if($i === 1 || $i === 3 || $i === 6 || $i === 12)
                                <div class="col-6 col-sm-3">
                                    <div class="card border-0 vertical-align h-100">
                                    <div class="vertical-align-middle font-size-16">
                                        <div class="d-block">
                                            <div class="input-group w-120 mx-auto">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input id="price_{{ $i }}m" type="text" class="form-control"
                                                    placeholder="0.00"
                                                    value="{{ $prices[$i] }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <i class="wb-triangle-down font-size-24 mb-10 blue-600"></i>
                                        <div>
                                        <span class="font-size-12">{{ $i }} month</span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endif
                                @endfor

                            
                                <div class="col-12">
                                    <button id="prices_btn" type="button" class="btn btn-dark btn-lg btn-block @if(!$enabled) disabled @endif"
                                        onclick="updatePrices()" @if(!$enabled) disabled @endif>Update Prices
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div>
                <div class="row pb-30">
                    <div class="col-12">
                        <h5>Role Description</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="row no-space text-center">
                                    <div class="col-12">
                                        @if(RoleDesc::where('guild_id', $guild_id)->where('role_id', $role_id)->exists())
                                            <textarea id='product-description' class="lit-group-item form-control" placeholder="These awesome perks..."
                                                    @if(!$enabled) disabled
                                                    @endif>{{ RoleDesc::where('guild_id', $guild_id)->where('role_id', $role_id)->get()[0]->description }}</textarea>
                                        @else
                                            <textarea id='product-description' class="lit-group-item form-control" placeholder="These awesome perks.."
                                                    @if(!$enabled) disabled @endif></textarea>
                                        @endif
                                        <button type="button" class="btn btn-block mt-10 btn-dark btn-lg @if(!$enabled) disabled @endif" id="desc-btn"
                                            @if(!$enabled) disabled @else onClick="updateProductDesc();" @endif>Update Description
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
           <div>
                <div class="row pb-30">
                    <div class="col-12">
                        <a href="/shop/{{ $shop_url }}" class="btn float-right btn-primary d-none" id="btn_visit-shop">Visit shop</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
$('textarea#product-description').on('keyup', function(){
  $(this).val($(this).val().replace(/[\r\n\v]+/g, ' '));
  $(this).attr('maxlength','150');
});
</script> 

<script type="text/javascript">
    var guild_id, role_id, guild_name, role_name;
    //var elem = document.querySelector('.js-switch');
    //var switchery = new Switchery(elem);
    var clicked = false;

    $(document).ready(function () {

        socket.emit('get_role_data', [socket_id, '{{ $guild_id }}', '{{ $role_id }}']);

        socket.on('res_role_data_' + socket_id, function (message) {
            $('#role_name').text(message['name']);
            guild_id = message['guild_id'];
            role_id = message['id'];
            guild_name = message['guild_name'];
            role_name = message['name'];

            $('#form_guild_name').val(guild_name);
            $('#form_role_name').val(role_name);
        });
        /*
        $('#toggle-switch').on('change', function (e) {
            e.preventDefault();
            if (!clicked) {
                switchery.disable();
                $.ajax({
                    url: '/toggle-role',
                    type: 'POST',
                    data: {
                        'guild_id': '{{ $guild_id }}',
                        'role_id': '{{ $role_id }}',
                        'guild_name': guild_name,
                        'role_name': role_name,
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                    switchery.enable();
                    console.log(msg);
                    if (!msg['success']) {
                        $('.js-switch').click();
                        Swal.fire({
                            title: 'Failure',
                            text: msg['msg'],
                            type: 'warning',
                            showCancelButton: false,
                            showConfirmButton: true,
                            target: document.getElementById('slider-div')
                        });
                    } else {
                        switchery.enable();
                        if (msg['active']) {
                            $('#' + guild_id + '_' + role_id).removeClass('hidden');
                            $('#prices_btn').removeClass('disabled');
                            $('#prices_btn').attr('disabled', false);
                            $('#desc_btn').attr('disabled', false);
                            $('#desc_btn').removeClass('disabled');
                            $('.price-inputs').attr('disabled', false);
                            $('.price-inputs').removeClass('disabled');
                            $('#status_' + guild_id + '_' + role_id).find('i').removeClass('yellow-500');
                            $('#status_' + guild_id + '_' + role_id).find('i').addClass('green-500');
                            $('#status_' + guild_id + '_' + role_id).find('span').text('Active');
                        } else {
                            $('#' + guild_id + '_' + role_id).addClass('hidden');
                            $('#prices_btn').addClass('disabled');
                            $('#prices_btn').attr('disabled', true);
                            $('#desc_btn').attr('disabled', true);
                            $('#desc_btn').addClass('disabled');
                            $('.price-inputs').attr('disabled', true);
                            $('.price-inputs').addClass('disabled');
                            $('#status_' + guild_id + '_' + role_id).find('i').removeClass('green-500');
                            $('#status_' + guild_id + '_' + role_id).find('i').addClass('yellow-500');
                            $('#status_' + guild_id + '_' + role_id).find('span').text('Inactive');
                        }

                        $('#prices_btn').attr('disabled', !msg['active']);
                        $('#desc_btn').attr('disabled', !msg['active']);
                        $('#product-description').attr('disabled', !msg['active']);
                        $('.price-inputs').attr('disabled', !msg['active']);

                    }
                    clicked = false;
                });
            }
            clicked = true;
        }); */
    });

    function updateProductDesc() {
        $.ajax({
            url: '/update_product_desc',
            type: 'POST',
            data: {
                'guild_id': '{{ $guild_id }}',
                'role_id': '{{ $role_id }}',
                'description': $('#product-description').val(),
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg) {
            Toast.fire({
                title: 'Success',
                text: 'Product updated!',
                type: 'success',
                showCancelButton: false,
                //showConfirmButton: true,
               // target: document.getElementById('slider-div')
            });
            $('#btn_visit-shop').removeClass('d-none');
        });
    }

    // TODO: For now we close the slide but we need to turn off the switcheries
    function updatePrices() {
        Toast.fire({
            title: 'Processing....',
            text: '',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: () => !Toast.isLoading(),
            //target: document.getElementById('slider-div')
        });
        Toast.showLoading();
        $.ajax({
            url: '/update_discord_prices',
            type: 'POST',
            data: {
                'price_1_month': $('#price_1m').val(),
                'price_3_month': $('#price_3m').val(),
                'price_6_month': $('#price_6m').val(),
                'price_12_month': $('#price_12m').val(),
                'role_id': role_id,
                'role_name': role_name,
                'guild_id': guild_id,
                _token: '{{ csrf_token() }}'
            },
        }).done(function (msg, enabled) {
            if (msg['success']) {
                Toast.fire({
                    title: 'Success!',
                    text: msg['msg'],
                    type: 'success',
                });
                $('#' + guild_id + '_' + role_id).removeClass('inactive-role').addClass('active-role');
                $('#toggle-product-icon_' + guild_id + '_' + role_id).removeClass('wb-plus').addClass('wb-check');
                $('#toggle-check_' + guild_id + '_' + role_id).addClass('green-600').removeClass('grey-2');
                $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled btn-dark').addClass('btn-primary').attr("disabled", false);
                $('#toggle-product_' + guild_id + '_' + role_id).addClass('active');
                $('#product-settings_' + guild_id + '_' + role_id).removeClass('disabled').attr("disabled", false);
            } else {
                Toast.fire({
                    title: 'Success',
                    text: msg['msg'],
                    type: 'warning',
                });
                $('#' + guild_id + '_' + role_id).addClass('inactive-role').removeClass('active-role');
                $('#toggle-product-icon_' + guild_id + '_' + role_id).addClass('wb-plus').removeClass('wb-check');
                $('#toggle-check_' + guild_id + '_' + role_id).addClass('grey-2').removeClass('green-600');
                $('#product-settings_' + guild_id + '_' + role_id).addClass('disabled btn-dark').removeClass('btn-primary').attr("disabled", true);
                $('#toggle-product_' + guild_id + '_' + role_id).removeClass('active');
                $('.text_save-roles').removeClass('d-none').text('Disabled');
            }
            setTimeout(function(){
                //$('#icon_save-roles').removeClass("wb-check").addClass("wb-minus");
                $('.text_save-roles').addClass('d-none').text();
            }, 2000);
  
            $('#btn_visit-shop').removeClass('d-none');
        });
    }
</script>

<script type="text/javascript">
      if (RegExp('multipage', 'gi').test(window.location.search)) {
        introJs().goToStepNumber(2).start();
      }
</script>

@include('partials/clear_script')