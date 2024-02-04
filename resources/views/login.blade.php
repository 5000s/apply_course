@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-9 col-lg-8 mx-auto">
            <h3 class="login-heading mb-4">ระบบลงทะเบียนสมัครเข้าคอร์สปฎิบัติ</h3>
            <form action="{{url('post-login')}}" method="POST" id="logForm">

                {{ csrf_field() }}

                <div class="form-label-group">

                    <label for="name">ชื่อจริง</label>
                    <input type="name" name="name" id="inputName" class="form-control" placeholder="ชื่อจริง" >

                    @if ($errors->has('name'))
                        <span class="error">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-label-group">

                    <label for="name">นามสกุล</label>
                    <input type="lastname" name="lastname" id="inputLastName" class="form-control" placeholder="นามสกุล" >

                    @if ($errors->has('lastname'))
                        <span class="error">{{ $errors->first('lastname') }}</span>
                    @endif
                </div>

                <div class="form-label-group">
                    <label for="inputPhone">เบอร์โทรศัพท์ติดต่อ </label>
                    <input type="phone" name="phone" id="inputPhone" class="form-control" placeholder="0819999999">


                    @if ($errors->has('phone'))
                        <span class="error">{{ $errors->first('phone') }}</span>
                    @endif
                </div>

                <button class="btn btn-lg btn-primary btn-block btn-login text-uppercase font-weight-bold mb-2" type="submit">ลงชื่อเข้าใช้</button>
                <div class="text-center">หากท่านยังไม่มีบัญชี
                    <a class="small" href="{{url('registration')}}">สร้างบัญชี</a>
                </div>
            </form>
        </div>
    </div>
@endsection
