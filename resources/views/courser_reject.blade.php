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

            <div class="row justify-content-center " style="padding-top: 10px; ">
                <div class="col-lg-6 col-md-12 thing">
                    <div class="row ">
                        <div class="col-12 text-center">
                            ระบบปฏิเสธการลงทะเบียนของท่าน {{$course->courseName}} <br>
                        </div>
                    </div>
                    <div class="row pt-2 justify-content-center pl-10 pr-10">

                        @foreach($message as $mess)
                           <div class="col-lg-8 col-md-12"  style="color: #740005; text-align: center">  {{$mess}} </div>
                        @endforeach
                    </div>
                    <div class="row pt-2 justify-content-center">
                        <input type="hidden" name="course_id" value="{{$course->id}}">
                        <input type="hidden" name="member_id" value="{{$member->id}}">
                        <table class="table">
                            <tr>
                                <td class="table-light"  colspan="5">ประวัติการเข้าคอร์ส/ Your course history</td>
                            </tr>
                            <tr>
                                <td style="width: 25%">วันที่</td>
                                <td style="width: 25%">ชื่อคอร์ส</td>
                                <td style="width: 25%">สถานที่</td>
                                <td >หน้าที่</td>
                                <td>สถานนะ</td>
                            </tr>
                            @foreach($applies as $apply)
                                @php $applyCourse = $apply->course; @endphp
                                @php $applyLocation = $applyCourse->location()->first(); @endphp
                                <tr>
                                    <td>{{$applyCourse->courseDateTxt}}</td>
                                    <td>{{$applyCourse->category}}</td>
                                    <td>{{$applyLocation->name}}</td>
                                    <td>{{$apply->role}}</td>
                                    <td>{{$apply->state}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
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
