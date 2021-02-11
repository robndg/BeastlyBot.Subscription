<!DOCTYPE html>

<html>

<head>
<title>Store Front Guild</title>
@include('partials.store.head')
</head>

<body>
    <div class="page-wrapper">
    @section('partials.store.header')
    
    @yield('content')
       @section('partials.store.footer')
    </div>
    @include('partials.store.scripts')
    <!--[if lte IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif]-->

    <a class="w-webflow-badge" href="/"><img src="" alt="" style="margin-right: 8px; width: 16px;">
        <img src=""
            alt="Powered by BeastlyBot">
    </a>
</body>

</html>