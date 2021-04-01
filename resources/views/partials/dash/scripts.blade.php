<!-- Backend Bundle JavaScript -->
<script src="{{ asset('dash/assets/js/backend-bundle.min.js') }}"></script>

<!-- Flextree Javascript-->
<script src="{{ asset('dash/assets/js/flex-tree.min.js') }}"></script>
<script src="{{ asset('dash/assets/js/tree.js') }}"></script>

<!-- Table Treeview JavaScript -->
<script src="{{ asset('dash/assets/js/table-treeview.js') }}"></script>

<!-- Masonary Gallery Javascript -->
<script src="{{ asset('dash/assets/js/masonry.pkgd.min.js') }}"></script>
<script src="{{ asset('dash/assets/js/imagesloaded.pkgd.min.js') }}"></script>

<!-- Mapbox Javascript -->
<script src="{{ asset('dash/assets/js/mapbox-gl.js') }}"></script>
<script src="{{ asset('dash/assets/js/mapbox.js') }}"></script>

<!-- Fullcalender Javascript -->
<script src="{{ asset('dash/assets/vendor/fullcalendar/core/main.js') }}"></script>
<script src="{{ asset('dash/assets/vendor/fullcalendar/daygrid/main.js') }}"></script>
<script src="{{ asset('dash/assets/vendor/fullcalendar/timegrid/main.js') }}"></script>
<script src="{{ asset('dash/assets/vendor/fullcalendar/list/main.js') }}"></script>

<!-- SweetAlert JavaScript -->
<script src="{{ asset('dash/assets/js/sweetalert.js') }}"></script>

<!-- Vectoe Map JavaScript -->
<script src="{{ asset('dash/assets/js/vector-map-custom.js') }}"></script>

<!-- Chart Custom JavaScript -->
<script src="{{ asset('dash/assets/js/customizer.js') }}"></script>

<!-- Chart Custom JavaScript -->
<script src="{{ asset('dash/assets/js/chart-custom.js') }}"></script>

<!-- slider JavaScript -->
<script src="{{ asset('dash/assets/js/slider.js') }}"></script>

<!-- app JavaScript -->
<script src="{{ asset('dash/assets/js/app-v1.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="{{ asset('examples/js/apps/beast.js') }}"></script>
<script src="https://js.stripe.com/v3/"></script>

@auth
    <script type="text/javascript">
        let stripe = Stripe('{{ \App\StripeHelper::getStripePublic() }}');

        function timeDiff( tstart, tend ) {
            var diff = Math.floor((tend - tstart) / 1000), units = [
                { d: 60, l: "seconds" },
                { d: 60, l: "minutes" },
                { d: 24, l: "hours" },
                { d: 7, l: "days" }
            ];

            var data = [];

            for (var i = 0; i < units.length; ++i) {
                data[units[i].l] = (diff % units[i].d);
                diff = Math.floor(diff / units[i].d);
            }

            return data;
        }

        function timeDiffStr(tstart, tend) {
            var timeDiffArray = timeDiff(tstart, tend);
            var timeDiffVal = timeDiffArray['days'];

            if (timeDiffVal == 1) {
                var timeDiffStr =  timeDiffVal + ' day ago';
            }else{
            var timeDiffStr = timeDiffVal + ' days ago';
            }

            if(timeDiffVal < 1) {
                timeDiffVal = timeDiffArray['hours'];
                if (timeDiffVal == 1) {
                        timeDiffStr = timeDiffVal + ' hour ago';
                }else{
                        timeDiffStr = timeDiffVal + ' hours ago';
                }
                if(timeDiffVal < 1) {
                    timeDiffVal = timeDiffArray['minutes'];
                    if (timeDiffVal == 1) {
                        timeDiffStr = timeDiffVal + ' minute ago';
                    }else{
                        timeDiffStr = timeDiffVal + ' minutes ago';
                    }
                    if(timeDiffVal < 1) {
                        timeDiffVal = timeDiffArray['seconds'];
                        if (timeDiffVal == 1) {
                            timeDiffStr = timeDiffVal + ' second ago';
                        }else{
                            timeDiffStr = timeDiffVal + ' seconds ago';
                        }
                    }
                }
            }

            return timeDiffStr;
        }
    </script>
{{--

  <!-- @include('partials.notifications_script') -->
    @if(!str_contains(url()->current(), '/account/notifications'))
        @include('partials.notifications_script')
    @endif
--}}
    <script>
        $('.site-helptools-toggle').on( "click", function() {
            location.hash = "?help";
        });
    </script>




