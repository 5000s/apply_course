@extends('layouts.master')

@section('content')
    <style>
        .btn-center {
            display: block;
            text-align: center
        }
    </style>

    <div class="container-fluid">
        @include('partials.courseheader', ['course' => $course])


        <form action="{{url("member/search")}}" method="post" id="member_search">
            @csrf
            <input type="hidden" name="course" value="{{$course->id}}">
            <div class="row justify-content-center " style="padding-top: 10px; ">
                <div class="col-lg-6 col-md-12 thing">
                    <div class="row ">
                        <div class="col-12 text-center">
                            กรุณากรอกข้อมูลเพื่อค้นหาข้อมูลเดิม<br>
                            Please fill the form for searching your data <br>
                        </div>
                    </div>
                    <div class="row pt-2 justify-content-center">
                        <div class="col-lg-12 col-md-12 text-center">
                            ชื่อจริง-นามสกุล/ firstname - lastname
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="row justify-content-center">
                                <div class="col-lg-5 col-md-6">
                                    <input required name="name" id="name" class="form form-control" maxlength="50" type="text"
                                           placeholder="ชื่อจริง/Firstname">
                                </div>
                                <div class="col-lg-5 col-md-6 ">
                                    <input required name="lastname" id="lastname" class="form form-control " maxlength="50" type="text"
                                           placeholder="นามสกุล/Lastname">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-lg-12 text-center">
                            วันเกิด/birthday
                        </div>
                        <div class="col-lg-12 text-center">
                            <select required class="select-form" name="day" id="day">
                                <option value="0">วัน/day</option>
                                @for($day = 1;$day <= 31 ; $day++)
                                    <option value="{{$day}}">{{$day}}</option>
                                @endfor
                            </select>
                            <select required class="select-form" name="month" id="month">
                                <option value="0">เดือน/month</option>
                                @php $monthThai = \App\Helper\ThaiLocal::month(); @endphp
                                @for($month = 1;$month <= 12 ; $month++)
                                    <option value="{{$month}}">{{$monthThai[$month-1]}} ({{$month}})</option>
                                @endfor
                            </select>
                            <select required class="select-form" name="year" id="year">
                                <option value="0">ปี/year</option>
                                @php $now = \Carbon\Carbon::now();  ;@endphp
                                @for($year = $now->year - 80 ; $year <= ($now->year - 7) ; $year++)
                                    <option value="{{$year}}">{{$year+543}} ({{$year}})</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-lg-12 text-center">
                            <button class="btn btn-primary"> ค้นหา / search</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">

        $('#member_search').on('submit', function() {


           let year = $('#year').val();

            let month = $('#month').val();

            let day = $('#day').val();

            let name = $('#name').val();

            let lastname = $('#lastname').val();


            if(year == 0 || month == 0 || day == 0 || name == "" || lastname == ""){

                Swal.fire({
                    title: 'Error!',
                    text: "กรุณากรอกข้อมูลให้ครบถ้วน / Please fill all input fields",
                    icon: 'error',
                })

                return false;
            }
        });
    </script>

@endsection
