<html>
<head>
    <title>BeastlyBot - Dash | @yield('title')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#3f8ef7">
    <meta name="msapplication-TileColor" content="#3f8ef7">
    <meta name="theme-color" content="#ffffff">
    @yield('metadata')
    @include('partials.dash.head')
    @yield('head')
</head>

<body class="dash-layout dark">
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    @include('partials.dash.rightbar')
    <!-- Wrapper Start -->
    <div class="wrapper">
        @include('partials.dash.navbar')
        @include('partials.dash.sidebar')
        <div class="content-page">
            @yield('content')
            <!-- Page end  -->
            @include('partials.modals')
        </div>
    </div>
    <!-- Wrapper End-->
    @include('partials.dash.footer')
   
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