<script>
        console.log("goPremiumButton here");

        function goPremiumButton(){


            Swal.fire({
                //title: 'Custom width, padding, background.',
                title: '<strong>Beastly <u>Premium</u> Member</strong>',
                //icon: 'info',
                html:
                    'Unlock <b>special premium features<b> to your store<br>' +
                    'and control in new ways.',
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText:
                    '<i class="las la-certificate"></i> Great!',
                confirmButtonAriaLabel: 'Premium, great!',
                cancelButtonText:
                    '<i class="fa fa-thumbs-down"></i>',
                cancelButtonAriaLabel: 'Thumbs down',
                width: 600,
                padding: '3em',
                background: '#2C2F33 url("/site/assets/images/4-b.png") no-repeat scroll center top / cover',
                backdrop: `
                    rgba(0,0,123,0.4)
                    url(https://sweetalert2.github.io/images/nyan-cat.gif)
                    left top
                    no-repeat
                `
                }).then((result) => {
                    console.log(result)
                /* Read more about isConfirmed, isDenied below */
                    if (result.value == true) {
                            /*const inputOptions = new Promise((resolve) => {
                                setTimeout(() => {
                                    resolve({
                                    'beastly_premium_monthly-1': 'Monthly',
                                    'beastly_premium_yearly-1': 'Yearly',
                                    })
                                }, 1000)
                            })*/

                            Swal.fire({
                            title: 'Select Premium Plan',
                            confirmButtonText:
                            '<i class="las la-certificate"></i> Unlock',
                            confirmButtonAriaLabel: 'Premium, great!',
                            width: 600,
                            padding: '3em',
                           // background: '#000 url("https://sweetalert2.github.io/images/trees.png")',
                            background: '#2C2F33 url("/site/assets/images/4-b.png") no-repeat scroll center top / cover',
                            backdrop: `
                                rgba(0,0,123,0.4)
                                url(https://sweetalert2.github.io/images/nyan-cat.gif)
                                left top
                                no-repeat
                            `,
                            input: 'radio',
                            inputOptions: {
                                'beastly_premium_monthly-1': 'Monthly',
                                'beastly_premium_yearly-1': 'Yearly',
                            },
                                inputValidator: (value) => {
                                    console.log(value);
                                    if (!value) {
                                    return 'Select the plan that suits you, discount on yearly!'
                                    }else{
                                        interval_plan = value;
                                        if (interval_plan) {
                                            window.open(`https://thestripelink.com/${interval_plan}`, "_blank") || window.location.replace(`https://thestripelink.com/${interval_plan}`);
                                            setTimeout(() => {
                                                Toast.fire({
                                                    title: 'Premium Confirming!',
                                                    text: "We'll let you know when successful",
                                                    type: 'success',
                                                    showCancelButton: false,
                                                    showConfirmButton: false, // TODO: look below, make true if can do session with Toast
                                                });
                                            }, 1000)
                                            /*setTimeout(() => { // for video
                                                Swal.fire({
                                                    title: 'Premium Added',
                                                    text: "Congratulations you're now a Beastly Premium member! Reload the page?",
                                                    type: 'success',
                                                    showCancelButton: true,
                                                    showConfirmButton: true,
                                                });
                                            }, 3000)*/
                                           
                                            //checkPremiumCount(40) <!-- TODO!!: create returnCheckPremium for function checkPremiumCount
                                        }
                                    }
                                }
                            })
                            
                           /* if (interval_plan) {
                                Swal.fire({ html: `You selected: Premium ${interval_plan}... redirecting` })
                            }*/
                            ///Swal.fire('Awesome Opening Page', '', 'success')
                          

                        
                    } else if (result.isDenied) {
                        Swal.fire('All good!', 'Maybe next time :)', 'success')
                    }
                });

        } // end function goPremium


        function checkPremiumCount(checkInterval){ // TODO: session this, instead of waiting on page
            var checkInterval = checkInterval
            
            setTimeout(function() { 

                $.ajax({
                    url: '/bknd00/returnCheckPremium', // TODO: this
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                }).done(function (msg) {

                    if(!msg['success']){
                        checkInterval = checkInterval - 1;
                        if(checkInterval > 0){
                            console.log("checking premium")
                            checkPremiumCount(checkInterval)
                        }else{
                            console.log("false premium")
                        }
                    }else{
                        Swal.fire({
                            title: 'Premium Added',
                            text: "Congratulations you're now a Beastly Premium member! Reload the page?",
                            type: 'success',
                            showCancelButton: true,
                            showConfirmButton: true,
                        });
                        
                        // Save Settings (or page), then....
                        // add fireworks or confetti css, reload.. or change data element to allow premium features

                        // Add if easier to reload the page, add result denied or accepted from confirm/cancel buttons.
                        //location.reload();
                        
                    }

                })


            
            }, 1500);
        }

        </script>

@endauth
