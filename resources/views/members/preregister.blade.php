@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-5 text-center">
            <div class="col">
                <h2>{{ __('messages.please_select') }}</h2>
                <p>{{ __('messages.select_one_option') }}</p>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-8 offset-md-2 text-center">
                <button class="btn btn-primary btn-lg w-100 mb-2" onclick="window.location='{{ route('request-access') }}'">
                    <i class="fas fa-envelope"></i> {{ __('messages.former_student') }}
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <button class="btn btn-success btn-lg w-100" onclick="window.location='{{ route('register') }}'">
                    <i class="fas fa-user-plus"></i> {{ __('messages.new_application') }}
                </button>
            </div>
        </div>
    </div>
@endsection
