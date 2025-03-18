@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <div class="container" style="max-width: 800px;">
        <div class="row justify-content-center">
{{--            <div class="col-md-12" id="course_button">--}}
{{--                <div class="card">--}}
{{--                    <a href="{{route('showCourseForStudent')}}" class="card-body" style="text-align: center; font-size: 25px; background-color: #2d995b; color:black">--}}
{{--                        ดูตารางคอร์สปฏิบัติทั้งหมด--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}


            <div class="col-md-12" id="login_panel" style="padding-top: 20px;">
                <div class="card">
                    <div class="card-header">{{ __('auth.Login') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3 row">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('auth.Email Address') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('auth.Password') }}</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">{{ __('auth.Remember Me') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0 row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">{{ __('auth.Login') }}</button>
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('auth.Forgot Your Password?') }}</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 📌 เส้นคั่น + ข้อความ --}}
            <div class="col-md-12 text-center my-4">
                <hr>
                <h5 class="text-muted">สำหรับผู้ที่ไม่เคยเข้าระบบมาก่อน</h5>
            </div>

            {{-- 📌 ศิษย์เก่า --}}
            <div class="col-md-6" style="padding-top: 10px">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold text-center bg-light">ศิษย์เก่า</div>
                    <div class="card-body text-center">
                        <p class="text-muted">หากท่านเคยเข้าคอร์สปฏิบัติ หรือเคยส่งใบสมัครมาก่อน</p>
                        <a href="{{ route('request-access') }}" class="btn btn-info text-white w-100 py-3">
                            คลิกเพื่อตั้งรหัสผ่าน
                        </a>
                    </div>
                </div>
            </div>

            {{-- 📌 ศิษย์ใหม่ --}}
            <div class="col-md-6" style="padding-top: 10px">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold text-center bg-light">ศิษย์ใหม่</div>
                    <div class="card-body text-center">
                        <p class="text-muted">หากท่านไม่เคยสมัครหรือเข้าร่วมคอร์สมาก่อน</p>
                        <a href="{{ route('register') }}" class="btn btn-success w-100 py-3">
                            คลิกเพื่อลงทะเบียนใหม่
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
