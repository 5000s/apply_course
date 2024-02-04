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
        <form action="{{url("course/apply")}}" method="post" id="course_apply">
            @csrf
            <div class="row justify-content-center " style="padding-top: 10px; ">
                <div class="col-lg-6 col-md-12 thing">
                    <div class="row ">
                        <div class="col-12 text-center">
                            กรุณากรอกข้อมูลให้ครบถ้วนเพื่อลงชื่อสมัคร {{$course->courseName}} <br>
                            Please fill the form for register
                        </div>
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


                        <table class="table">
                            <tr>
                                <td class="table-light"  colspan="2">ข้อมูลผู้สมัคร/Personal</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ชื่อจริง/Firstname <i class="req-in">*</i></td>
                                <td>
                                    <input required  @if($old_student == true)  disabled  @endif value="{{$member->name}}" style="width: 100%" name="name" id="name" class="form form-control" maxlength="50" type="text"
                                            placeholder="ชื่อจริง/Firstname">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">นามสกุล/Surname <i class="req-in">*</i></td>
                                <td>
                                    <input required  @if($old_student == true)  disabled  @endif value="{{$member->surname}}" style="width: 100%" name="surname" id="surname" class="form form-control" maxlength="50" type="text"
                                           placeholder="นามสกุล/Surname">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ชื่อเล่น/Nickname <i class="req-in">*</i></td>
                                <td>
                                    <input required @if($old_student == true)  disabled  @endif value="{{$member->nickname}}" style="width: 100%" name="nickname" id="nickname" class="form form-control" maxlength="50" type="text"
                                           placeholder="ชื่อเล่น/Nickname">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">เพศ/Gender <i class="req-in">*</i></td>
                                <td>
                                    <select required @if($old_student == true)  disabled  @endif name="gender" id="gender" class="form-select" style="width: 100%">
                                        <option @if($member->gender == 'หญิง') selected @endif value="หญิง">หญิง/Female</option>
                                        <option @if($member->gender == 'ชาย') selected @endif value="ชาย">ชาย/Male</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">พุทธบริษัท/Buddhist <i class="req-in">*</i></td>
                                <td>
                                    <select required name="buddhism" id="buddhism" class="form-select" style="width: 100%">
                                        <option @if($member->buddhism == 'ฆราวาส') selected @endif value="ฆราวาส">ฆราวาส/secular</option>
                                        <option @if($member->buddhism == 'แม่ชี') selected @endif value="แม่ชี">แม่ชี</option>
                                        <option @if($member->buddhism == 'ภิกษุ') selected @endif value="ภิกษุ">ภิกษุ</option>
                                        <option @if($member->buddhism == 'สามเณร') selected @endif value="สามเณร">สามเณร</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">วันเกิด/Birthday <i class="req-in">*</i></td>
                                <td>
                                    <select required @if($old_student == true)  disabled  @endif class="select-form" name="day" id="day">
                                        <option value="0">วัน</option>
                                        @for($day = 1;$day <= 31 ; $day++)
                                            <option @if($member->birthdate->day == $day) selected  @endif value="{{$day}}">{{$day}}</option>
                                        @endfor
                                    </select>
                                    <select required  @if($old_student == true)  disabled  @endif class="select-form" name="month" id="month">
                                        <option value="0">เดือน</option>
                                        @for($month = 1;$month <= 12 ; $month++)
                                            <option @if($member->birthdate->month == $month) selected @endif value="{{$month}}">{{$month}}</option>
                                        @endfor
                                    </select>
                                    <select required  @if($old_student == true)  disabled  @endif class="select-form" name="year" id="year">
                                        <option value="0">ปี</option>
                                        @php $now = \Carbon\Carbon::now();  ;@endphp
                                        @for($year = $now->year - 80 ; $year <= ($now->year - 7) ; $year++)
                                            <option  @if($member->birthdate->year == $year) selected  @endif value="{{$year}}">{{$year+543}} ({{$year}})</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">สัญชาติ/Nationality <i class="req-in">*</i></td>
                                <td>
                                    <select required @if($old_student == true)  disabled  @endif class="select-form" style="width: 100%"  name="nationality" id="nationality">
                                        <option  @if($member->nationality == 'ไทย') selected  @endif value="ไทย">ชาวไทย/Thai</option>
                                        <option @if($member->nationality == 'ต่างชาติ') selected  @endif value="ต่างชาติ">ชาวต่างชาติ/Foreigner</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="table-light"  colspan="2">ที่อยู่/address</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ประเทศ/Country <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->country}}" style="width: 100%" name="country" id="country" class="form form-control" maxlength="50" type="text"
                                           placeholder="ประเทศ/Country">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">จังหวัด/Province</td>
                                <td>
                                    <input value="{{$member->province}}" style="width: 100%" name="province" id="province" class="form form-control" maxlength="50" type="text"
                                           placeholder="จังหวัด/Province">
                                </td>
                            </tr>
                            <tr>
                                <td  class="table-light"  colspan="2">ติดต่อ/contact</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number (1) <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->phone}}" style="width: 100%" name="phone" id="phone" class="form form-control" maxlength="50" type="text"
                                           placeholder="เบอร์โทรศัพท์/Phone number (1)">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number (2)</td>
                                <td>
                                    <input value="{{$member->phone_2}}" style="width: 100%" name="phone_2" id="phone_2" class="form form-control" maxlength="50" type="text"
                                           placeholder="เบอร์โทรศัพท์/Phone number (2)">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">อีเมล/email</td>
                                <td>
                                    <input  value="{{$member->email}}" style="width: 100%" name="email" id="email" class="form form-control" maxlength="50" type="text"
                                           placeholder="อีเมล/email">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">Line ID</td>
                                <td>
                                    <input  value="{{$member->line}}" style="width: 100%" name="line" id="line" class="form form-control" maxlength="50" type="text"
                                           placeholder="Line ID">
                                </td>
                            </tr>

                            <tr>
                                <td  class="table-light"  colspan="2">ประวัติ/Profile</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ระดับการศึกษา/Education<i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->degree}}" style="width: 100%" name="degree" id="degree" class="form form-control" maxlength="50" type="text"
                                           placeholder="ระดับการศึกษา/Education Level">
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; width: 35%">อาชีพ/Career</td>
                                <td>
                                    <input  value="{{$member->career}}" style="width: 100%" name="career" id="career" class="form form-control" maxlength="50" type="text"
                                           placeholder="อาชีพ/Career">
                                </td>
                            </tr>
                            <tr>
                                    <td style="text-align: right; width: 35%">ที่ทำงาน/Organization</td>
                                <td>
                                    <input value="{{$member->organization}}" style="width: 100%" name="organization" id="organization" class="form form-control" maxlength="50" type="text"
                                           placeholder="ที่ทำงาน/Organization">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ความสามารถพิเศษ/Expertise</td>
                                <td>
                                    <input value="{{$member->expertise}}" style="width: 100%" name="expertise" id="expertise" class="form form-control" maxlength="50" type="text"
                                           placeholder="ความสามารถพิเศษ/Expertise">
                                </td>
                            </tr>
                        </table>
                        <table class="table">
                             <tr>
                                    <td   class="table-light"  colspan="2">ประวัติการฝึกปฏิบัติธรรม/Dharma practice profile</td>
                             </tr>
                            <tr>
                               <td style="text-align: right; width: 40%">ท่านเคยปฏิบัติธรรมมาก่อนหรือไม่ <br> Do you have any experience in dharma course. </td>
                               <td style="text-align: left; justify-content: left;"  >
                                  <select class="select-form" style="width:200px; text-align: left" name="dharma_ex" id="dharma_ex">
                                       <option value="ไม่เคย">ไม่เคย/no</option>
                                       <option value="เคย">เคย/yes</option>
                                   </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 40%">โปรดระบุการปฏิบัติธรรม<br>Please describe your previous dharma course. </td>
                                <td>
                                    <input style="width: 100%" name="dharma_ex_desc" id="dharma_ex_desc" class="form form-control" maxlength="50" type="text"
                                           placeholder="การปฏิบัติธรรม/Dharma course">
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; width: 40%">ท่านทราบข่าวการรับสมัครจากทีใด<br>Where did you know about this course? </td>
                                <td>
                                    <input style="width: 100%" name="know_source" id="know_source" class="form form-control" maxlength="50" type="text"
                                           placeholder="ทราบจากที่ใด/source of this course">
                                </td>
                            </tr>
                        </table>

                        <table class="table">
                            <tr>
                                <td class="table-light" colspan="2">ชือผู้ติดต่อได้กรณีฉุกเฉิน/Emergency Contact Person</td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ชื่อจริง/Firstname <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->name_emergency}}" style="width: 100%" name="name_emergency" id="name_emergency" class="form form-control" maxlength="50" type="text"
                                           placeholder="ชื่อจริง/Firstname">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">นามสกุล/Surname <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->surname_emergency}}" style="width: 100%" name="surname_emergency" id="surname_emergency" class="form form-control" maxlength="50" type="text"
                                           placeholder="นามสกุล/Surname">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->phone_emergency}}" style="width: 100%" name="phone_emergency" id="phone_emergency" class="form form-control" maxlength="50" type="text"
                                           placeholder="เบอร์โทรศัพท์/Phone number">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">ความสัมพันธ์/Relationship <i class="req-in">*</i></td>
                                <td>
                                    <input required value="{{$member->relation_emergency}}" style="width: 100%" name="relation_emergency" id="relation_emergency" class="form form-control" maxlength="50" type="text"
                                           placeholder="ความสัมพันธ์/Relationship">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="row pt-2">
                        <div class="col-lg-12 text-center">
                            <button class="btn btn-primary"> ยืนยันข้อมูลผู่้สมัคร / Confirm Personal Information</button>
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
