@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')


<div class="page-aside">

<div class="page-aside-switch">
  <i class="icon wb-chevron-left" aria-hidden="true"></i>
  <i class="icon wb-chevron-right" aria-hidden="true"></i>
</div>

<div class="page-aside-inner page-aside-scroll">
  <div data-role="container">
    <div data-role="content">
    @if(auth()->user()->StripeConnect->express_id != null && auth()->user()->error != '1')
    <div class="card card-shadow card-inverse mb-0 bg-grey-3 white">
      <div class="card-block p-20">
        <div class="counter counter-lg counter-inverse text-left">
          <div class="counter-label mb-20">
            <div>PAY OUT <i class="icon wb-info-circle ml-1 text-white" aria-hidden="true"
              data-plugin="webuiPopover"
              data-content="&lt;p&gt;Funds successfully paid out and on the way to your bank account.&lt;/p&gt;" data-trigger="hover"
              data-animation="pop"></i></div>
          </div>
          <div class="counter-number-group mb-5">
            <span class="counter-number-related">$</span>
            <span class="counter-number">{{ number_format(($balance->available[0]->amount)/100, 2, '.', ',') }}</span>
            <button type="button" class="btn btn-primary btn-sm btn-link float-right" data-toggle="site-sidebar" data-url="/slide-payout/{{ auth()->user()->stripe_express_id }}">View Pending</button>
          </div>
        </div>
      </div>
    </div>
    @endif
    <div class="card">
              <div class="card-header">
              <span id="notification_count_1-pg">0</span> New Notifications
              </div> 
              <div class="card-block p-0">
                <ul class="list-group list-group-full list-group-dividered list-group-no-hover mb-0" id="notifications-dropdown">




                  {{-- <li class="list-group-item px-5">
                    <div class="d-flex align-items-start">
                      <div class="pl-2 pr-10">
                        <a class="avatar avatar-lg" href="javascript:void(0)">
                          <img class="img-fluid" src="https://cdn.discordapp.com/icons/608894397328785440/9cd0dbd96c17009815b7a0f90ac05a33.jpg" alt="...">
                        </a>
                      </div>
                      <div class="media-body">
                        <h5 class="mt-5 mb-5">Other Servers</h5>
                        <small>123 Members</small>
                      </div>
                      <div class="pl-5">
                        <button type="button" class="btn btn-primary btn-outline mt-5">Invite</button>
                      </div>
                    </div>
                  </li> --}}

                </ul>
              </div>
            </div>

    </div>
  </div>
</div>
<!---page-aside-inner-->
</div>

