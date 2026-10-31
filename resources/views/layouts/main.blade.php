<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="Pluginng">
    <meta name="description" content="Plugin here to get the Best rate for Data Purchase, Airtime and Pay bills. Fastest online utility selling platform">
    <meta name="keywords" content="Buy data, Buy airtime, Pay Bills">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    
    <meta name="author" content="pluginng.com" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pluginng') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('frontend/images/favicon.png') }}" />


    <!-- custom style -->
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- plugins -->
    <link href="{{ asset('frontend/css/plugins-css.css') }}" rel="stylesheet" type="text/css" />


    <!-- mega menu -->
    <link href="{{ asset('frontend/css/mega-menu/mega_menu.css') }}" rel="stylesheet" type="text/css" />

    <!-- default -->
    <link href="{{ asset('frontend/css/default.css') }}" rel="stylesheet" type="text/css" />


    <!-- main style -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" type="text/css" />

    <!-- responsive -->
    <link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('frontend/css/custom.css') }}" rel="stylesheet" type="text/css" />


    <link href="{{ asset('template/css/ruang-admin.min.css') }}" rel="stylesheet">


</head>

<body>

    <div class="page-wrapper">

        <div id="preloader">
            <div class="clear-loading loading-effect"> <span></span> </div>
        </div>


        @include('layouts.header')
        <section class="bg-2">
            <div class="container">
                <div class="row text-center intro-title">

                    <ul class="page-breadcrumb">
                        <li><a href="/"><i class="fa fa-home"></i> Home</a></li>

                    </ul>
                </div>
            </div>
        </section>
        <section class="about page-section-ptb">
            <div class="container">
                <div id="app">


                </div>
            </div>
        </section>

        @include('layouts.footer')
    </div>



    <div id="back-to-top"><a class="top arrow" href="#top"><i class="fa fa-long-arrow-up"></i></a></div>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <script type="text/javascript" src="{{ asset('frontend/js/jquery.min.js') }}"></script>

    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- plugins-jquery -->
    <script type="text/javascript" src="{{ asset('frontend/js/plugins-jquery.js') }}"></script>

    <!-- appear -->
    <script src="{{ asset('frontend/js/jquery.appear.js') }}"></script>

    <script type="text/javascript" src="{{ asset('frontend/js/mega-menu/mega_menu.js') }}"></script>
    <script type="text/javascript" src="{{ asset('frontend/js/custom.js') }}"></script>

    @php
        $tawkto = \App\Models\Setting::where('key', 'LIVECHAT')->first();
        
    @endphp
    {!! $tawkto->value !!}



</body>

</html>
