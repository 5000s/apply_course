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

            <div class="col-md-8 " style="padding-top: 10px">
                <div class="card">
                    <div class="card-body" style="font-size: 18px; text-align: center;">
                        กรุณาเช็คอีเมลของท่าน เพื่อทำการตั้งรหัสผ่านใหม่
                        <br/> เมื่อตั้งเสร็จแล้ว จะสามารถเข้าสู่ระบบด้วยอีเมลและรหัสผ่านได้ <br/>
                        <ol style="text-align: left; ">
                            <li>  คลิกรีเซ็ตรหัสผ่าน  </li>
                            <li>  กรอกพาสเวิร์ดให้เหมือนกันทั้ง 2 ช่อง   </li>
                            <li>  จากนั้นกดปุ่ม Reset Password </li>
                            <li>  จะเข้าสู่ระบบโดยอัตโนมัติในครั้งแรก ที่ทำการตั้งรหัสผ่านใหม่ </li>
                        </ol>

                        <img src="{{asset("images/email_reset_1.jpg")}}" class="w-100">
                        <img src="{{asset("images/email_reset_2.jpg")}}" class="w-100">

                        <a href="{{route("login")}}" class="btn shadow w-100" style="font-size: 18px; background-color: #ff6365; color: #111111;">ไปยังหน้าเข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
