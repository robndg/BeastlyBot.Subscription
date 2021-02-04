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

@endauth