<div class="page-main pb-100">
<div class="container-fluid pt-20 px-lg-30">

      <!-- start big row -->
      <div class="row">
        <div class="col-12">
          <h4 class="font-weight-100">Dashboard</h4>
        </div>
        <div class="col-12 order-2 order-md-1">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="row no-space">

                    <div class="col-lg col-xs-9 col-sm-9 col-md-10 px-0">
                        <div class="card-block h-150 bg-grey-2 overlay">
                          <div class="overlay-panel vertical-align">
                            <div class="vertical-align-middle prof">
                              <a class="avatar avatar-100 float-left prof-20 mr-20" href="javascript:void(0)">
                                <img src="{{ auth()->user()->getDiscordHelper()->getAvatar() }}" alt="">
                              </a>

                              <div class="float-left text-white">
                                <div class="font-size-20 mt-20">{{ auth()->user()->getDiscordHelper()->getUsername() }}</div>
                                  <p class="mb-20 text-nowrap">
                                    <span class="text-break">{{ auth()->user()->getDiscordHelper()->getEmail() }}</span>
                                  </p>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-1 col-xs-3 col-sm-3 col-md-2">

                        <div class="row no-space">
                            {{-- <div class="@if(auth()->user()->StripeConnect->express_id != null != null && auth()->user()->error != '1')col-6 col-sm-12 @else col-12 h-only-xs-50 @endif">
                              <a href="javascript:void(0);" class="btn-75 @if(auth()->user()->StripeConnect->express_id != null && auth()->user()->error != '1')pt-20 @else h-150 pt-10 pt-sm-60 @endif bd-top-sm" data-toggle="site-sidebar" data-url="/slide-account-settings"> --}}
                              <div class="col-12 h-only-xs-50">
                              <a href="javascript:void(0);" class="btn-75 h-150 pt-10 pt-sm-60 bd-top-sm" data-toggle="site-sidebar" data-url="/slide-account-settings">
                                <i class="wb-user"></i>
                              </a>
                            </div>
                           {{-- @if(auth()->user()->StripeConnect->express_id != null && auth()->user()->error != '1')
                            <div class="col-6 col-sm-12">
                              <a href="javascript:void(0);" class="btn-75 pt-25 bd-top" data-toggle="site-sidebar" data-url="/slide-payout/{{ auth()->user()->StripeConnect->express_id }}">
                                  <i class="icon-stripe"></i>
                              </a>
                            </div>
                            @endif --}}

                          </div>

                    </div>

                      <div class="col-12 col-md-9 col-lg-5 col-xl-3">
                        <div class="card-block h-150 h-only-xs-100 h-only-sm-100 h-only-md-100 bg-grey-3 draw-grad-up">
                          <div class="slider pt-0 mx-30" id="subscriptionsSlider">

                            @if(auth()->user()->canAcceptPayments())
                            <div>
                              <div class="text-center mt-lg-40" onclick="window.location.href = '/account/settings';">
                                <span class="badge badge-success badge-lg font-size-20">Live</span>
                                <p class="font-weight-100">Active {{ gmdate("m-d-Y", auth()->user()->getPlanExpiration()) }}</p>
                              </div>
                            </div>
                            @endif

                          </div>
                        </div>
                      </div>
                      <!--<div class="col-md-4 hidden-md-down">
                        <div class="card-block h-150 h-only-xs-100 bg-grey-2">
                          <div class="mt-40 w-150 mx-auto">
                            <div class="d-flex justify-content-around">
                              <div>
                                <a href="#" class="btn btn-icon btn-dark btn-round"><i class="icon wb-settings"></i></a>
                              </div>
                              <div>
                                <a href="#" class="btn btn-dark">Payout</a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>-->

                    </div>

                </div> <!-- end card -->


              </div> <!-- end col -->

          </div> <!-- end row -->



        </div> <!-- end first column -->

        <div class="col-12 order-1 order-md-2"> <!-- begin next column -->

        <div class="row">
            <!--<div class="col-12 hidden-md-down visible-xs-down">
              <p class="mb-2 font-weight-100">Account</p>
            </div>-->
            <div class="col-12 col-sm-6">
                <a href="/account/subscriptions" class="card card-block card-hover mb-25" id="tour_subscriptions-block">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-100 h-only-xs-100 h-only-sm-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-15"><i class="icon wb-star text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Subscriptions</span>
                      </div>
                    </div>
              </a>
            </div>

            <!--<div class="col-6 col-sm-3">
                <a href="javascript:void(0);" class="card card-block card-hover mb-25">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-100 h-only-xs-100 h-only-sm-100" data-toggle="slidePanel" data-url="/slide-account-payments">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-15"><i class="icon wb-order text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Payments</span>
                      </div>
                    </div>
                </a>
            </div>-->



            <!--<div class="col-6 col-md-4 col-lg-3 col-xxl-3">
                <a href="/account/settings" class="card card-block btn btn-primary">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-150 h-only-xs-100 h-only-sm-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-5"><i class="icon wb-user-circle text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Settings</span>
                      </div>
                    </div>
                </a>
            </div>-->

          @if(auth()->user()->StripeConnect->express_id != null)

            @if(auth()->user()->error == "1")


            <div class="col-12 col-sm-6">
                <a href="javascript:void(0);" class="card card-block pulse card-hover"
                    id="btn_start-shop-block">
                    <i class="icon wb-info-circle l-up text-white" aria-hidden="true"
                      data-plugin="webuiPopover"
                      data-content="&lt;p&gt;Once connected you must re-enable all active Products and reset all prices. Your Live account is still active for the term. Please contact support at with any other concerns (team@beastly.app).&lt;/p&gt;" data-trigger="hover"
                      data-animation="pop"></i>
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-5"><i class="icon-shop text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Stripe Payment Failure</span>
                        <p class="font-size-12 text-white">Please connect a US Stripe account.</p>
                      </div>
                    </div>
                </a>
                <a href="{{ 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=' . SiteConfig::get('APP_URL') . '&client_id=' . SiteConfig::get('STRIPE_CLIENT_ID') }}" class="d-none card card-block btn btn-primary bg-blue-600 ladda-button"
                    id="btn_connect-stripe-block" data-style="slide-up" data-plugin="ladda">
                    <i class="icon wb-info-circle l-up text-white" aria-hidden="true"
                      data-plugin="webuiPopover"
                      data-content="&lt;p&gt;@lang('lang.connect_stripe') (MUST BE USA ACCOUNT)&lt;/p&gt;" data-trigger="hover"
                      data-animation="pop"></i>
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-100">
                      <div class="vertical-align-middle ladda-label">
                        <div class="counter-icon mb-5"><i class="icon-stripe text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Connect Stripe</span>
                      </div>
                      <span class="ladda-spinner"></span>
                    </div>
                </a>
            </div>

            <script type="text/javascript">
                function changeBtn() {
                    $('#btn_connect-stripe-block').removeClass('d-none');
                    $('#btn_start-shop-block').addClass('d-none');
                }
                window.onload = function () {
                    document.getElementById("btn_start-shop-block").addEventListener('click', changeBtn);
                }
            </script>


            @else

           <!--<div class="col-12 hidden-md-down visible-xs-down">
              <p class="mb-1 font-weight-100">Shop</p>
            </div>-->
            <div class="col-6 col-sm-3">
                <a href="javascript:void(0);" class="card card-block card-hover" id="servers-block" data-toggle="slidePanel" data-url="/slide-servers">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-100 h-only-xs-100 h-only-sm-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-15"><i class="icon icon-shop text-white font-size-50 mb--10" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Shops</span>
                      </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-sm-3">
                <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="slidePanel" data-url="/slide-promotions">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-100 h-only-xs-100 h-only-sm-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-15"><i class="icon icon-gift1 text-white font-size-50 mb--10" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Promotions</span>
                      </div>
                    </div>
                </a>
            </div>

            <!--<div class="col-6 col-lg-3 col-xxl-3">
                <a href="javascript:void(0);" class="card card-block btn btn-primary" data-toggle="slidePanel" data-url="slide-payout">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-md-150 h-only-xs-100 h-only-sm-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-5"><i class="icon icon-right-big text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Payout</span>
                      </div>
                    </div>
                </a>
            </div>-->

            @endif


          @else

            <div class="col-12 col-sm-6">
                <a href="javascript:void(0);" class="card card-block card-hover"
                    id="btn_start-shop-block">
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-100">
                      <div class="vertical-align-middle">
                        <div class="counter-icon mb-5"><i class="icon-shop text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Create Shop</span>
                      </div>
                    </div>
                </a>
                <a href="{{ 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=' . SiteConfig::get('APP_URL') . '&client_id=' . SiteConfig::get('STRIPE_CLIENT_ID') }}" class="d-none card card-block btn btn-primary bg-blue-600 ladda-button"
                    id="btn_connect-stripe-block" data-style="slide-up" data-plugin="ladda">
                    <i class="icon wb-info-circle l-up text-white" aria-hidden="true"
                      data-plugin="webuiPopover"
                      data-content="&lt;p&gt;@lang('lang.connect_stripe')&lt;/p&gt;" data-trigger="hover"
                      data-animation="pop"></i>
                    <div class="counter counter-lg counter-inverse blue-grey-100 vertical-align h-100">
                      <div class="vertical-align-middle ladda-label">
                        <div class="counter-icon mb-5"><i class="icon-stripe text-white" aria-hidden="true"></i></div>
                        <span class="counter-number text-white">Connect Stripe</span>
                      </div>
                      <span class="ladda-spinner"></span>
                    </div>
                </a>
            </div>

            <script type="text/javascript">
                function changeBtn() {
                    $('#btn_connect-stripe-block').removeClass('d-none');
                    $('#btn_start-shop-block').addClass('d-none');
                }
                window.onload = function () {
                    document.getElementById("btn_start-shop-block").addEventListener('click', changeBtn);
                }
            </script>

          @endif

          </div>


        </div>

      </div><!-- end row -->

    <div class="row">
      <div class="col-12 mt-lg-30">
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-12 mt-lg-15">
        <h4 class="font-weight-100">Need help?</h4>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
          <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="site-sidebar" data-url="slide-help-managing-subscriptions">
              <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align h-only-xs-50 h-only-sm-50 h-md-50">
                <div class="vertical-align-middle">
                  <span class="counter-number">Managing Subscriptions</span>
                </div>
              </div>
          </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
          <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="site-sidebar" data-url="slide-help-ultimate-shop-guide">
              <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align h-only-xs-50 h-only-sm-50 h-md-50">
                <div class="vertical-align-middle">
                  <span class="counter-number">Ultimate Shop Guide</span>
                </div>
              </div>
          </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
          <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="site-sidebar" data-url="slide-help-withdraw-earnings">
              <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align h-only-xs-50 h-only-sm-50 h-md-50">
                <div class="vertical-align-middle">
                  <span class="counter-number">Withdrawing Earnings</span>
                </div>
              </div>
          </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
          <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="site-sidebar" data-url="slide-help-creating-a-promotion">
              <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align h-only-xs-50 h-only-sm-50 h-md-50">
                <div class="vertical-align-middle">
                  <span class="counter-number">Creating a Promotion</span>
                </div>
              </div>
          </a>
      </div>
      <div class="col-12">
          <a href="javascript:void(0);" class="card card-block card-hover" data-toggle="site-sidebar" data-url="slide-help-titles">
              <div class="counter counter-lg counter-inverse blue-grey-300 vertical-align h-only-xs-50 h-only-sm-50 h-md-100">
                <div class="vertical-align-middle">
                  <div class="counter-icon mb-5 hidden-md-down"><i class="icon wb-plugin blue-grey-400" aria-hidden="true"></i></div>
                  <span class="counter-number">More Help</span>
                </div>
              </div>
          </a>
      </div>
    </div>
