@extends('layouts.app')

@section('content')
    <!--=================================
                                                                                                                                                                                                                                                                                                 inner-intro-->

    <section class="bg-2">
        <div class="container">
            <div class="row text-center intro-title">

                <ul class="page-breadcrumb">
                    <li><a href="/"><i class="fa fa-home"></i> Home</a> <i class="fa fa-angle-double-right"></i></li>
                    <li><span>Pricing</span> </li>
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
                        <h1 class="text-blue">Our Data pricing</h1>
                        <div class="title-line"></div>
                        <p>Below is our data price list</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="pricing-2 gray-bg page-section-ptb mt-70">
                        <div class="container">
                            <div class="col-md-12">

                                @foreach ($category as $plan)
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="pricing-table text-center">
                                            <div class="pricing-title">
                                                @php
                                                    $title = $plan->category->title;
                                                    $varr = explode(' ', $plan->title);
                                                    if (count($varr) > 1) {
                                                        $var = $varr[1];
                                                        if ($var == 'DATA' || $var == 'data' || $var == 'Data') {
                                                            $title = '';
                                                        }
                                                    }
                                                    
                                                @endphp
                                                <h2>{{ $plan->title }} {{ $title }}</h2>

                                            </div>
                                            <div class="pricing-list">
                                                @php
                                                    $dataprice = json_decode($plan->products);
                                                    
                                                @endphp
                                                <ul>
                                                    <li> <img width="40px" height="40px"
                                                            src="{{ asset('template/images/services/' . $plan->subcat_image) }}" />
                                                    </li>
                                                    <li>
                                                        <div class="row">
                                                            <div class="col-md-4"><b>Plan</b></div>
                                                            <div class="col-md-2"><b>Normal</b></div>
                                                            <div class="col-md-2"><b>Agent</b></div>
                                                            <div class="col-md-2"><b>Reseller</b></div>
                                                            <div class="col-md-2">
                                                                <b>API</b>
                                                            </div>
                                                        </div>

                                                    </li>
                                                    @if (!is_null($dataprice) && !is_array($dataprice))
                                                        <li>
                                                            <div class="row">
                                                                <div class="col-md-4">Discount
                                                                </div>
                                                                <div class="col-md-2">
                                                                    {{ $dataprice->amount1 }}%</div>
                                                                <div class="col-md-2">
                                                                    {{ $dataprice->amount2 }}%</div>
                                                                <div class="col-md-2">
                                                                    {{ $dataprice->amount3 }}%</div>
                                                                <div class="col-md-2">
                                                                    {{ $dataprice->amount4 }}%</div>
                                                            </div>

                                                        </li>
                                                    @elseif (!is_null($dataprice) && is_array($dataprice) && count($dataprice) > 0)
                                                        @foreach ($dataprice as $price)
                                                            <li>
                                                                <div class="row">
                                                                    <div class="col-md-4">{{ $price->plan }}
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        &#8358;{{ $price->amount1 }}</div>
                                                                    <div class="col-md-2">
                                                                        &#8358;{{ $price->amount2 }}</div>
                                                                    <div class="col-md-2">
                                                                        &#8358;{{ $price->amount3 }}</div>
                                                                    <div class="col-md-2">
                                                                        &#8358;{{ $price->amount4 }}</div>
                                                                </div>

                                                            </li>
                                                        @endforeach
                                                    @else
                                                        <li>{{ $dataprice }}</li>
                                                    @endif



                                                </ul>
                                            </div>
                                            <div class="pricing-order">
                                                <a class="button-border" href="/login"><span>Buy Now!</span></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
