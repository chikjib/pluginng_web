@extends('layouts.app')

@section('content')
    <!--=================================
                                     inner-intro-->

    <section class="bg-2">
        <div class="container">
            <div class="row text-center intro-title">

                <ul class="page-breadcrumb">
                    <li><a href="/"><i class="fa fa-home"></i> Home</a> <i class="fa fa-angle-double-right"></i></li>
                    <li><span>about us</span> </li>
                </ul>
            </div>
        </div>
    </section>

    <!--=================================
                                     inner-intro-->

    <section class="about page-section-ptb">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="section-title-1 text-center">
                        <h1 class="text-blue">About us</h1>
                        <div class="title-line"></div>
                        <p>Who we are and what we do</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="custom-content">
                        {!! $settings->value !!}
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div>
                        <img id="logo_dark_img" src="{{ asset('frontend/images/Pluginng.png') }}" alt="logo">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
