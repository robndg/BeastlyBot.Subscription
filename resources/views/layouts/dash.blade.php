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
.sidebar-default .sidebar-bottom {
    padding: 40px 15px 0px !important;
}
</style>

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
            @include('partials.dash.modals')
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
