@extends('layouts.app')

@section('content')
    <br>
    <br>
    
    
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
        
        <div class="row">
          <div class="col-xl-6 col-lg-6 mx-auto">
            <div class="sign-in__single">
              <h3 class="sign-in__title">MTN Direct Automation APP Login</h3>

              <form
                method="POST"
                action="{{ route('generateMtnOtpApp') }}"
                class="sign-in__form"
              >
              @csrf
                <div class="sign-in__form-input-box">
                  <input
                    type="text"
                    class="form-control"
                    required
                    name="phone_number"
                    placeholder="Enter Phone Number e.g +23470...."
                  />
                </div>


                <div class="sign-in__form-btn-box">
                  <button
                    type="submit"

                    class="btn btn-primary btn-block"
                  >
                    Submit
                  </button>



                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
