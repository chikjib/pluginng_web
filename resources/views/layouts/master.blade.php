<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="Pluginng">
    <meta name="description" content="Plugin here to get the Best rate for Data Purchase, Airtime and Pay bills. Fastest online utility selling platform">
    <meta name="keywords" content="Buy data, Buy airtime, Pay Bills">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    
    <meta name="author" content="pluginng.com" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('template/img/logo/logo.png') }}" rel="icon">
    <title>{{ config('app.name', 'Pluginng') }}</title>
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/css/ruang-admin.min.css') }}" rel="stylesheet">
    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1189822208568969');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1189822208568969&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
</head>




<body>

    <div id="app">


    </div>


    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <!-- Vue initializes dashboard theme events after the async layout mounts. -->
    
    <!--added by Techioex-->
     <script type="text/javascript">
        $(document).ready(function () {
            $('a').each(function () {
                if ($(this).find('.menu-title').text().trim().includes("API Documentation")) {
                    $(this).closest('li a').attr('href', 'https://documenter.getpostman.com/view/32987010/2sAYJAeHjy');
                }
            });
        });
    </script>

    @php
        $tawkto = \App\Models\Setting::where('key', 'LIVECHAT')->first();
        
    @endphp
    {!! $tawkto->value !!}


</body>

</html>
