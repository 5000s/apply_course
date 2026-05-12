@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('reset.new_title') }}
                    </div>

                    <div class="card-body">
                        <p>
                            {{ __('reset.new_email_sent', ['email' => $maskedEmail]) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8" style="padding-top: 10px">
                <div class="card">
                    <div class="card-body" style="font-size: 18px; text-align: center;">
                        <p>{{ __('reset.new_please_check') }}</p>
                        {{-- <p style="color: #dc3545; font-size: 16px;">* {{ __('reset.new_after_login') }}</p> --}}

                        <a href="{{ route('login') }}" class="btn shadow w-100 mt-4"
                            style="font-size: 18px; background-color: #8193ef; color: #111111;">
                            {{ __('reset.go_to_login') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
