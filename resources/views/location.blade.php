@extends('layouts.master')

@section('content')
    <style>
        .btn-center{
            display:block;
            text-align:center
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h3> {{$location->name}} {{$location->full_address}}</h3>
             </div>
            <div class="col-lg-8">
                <img  src="{{asset("images/".$location->image)}}" style="width: 100%;"/>
            </div>
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>แบบการฝึก</th>
                            <th>สถานะ</th>
                            <th>สมัคร</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td> {{$course->start }}</td>
                            <td> {{$course->end}}</td>
                            <td> {{$course->category}}</td>
                            <td> {{$course->state}}</td>
                            <td>
                                @if($course->state == "เปิดรับสมัคร")
                                <a href="{{url("course/".$course->id."/register")}}"> สมัคร </a>
                                    @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>




                </table>
            </div>
        </div>
    </div>
@endsection
