@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('messages.course_application_system') }} {{ \Carbon\Carbon::now()->year + 543 }}
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        {{ __('messages.please_sign_in') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
