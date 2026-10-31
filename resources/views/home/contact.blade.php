@extends('layouts.app')

@section('content')
    <section class="bg-2">
        <div class="container">
            <div class="row text-center intro-title">

                <ul class="page-breadcrumb">
                    <li><a href="/"><i class="fa fa-home"></i> Home</a> <i class="fa fa-angle-double-right"></i></li>
                    <li><span>Contact us</span> </li>
                </ul>
            </div>
        </div>
    </section>

    <section class="contact white-bg page-section-ptb">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="section-title-1 text-center">
                        <h1 class="text-blue">Let's Get in Touch!</h1>
                        <div class="title-line"></div>
                        <p>Feel free to reach us with the details below</p>
                    </div>
                </div>
            </div>
            @php
                $setting = explode('|', $settings->value);
                $address = $setting[0];
                $phone = $setting[1];
                $email = $setting[2];
                
            @endphp
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="contact-box text-center">
                        <i class="fa fa-map-marker"></i>
                        <h3 class="mt-20">Address</h3>
                        <p class="mt-20">{{ $address }} </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="contact-box text-center">
                        <i class="fa fa-phone"></i>
                        <h3 class="mt-20">Phone</h3>
                        <p class="mt-20">{{ $phone }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="contact-box text-center">
                        <i class="fa fa-envelope-o"></i>
                        <h3 class="mt-20">Email</h3>
                        <p class="mt-20">{{ $email }}</p>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
