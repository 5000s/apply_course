@extends('layouts.master')

@section('content')
    <style>
        .btn-center{
            display:block;
            text-align:center;
        }
    </style>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="btn-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        <div class="btn-center mt-3">
                            If you forgot your password, <a href="{{ route('password.request') }}">click here</a> to reset.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