</div>


<div class="modal notification" id="notifications_modal">
  <div class="modal-dialog modal-center">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h3 class="modal-title"><span class="badge badge-info" id="noti_type"></span></h3>
      </div>
      <div class="modal-body text-center">
        <h5 class="font-size-16 font-weight-100" id="noti_message"></h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline btn-success" data-dismiss="modal"><i class="wb wb-check"></i></button>
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')


<script type="text/javascript">
        var subscriptions = JSON.parse('{!! json_encode($subscriptions) !!}');
        var finished = [];

        $(document).ready(function () {
            var discord_username, discord_discriminator;
            socket.on('connect', function () {
                socket.emit('get_user_data', [socket_id, '{{ auth()->user()->DiscordOAuth->discord_id }}']);
            });
            socket.on('res_user_data_' + socket_id, function (message) {
                $('#avatarIconHeader').attr('src', message['avatar']);
                discord_username = message['name'];
                discord_discriminator = message['discriminator'];
                $('#discord_username').text(discord_username + " #" + discord_discriminator);
            });

            @foreach($subscriptions as $subscription)
                var data = '{{ $subscription['items']['data'][0]['plan']['id'] }}';
                var guild_id = data.split('_')[0];
                var role_id = data.split('_')[1];
                socket.emit('get_role_data', [socket_id, guild_id, role_id]);
            @endforeach

            socket.on('res_role_data_' + socket_id, function(message) {
                jQuery.each(subscriptions, function(i, val) {
                    var id = val.items.data[0].plan.id;
                    var guild_id = id.split('_')[0];
                    var role_id = id.split('_')[1];
                    if(guild_id === message['guild_id'] && role_id === message['id'] && !finished.includes(val.id)) {
                        finished.push(val.id);
                        var dateObj = new Date(val.current_period_end * 1000);
                        var month = dateObj.getUTCMonth() + 1; //months from 1-12
                        var day = dateObj.getUTCDate();
                        var year = dateObj.getUTCFullYear();
                        var newdate = month + "/" + day + "/" + year;

                        $('#subscriptionsSlider').append(getHTMLSubs(i, message['guild_name'], message['name'], message['color'], message['guild_id'], toTitleCase(val.status), newdate));
                    }
                });
            });
        });

        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function(txt){
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        function getHTMLSubs(sub_id, guild_name, role_name, role_color, guild_id, status, date_end) {
            var status_color = status == 'Active' ? 'green-500' : 'yellow-500';
            var status_payment = status == 'Active' ? '' : 'd-none';
            return html = `
                <div>
                  <div class="text-center mt-lg-40" onclick="window.location.href = '/account/subscriptions';">
                    <span class="badge badge-primary badge-lg font-size-20" style="background-color: ` + role_color + `">` + role_name + `</span>
                    <p class="font-weight-100">` + guild_name + `</p>
                  </div>
                </div>
            `;
        }
    </script>


<script>

$(document).on("click", ".open-notification", function () {
     var id = $(this).data('id');
     var type = $(this).data('type');
     var message = $(this).data('message');
     $('#notifications_modal').modal('show');
     if(type == 'success') {
         color = 'success';
     } else if(type == 'warning') {
         color = 'warning';
     } else if(type == 'error') {
         color = 'danger';
     }
     $(".modal.notification #noti_type").addClass('badge-' + color).text( type ).removeClass('badge-info');
     $(".modal.notification #noti_message").text( message );
     $('#notifications_modal').attr('noti-id', id);
     // As pointed out in comments,
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');

                $.ajax({
                    url: '/bknd00/mark-notification-read/' + id,
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                  $('#not1fication_' + id).blur().addClass('read').removeClass('unread');
                });

});
$('#notifications_modal').on('hide.bs.modal', function() {
        var id = $(this).data('id');
        $(".modal.notification #noti_type").addClass('badge-info').removeClass('fade badge-success badge-warning badge-danger');

});
</script>


    <script type="text/javascript">
      $(window).on('load', function() {
        $("#subscriptionsSlider").slick({
          arrows:false,
          autoplay:true
        });
      });
    </script>
  <script type="text/javascript">
    setTimeout(function(){
    if(window.location.href.includes('open-servers=true')) {
        $('#servers-block').click()
        }
    },1800);
    </script>


<script>

// $(document).ready(function () {
//         fetchNotifications();

//         $('#notification_count_1').text('0');

//         function fetchNotifications() {
//         $.ajax({
//             url: '/bknd00/get_notifications',
//             type: 'GET',
//             data: {
//                 _token: '{{ csrf_token() }}'
//             },
//         }).done(function (msg) {
//             //$('#notification_count_1').addClass('badge-default').removeClass('badge-primary');
//             $('#notification_count_1-pg').text(msg['unread_count']);

//             msg['notifications'].reverse().forEach(notification => {
//                 if($('#not1fication_' + notification['id']).length) {
//                 } else {
//                     var color = 'blue';

//                     if(notification['type'] == 'success') {
//                         color = 'green';
//                     } else if(notification['type'] == 'warning') {
//                         color = 'yellow';
//                     } else if(notification['type'] == 'error') {
//                         color = 'red';
//                     }
//                     if(notification['read']==true){
//                       read = 'read'
//                     }else{
//                       read = 'unread'
//                     }

//                     var timeDiff = timeDiffStr(new Date(notification['created_at'] * 1000).getTime(), (new Date()).getTime());

//                     var html = `
//                         <a class="list-group-item dropdown-item px-15 m-0 open-notification ${read}" href="#notifications_modal" id="not1fication_${notification['id']}" data-id="${notification['id']}" data-type="${notification['type']}" data-message="${notification['message']}">
//                             <div class="media">
//                                 <div class="pr-10">
//                                     <i class="icon wb-order bg-${color}-600 white icon-circle"
//                                     aria-hidden="true"></i>
//                                 </div>
//                                 <div class="media-body">
//                                     <h6 class="media-heading text-truncate" title="${notification['message']}">${notification['message']}</h6>
//                                     <time class="media-meta">${timeDiff}</time>
//                                 </div>
//                             </div>
//                         </a>
//                     `;

//                     $('#notifications-dropdown').prepend(html);
//                 }
//             });
//         });
//     }

//     setInterval(fetchNotifications, 2000);
//     });



</script>

{{--
<script type="text/javascript">
    var guild_id = null, role_id = null;
    $(document).ready(function () {
        socket.emit('get_guilds', [socket_id, '{{ auth()->user()user()->DiscordOAuth->discord_id }}']);

        socket.on('res_guilds_' + socket_id, function (message) {
            $('#servers-table-side').empty();

            Object.keys(message).forEach(function (key) {

                var html = `
                <li class="list-group-item px-5">
                    <div class="d-flex align-items-start">
                      <div class="pl-2 pr-10">
                        <a class="avatar avatar-lg" href="javascript:void(0)">
                          <img class="img-fluid" src="${message[key]['iconURL']}" alt="...">
                        </a>
                      </div>
                      <div class="media-body">
                        <h5 class="mt-5 mb-5">${message[key]['name']}</h5>
                        <small>${message[key]['memberCount']} Members</small>
                      </div>
                      <div class="pl-5">
                        <button type="button" class="btn btn-primary mt-5" onclick="window.location.href = '{{ SiteConfig::get('APP_URL') }}/server/${key}';">Shop</button>
                      </div>
                    </div>
                  </li>
                  `;

                $('#servers-table-side').append(html);
            });
        });

    });
</script> --}}
@endsection
{{-- @foreach(Refund::where('owner_id', (auth()->User()->id))->get() as $refundrequest)
@if(!$refundrequest->decision)
<script type="text/javascript">
  setTimeout(function(){
        Swal.fire({
            title: "New Refund Request",
            html: "User: {{ $refundrequest->getUser()->getDiscordUsername() }}<br>Role: {{ $refundrequest->role_name }}<br>Purchase Date: {{ Carbon::createFromTimestamp($refundrequest->start_date)->toDateTimeString() }}@if(($refundrequest->refunds_enabled) == '1')<br><br><b>Your Refund Policy: </b>{{ $refundrequest->refund_days }} days, @if(($refundrequest->refund_terms) == '1')No Questions Asked @endif @if(($refundrequest->refund_terms) == '2')by server owner discretion with reason. @endif @endif",
            // html: "<b>Refund Policy:</b> 15 days by server owner discretion with reason.<br>Username: SnowFalls<br>Role: VIP Member<br>Purchase Date: 05/06/19<br>Reason: Here is the request reason the user entered",
            footer: '<span class=\"text-white text-center\"><div class=\"checkbox-custom checkbox-default\"><input type=\"checkbox\" id=\"sub_ban\" name=\\"inputSub_ban\" autocomplete=\"off\"><label for=\"inputSub_ban\">Ban user from future purchases?</label></div></span>',
            type: 'warning',
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, accept refund!',
            cancelButtonText: 'No, deny request.',
            target: document.getElementById('slider-div')
        }).then(function(result){
            if($("#sub_ban").is(':checked')) {
                var subBan = "1";
            }else{
                var subBan = "0";
            }
            if (result.value) {
                $.ajax({
                    url: '/request-subscription-decision',
                    type: 'POST',
                    data: {
                        sub_id: '{{ $refundrequest->sub_id }}',
                        issued: '1',
                        ban: subBan,
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {
                    if (msg['success']) {
                        Swal.fire({
                            title: 'Thank you.',
                            text: 'User notified, subscription cancelled and role removed. Refund queued.',
                            //input: 'checkbox',
                            //inputPlaceholder: 'Ban user from future purchases?',
                            type: 'success',
                            showCancelButton: false,
                        }).then(result => {
                            $('#close-slide').click();
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: msg['msg'],
                            type: 'warning',
                            showCancelButton: false,
                        });
                    }
                })
            }else if(result.dismiss == 'cancel'){

              $.ajax({
                    url: '/request-subscription-decision',
                    type: 'POST',
                    data: {
                        sub_id: '{{ $refundrequest->sub_id }}',
                        issued: '0',
                        ban: subBan,
                        _token: '{{ csrf_token() }}'
                    },
                  }).done(function (msg) {
                    if (msg['success']) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Refund denied. User notified, subscription was not cancelled. Thank you.',
                            //input: 'checkbox',
                            //inputPlaceholder: 'Ban user from future purchases?',
                            type: 'success',
                            showCancelButton: false,
                        }).then(result => {
                            $('#close-slide').click();
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: msg['msg'],
                            type: 'warning',
                            showCancelButton: false,
                        });
                    }
                  })
              }
          })
    },3000);
</script>
@endif
@endforeach --}}
