@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('reset.title') }}
                    </div>

                    <div class="card-body">
                        <p>
                            {{ __('reset.email_sent', ['email' => $maskedEmail]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8" style="padding-top: 10px">
                <div class="card">
                    <div class="card-body" style="font-size: 18px; text-align: center;">
                        {{ __('reset.please_check') }}
                        <br/> {{ __('reset.after_set') }} <br/>

                        <ol style="text-align: left;">
                            <li>{{ __('reset.step1') }}</li>
                            <li>{{ __('reset.step2') }}</li>
                            <li>{{ __('reset.step3') }}</li>
                            <li>{{ __('reset.step4') }}</li>
                        </ol>

                        <img src="{{ asset('images/email_reset_1.jpg') }}" class="w-100" alt="{{ __('reset.image1_alt') }}">
                        <img src="{{ asset('images/email_reset_2.jpg') }}" class="w-100" alt="{{ __('reset.image2_alt') }}">

                        <a href="{{ route('login') }}" class="btn shadow w-100" style="font-size: 18px; background-color: #ff6365; color: #111111;">
                            {{ __('reset.go_to_login') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
