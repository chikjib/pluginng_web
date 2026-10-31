<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="Pluginng">
    <meta name="description" content="Plugin here to Buy Data, Buy Airtime and Pay bills. Fastest online utility selling platform">
    <meta name="keywords" content="Buy data, Buy airtime, Pay Bills">
    <meta name="robots" content="index, follow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    
    <meta name="author" content="pluginng.com" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('frontend/images/favicon.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontend/images/favicon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/images/favicon.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend/images/favicon.png') }}" />
    <link rel="manifest" href="{{ asset('frontend/images/favicons/site.webmanifest') }}" />

    <!-- bootstrap -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- plugins -->
    <link href="{{ asset('frontend/css/plugins-css.css') }}" rel="stylesheet" type="text/css" />

    <!-- mega menu -->
    <link href="{{ asset('frontend/css/mega-menu/mega_menu.css') }}" rel="stylesheet" type="text/css" />

    <!-- revoluation -->
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/js/revolution/css/extralayers.css') }}"
        media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/js/revolution/rs-plugin/css/settings.css') }}"
        media="screen" />

    <!-- default -->
    <link href="{{ asset('frontend/css/default.css') }}" rel="stylesheet" type="text/css" />

    <!-- main style -->
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" type="text/css" />

    <!-- responsive -->
    <link href="{{ asset('frontend/css/responsive.css') }}" rel="stylesheet" type="text/css" />

    <!-- Style customizer (Remove these two lines please) -->
    <link href="#" data-style="styles" rel="stylesheet">
    <link href="{{ asset('frontend/css/style-customizer.css') }}" rel="stylesheet" type="text/css" />

    <!-- custom style -->
    <link href="{{ asset('frontend/css/custom.css') }}" rel="stylesheet" type="text/css" />



</head>

<body>

    <div class="page-wrapper">

        <!--=================================
 preloader -->

        <div id="preloader">
            <div class="clear-loading loading-effect"> <span></span> </div>
        </div>

        <!--=================================
 preloader -->

        @include('layouts.header')

        @yield('content')



        <!--=================================
 footer -->

        @include('layouts.footer')
    </div>



    <div id="back-to-top"><a class="top arrow" href="#top"><i class="fa fa-long-arrow-up"></i></a></div>

    <!--=================================
 jquery -->

    @auth
        <script>
            window.user = @json(auth()->user())
        </script>
    @endauth


    <!-- jquery  -->
    <script type="text/javascript" src="{{ asset('frontend/js/jquery.min.js') }}"></script>

    <!-- bootstrap -->
    <script type="text/javascript" src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>

    <!-- plugins-jquery -->
    <script type="text/javascript" src="{{ asset('frontend/js/plugins-jquery.js') }}"></script>

    <!-- appear -->
    <script src="{{ asset('frontend/js/jquery.appear.js') }}"></script>

    <!-- mega menu -->
    <script type="text/javascript" src="{{ asset('frontend/js/mega-menu/mega_menu.js') }}"></script>

    <!-- socialstream -->
    <script src="{{ asset('frontend/js/social/socialstream.jquery.js') }}"></script>

    <!-- popup -->
    <script src="{{ asset('frontend/js/popup/jquery.magnific-popup.js') }}"></script>

    <!-- isotope -->
    <script src="{{ asset('frontend/js/isotope/isotope.pkgd.min.js') }}"></script>

    <!-- counter -->
    <script src="{{ asset('frontend/js/counter/jquery.countTo.js') }}"></script>

    {{-- <!-- Style Customizer -->
    <script type="text/javascript" src="{{ asset('frontend/js/style-customizer.js') }}"></script> --}}

    <!-- revoluation -->
    <script type="text/javascript" src="{{ asset('frontend/js/revolution/rs-plugin/js/jquery.themepunch.tools.min.js') }}">
    </script>
    <script type="text/javascript"
        src="{{ asset('frontend/js/revolution/rs-plugin/js/jquery.themepunch.revolution.min.js') }}"></script>

    <!-- custom -->
    <script type="text/javascript" src="{{ asset('frontend/js/custom.js') }}"></script>

    @php
        $tawkto = \App\Models\Setting::where('key', 'LIVECHAT')->first();
        
    @endphp
    {!! $tawkto->value !!}

    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('.tp-banner').show().revolution({
                dottedOverlay: "none",
                delay: 6000,
                startwidth: 1270,
                startheight: 650,
                hideThumbs: 200,
                thumbWidth: 100,
                thumbHeight: 50,
                thumbAmount: 5,

                navigationType: "off",
                navigationArrows: "solo",
                navigationStyle: "preview4",

                touchenabled: "on",
                onHoverStop: "on",

                swipe_velocity: 0.7,
                swipe_min_touches: 1,
                swipe_max_touches: 1,
                drag_block_vertical: false,

                parallax: "mouse",
                parallaxBgFreeze: "on",
                parallaxLevels: [7, 4, 3, 2, 5, 4, 3, 2, 1, 0],

                keyboardNavigation: "off",

                navigationHAlign: "center",
                navigationVAlign: "bottom",
                navigationHOffset: 0,
                navigationVOffset: 20,

                soloArrowLeftHalign: "left",
                soloArrowLeftValign: "center",
                soloArrowLeftHOffset: 20,
                soloArrowLeftVOffset: 0,

                soloArrowRightHalign: "right",
                soloArrowRightValign: "center",
                soloArrowRightHOffset: 20,
                soloArrowRightVOffset: 0,

                shadow: 0,
                fullWidth: "off",
                fullScreen: "off",

                spinner: "spinner4",

                stopLoop: "off",
                stopAfterLoops: -1,
                stopAtSlide: -1,

                shuffle: "off",

                autoHeight: "off",
                forceFullWidth: "off",
                hideThumbsOnMobile: "off",
                hideNavDelayOnMobile: 1500,
                hideBulletsOnMobile: "off",
                hideArrowsOnMobile: "off",
                hideThumbsUnderResolution: 0,

                hideSliderAtLimit: 0,
                hideCaptionAtLimit: 0,
                hideAllCaptionAtLilmit: 0,
                startWithSlide: 0,
                fullScreenOffsetContainer: ".header"
            });
        });
    </script>

</body>

</html>
