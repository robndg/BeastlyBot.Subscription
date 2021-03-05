<!DOCTYPE html>

<html>

<head>
@include('partials.store.head')
@yield('metadata')
</head>

<body>
    <div class="page-wrapper">
    @include('partials.store.header')
    
    @yield('content')
    @yield('shopfooter')
       @section('partials.store.footer')
    </div>
    @include('partials.store.scripts')
    <!--[if lte IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif]-->

    <a class="w-webflow-badge" href="https://beastlybot.com" style="color:#1c1b29!important"><img src="{{ asset('android-chrome-192x192.png') }}" alt="" style="margin-right: 8px; width: 16px;border-radius: 3px;">Powered by BeastlyBot
       
    </a>
    @include('partials.dash.scripts')

    <script type="text/javascript">
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        $(document).ready(function () {
            @if(Session::has('alert'))
            Toast.fire({
                type: '{{ Session::get('alert')['type'] }}',
                title: '{{ Session::get('alert')['msg'] }}'
            });

            {!! Session::forget('alert') !!}
            @endif
        });

    </script>

    <!---- SCRIPTS ----->
    @yield('scripts')
</body>

</html>