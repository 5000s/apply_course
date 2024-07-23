@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        <p>เราได้ส่ง Email ไปยัง {{ $maskedEmail }} เพื่อให้ท่านสร้างรหัสผ่านใหม่</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
