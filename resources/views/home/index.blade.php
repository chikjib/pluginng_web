@extends('layouts.app')

@section('content')
    <!--=================================
                                                                                                                                                                                                                                            slider- -->

    <div class="rev-slider-2">
        <div class="tp-banner-container">
            <div class="tp-banner">
                <ul>

                    @foreach ($slides as $slide)
                        @php
                            if (!empty($slide->description)) {
                                $sliders = explode('|', $slide->description);
                                if (count($sliders) > 1) {
                                    $header = $sliders[0];
                                    $description = $sliders[1];
                                } else {
                                    $header = $sliders[0];
                                    $description = '';
                                }
                            } else {
                                $header = '';
                                $description = '';
                            }
                            
                        @endphp
                        <!-- SLIDE  -->
                        <li data-transition="random-static" data-slotamount="2" data-masterspeed="1000"
                            data-thumb="{{ asset('storage/category/' . $slide->cat_image) }}" data-saveperformance="on"
                            data-title="{{ $header }}" class="rav-1">
                            <!-- MAIN IMAGE -->
                            <img src="{{ asset('storage/category/' . $slide->cat_image) }}" alt="slider-01"
                                data-bgposition="center top" data-bgfit="normal" data-bgrepeat="no-repeat">
                            <!-- LAYERS -->
                            <!-- LAYER NR. 1 -->
                            <div class="tp-caption" data-x="60" data-y="150" data-speed="300" data-start="1300"
                                data-easing="Power3.easeInOut" data-splitin="none" data-splitout="none"
                                data-elementdelay="5" data-endelementdelay="0.1" data-endspeed="300"
                                style="z-index: 5; font-weight: 600; font-size: 18pt; max-width: auto; max-height: auto; white-space: nowrap;">

                                {{ $header }}
                            </div>
                            <!-- LAYER NR. 4 -->
                            <div class="col-md-6 tp-caption title-1-desc sfl tp-resizeme" data-x="60" data-y="200"
                                data-speed="300" data-start="2100" data-easing="Power3.easeInOut" data-splitin="none"
                                data-splitout="none" data-elementdelay="7" data-endelementdelay="0.1" data-endspeed="300"
                                style="z-index: 8; max-width: auto; max-height: auto; white-space: nowrap;">
                                {!! html_entity_decode($description) !!}
                            </div>

                            <!-- LAYER NR. 6 -->
                            <div class="tp-caption title-1-bt sfl tp-resizeme" data-x="70" data-y="300"
                                data-speed="300" data-start="2400" data-easing="Linear.easeNone" data-splitin="none"
                                data-splitout="none" data-elementdelay="7" data-endelementdelay="0.1" data-endspeed="300"
                                style="z-index: 10; max-width: auto; max-height: auto; white-space: nowrap; line-height:21px;">
                                <a href='about' class='button'><span>About Us</span> </a>
                            </div>
                            <!-- LAYER NR. 7 -->
                            <div class="tp-caption title-1-btn sfr tp-resizeme" data-x="233" data-y="300"
                                data-speed="300" data-start="2400" data-easing="Linear.easeNone" data-splitin="none"
                                data-splitout="none" data-elementdelay="7" data-endelementdelay="0.1" data-endspeed="300"
                                style="z-index: 11; max-width: auto; max-height: auto; white-space: nowrap; line-height:21px;">
                                <a href='contact' class='button button-black'><span>Contact Us</span> </a>
                            </div>
                        </li>
                    @endforeach


                </ul>
                <div class="tp-bannertimer tp-bottom"></div>
            </div>
        </div>
    </div>

    <!--=================================
                                                                                                                                                                                                                                            slider -->


    <!--=================================
                                                                                                                                                                                                                                            action box- -->

    <section class="action-box-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <h3 class="text-white">{{ config('app.siteName') }} is the best utility platform in Nigeria.</h3>
                    <p class="text-white">Fast easy reliable way to purchase airtime, data, tv subscription and more</p>
                </div>
                <div class="col-lg-4 col-md-4 text-right action-box-button mt-40">
                    <a class="button button-white" href="register">
                        <span>Create Account</span>
                        <i class="fa fa-hand-o-right"></i>
                    </a>
                    <a class="button button-black" target="_blank" href="login">
                        <span>Login</span>
                        <i class="fa fa-users"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!--=================================
                                                                                                                                                                                                                                            action box- -->


    <!--=================================
                                                                                                                                                                                                                                            feature- -->

    <section class="feature-8-main white-bg">
        <div class="container-fluid fluid-padd">
            <div class="row">
                <div class="owl-carousel-10">
                    <div class="item">
                        <div class="feature-8">
                            <div class="feature-8-title">
                                <span aria-hidden="true" class="icon-layers"></span>
                            </div>
                            <div class="feature-8-info">
                                <h4 class="text-back">Trusted</h4>
                                <p>{{ config('app.siteName') }} is a trusted and reliable platform for quality service
                                    delivery in sales of mobile data, airtime, decoder subscriptions and Electricity bills
                                    payment.</p>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="feature-8">
                            <div class="feature-8-title">
                                <span class="icon-picture" aria-hidden="true"></span>
                            </div>
                            <div class="feature-8-info">
                                <h4 class="text-back">Easy Payment</h4>
                                <p>We accept a vast means of payments which includes cash deposits,internet banking
                                    transfer,mobile transfer,payments with ATM cards for your convenience.

                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="feature-8">
                            <div class="feature-8-title">
                                <span class="icon-mouse" aria-hidden="true"></span>
                            </div>
                            <div class="feature-8-info">
                                <h4 class="text-back">Automated</h4>
                                <p>We have a swift automated system with a 24hour service Delivery to meet your mobile needs

                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="feature-8">
                            <div class="feature-8-title">
                                <span class="icon-equalizer" aria-hidden="true"></span>
                            </div>
                            <div class="feature-8-info">
                                <h4 class="text-back">Affordability
                                </h4>
                                <p>We are Economice seller, Quality + Affordability + Trustworthy =
                                    {{ config('app.siteName') }}

                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!--=================================
                                                                                                                                                                                                                                            feature- -->

    <hr class="mr" />

    <!--=================================
                                                                                                                                                                                                                                            feature- -->

    <!--=================================
                                                                                                                                                                                                                                            feature- -->


    <!--=================================
                                                                                                                                                                                                                                            most Awesome- -->

    <section class="dropping-words-main bg-5 white-bg page-section-ptb">
        <div class="container-fluid fluid-padd">
            <div class="row">
                <div class="col-md-6 col-md-offset-1 col-sm-12">
                    <div class="custom-content-2">
                        <h1 class="mb-15">{{ config('app.siteName') }}

                        </h1>
                        {!! Str::limit($settings->value, 350) !!}
                        <a class="button mt-20" href="about">
                            <span>Learn More</span>
                            <i class="fa fa-hand-o-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--=================================
                                                                                                                                                                                                                                            most Awesome- -->


    <!--=================================
                                                                                                                                                                                                                                            happy clients -->


    <!--=================================
                                                                                                                                                                                                                                            happy clients -->




    <!--=================================
                                                                                                                                                                                                                                             Feature-->

    <section class="feature-main">
        <div class="container-fluid fluid-padd">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="feature-9 feature-9-bg-1 clearfix">
                        <div class="feature-9-title-left">
                            <h1>1</h1>
                        </div>
                        <div class="feature-9-title-2-right">
                            <h2 class="text-white">Register! </h2>
                            <p class="text-white">Create an account easily</p>
                        </div>
                        <div class="feature-9-info mt-30">
                            <p class="text-white">Its very easy to create an account by clicking on register. Fill the
                                registration form, verify your email address and you are set
                            </p>
                        </div>
                        <div class="feature-9-icon">
                            <span class="icon-notebook" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="feature-9 feature-9-bg-2 clearfix">
                        <div class="feature-9-title-left">
                            <h1>2</h1>
                        </div>
                        <div class="feature-9-title-2-right">
                            <h2 class="text-white">Fund Wallet</h2>
                            <p class="text-white">Many ways to fund wallet</p>
                        </div>
                        <div class="feature-9-info mt-30">
                            <p class="text-white">We provide the fastest and easiest ways to fund your wallet for
                                transaction. You can use card payment or transfer to dedicated virtual account. You can also
                                convert airtime to cash</p>
                        </div>
                        <div class="feature-9-icon">
                            <span class="icon-settings" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="feature-9 feature-9-bg-3 clearfix">
                        <div class="feature-9-title-left">
                            <h1>3</h1>
                        </div>
                        <div class="feature-9-title-2-right">
                            <h2 class="text-white">Pay Bills</h2>
                            <p class="text-white">Start paying bills easily</p>
                        </div>
                        <div class="feature-9-info mt-30">
                            <p class="text-white">You can now buy airtime, data, pay tv bills, electricity bills and more.
                                The process is very straight forward with dedicated customer service to attend to you at any
                                given time</p>
                        </div>
                        <div class="feature-9-icon">
                            <span class="icon-note" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
