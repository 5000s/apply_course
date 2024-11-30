@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <div class="container" style="max-width: 800px;">
        <div class="row justify-content-center">
            <div class="col-md-12" id="course_button">
                <div class="card">
                    <a href="{{route('showCourseForStudent')}}" class="card-body" style="text-align: center; font-size: 25px; background-color: #eda333; color:black">
                    ดูตารางคอร์สปฏิบัติทั้งหมด
                    </a>
                </div>
            </div>

            <div class="col-md-12" id="login_panel" style="padding-top: 20px;">
                <div class="card">
                    <div class="card-header">{{ __('auth.Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('auth.Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('auth.Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('auth.Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('auth.Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('auth.Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="padding-top: 10px">
                <div class="card">
                    <div class="card-body">
                        <p class="info_header">หากท่านเคยส่งใบสมัครผ่านทางอีเมลหรือเคยเข้าคอร์สปฏิบัติมาก่อน</p>
                        <a href="{{ route('request-access') }}" class="btn btn-info d-block mx-auto text-center" style="color: white">
                            คลิกสมัครเข้าสู่ระบบ <br>  สำหรับผู้มีข้อมูลในระบบอยู่แล้ว
                        </a>
                        <ul class="mt-3 text-start">
                            <li>เคยส่งใบสมัครแล้ว</li>
                            <li>เคยเข้าคอร์สปฏิบัติ</li>
                            <li>ศิษย์เก่า</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6"  style="padding-top: 10px">
                <div class="card">
                    <div class="card-body">
                        <p class="info_header">หากท่านไม่เคยส่งใบสมัครทางอีเมลหรือช่องทางอื่นใดมาก่อน</p>
                        <a href="{{ route('register') }}" class="btn btn-success d-block mx-auto text-center">
                            คลิกสมัครเข้าสู่ระบบ <br> สำหรับสมาชิกใหม่
                        </a>
                        <ul class="mt-3 text-start">
                            <li>ไม่เคยใบสมัครทางอีเมล</li>
                            <li>หากท่านเคยเป็นศิษย์แล้วมาสมัครใหม่ ข้อมูลเก่าของท่านจะสูญหาย</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
