@extends('layouts.master')

@section('content')
    <style>
        .btn-center {
            display: block;
            text-align: center
        }
    </style>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                @php $location = $course->location()->first(); @endphp
                <h3>{{$location->name}}</h3>
                <h3>{{$course->category}}</h3>
                <h3>{{$course->courseDateTxt}}</h3>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 p-0 text-center">
                <img style="width: 100%; max-width: 500px" src="{{asset("storage/images/".$location->image)}}"></img>
            </div>
        </div>
        <form action="{{url("member/search")}}" method="post" id="member_search">
            @csrf
            <input type="hidden" name="course" value="{{$course->id}}">
            <div class="row justify-content-center " style="padding-top: 10px; ">
                <div class="col-lg-6 col-md-12 thing">
                    <div class="row ">
                        <div class="col-12 text-center">
                            ระบบไม่พบข้อมูลเดิมของท่าน <br>
                            หากท่านต้องการสร้างข้อมูลใหม่ กดปุ่มตกลง <br>
                            The system cannot find your data  <br>
                            If you want to create new one, click confirm
                        </div>
                    </div>
                    <div class="row pt-2 justify-content-center">
                        <div class="col-lg-12 col-md-12 text-center">
                            ชื่อจริง-นามสกุล/ firstname - lastname
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="row justify-content-center">
                                <div class="col-lg-5 col-md-6">
                                    <input name="name" value="{{$member->name}}" id="name" class="form form-control" maxlength="50" type="text"
                                           placeholder="ชื่อจริง/Firstname">
                                </div>
                                <div class="col-lg-5 col-md-6 ">
                                    <input name="lastname" value="{{$member->surname}}" id="lastname" class="form form-control " maxlength="50" type="text"
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
                            <select class="select-form" name="day" id="day">
                                <option value="0">วัน/day</option>
                                @for($day = 1;$day <= 31 ; $day++)
                                    <option @if($member->birthdate->day == $day) selected @endif
                                            value="{{$day}}">{{$day}}</option>
                                @endfor
                            </select>
                            <select class="select-form" name="month" id="month">
                                <option value="0">เดือน/month</option>
                                @php $monthThai = \App\Helper\ThaiLocal::month(); @endphp
                                @for($month = 1;$month <= 12 ; $month++)
                                    <option @if($member->birthdate->month == $month) selected @endif
                                        value="{{$month}}">{{$monthThai[$month-1]}} ({{$month}})</option>
                                @endfor
                            </select>
                            <select class="select-form" name="year" id="year">
                                <option value="0">ปี/year</option>
                                @php $now = \Carbon\Carbon::now();  ;@endphp
                                @for($year = $now->year - 80 ; $year <= ($now->year - 7) ; $year++)
                                    <option @if($member->birthdate->year == $year) selected @endif
                                        value="{{$year}}">{{$year+543}} ({{$year}})</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row pt-2">
                        <div class="col-lg-12 text-center">
                            <button  name="cancel" value="cancel" class="btn btn-warning"> ย้อนกลับ / back </button>
                            <button name="confirm" value="confirm" class="btn btn-primary"> ตกลง / confirm </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">

        $('#member_search').submit(function(){
           let year = $('#year').val();

            let month = $('#month').val();

            let day = $('#day').val();

            let name = $('#name').val();

            let lastname = $('#lastname').val();


            if(year === 0 || month === 0 || day === 0 || name === "" || lastname === ""){

                let timerInterval
                Swal.fire({
                    title: 'Error!',
                    text: "กรุณากรอกข้อมูลให้ครบถ้วน / Please fill all input fields",
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => {
                            b.textContent = Swal.getTimerLeft()
                        }, 100)
                    },
                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                }).then((result) => {
                    /* Read more about handling dismissals below */
                    if (result.dismiss === Swal.DismissReason.timer) {
                        console.log('I was closed by the timer')
                    }
                })

                return false;
            }
        });
    </script>

@endsection
