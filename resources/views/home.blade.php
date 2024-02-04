@extends('layouts.master')

@section('content')
    <style>
        .btn-center{
            display:block;
            text-align:center
        }
    </style>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-4">
                <div class="card" >
                    <img src="{{asset("images/bkk_course.webp")}}" class="card-img-top" alt="...">
                    <div class="card-body ">
                        <h5 class="card-title text-center">กรุงเทพ</h5>
                        <h5 class="card-title text-center">อ่อนนุช</h5>
                        <a href="{{url('course/4/detail')}}" class="btn btn-primary btn-center">
                            ดูรายละเอียดคอร์ส
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="card" >
                    <img src="{{asset("images/saraburi_course.webp")}}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title text-center">สระบุรี</h5>
                        <h5 class="card-title text-center">แก่งคอย</h5>
                        <a href="{{url('course/1/detail')}}" class="btn btn-primary btn-center">
                            ดูรายละเอียดคอร์ส
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="card" >
                    <img src="{{asset("images/songkra_course.webp")}}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title text-center">สงขลา</h5>
                        <h6 class="card-title text-center">หาดใหญ่</h6>
                        <a href="{{url('course/3/detail')}}" class="btn btn-primary btn-center">
                            ดูรายละเอียดคอร์ส
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
