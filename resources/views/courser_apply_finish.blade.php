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
        <form action="{{url("course/apply/finish")}}" method="post" id="course_confirm">
            @csrf
            <div class="row justify-content-center " style="padding-top: 10px; ">
                <div class="col-lg-6 col-md-12 thing">
                    <div class="row ">
                        <div class="col-12 text-center">
                             {{$course->courseName}} ณ {{$course->courseLocation->name}}
                        </div>
                    </div>
                    <div class="row pt-2 justify-content-center">
                        <input type="hidden" name="course_id" value="{{$course->id}}">
                        <input type="hidden" name="member_id" value="{{$member->id}}">
                        <input type="hidden" name="apply_id" value="{{$my_apply->id}}">
                        <table class="table">
                            <tr>
                                <td class="table-light" colspan="2">ผลสมัครคอร์สปฏิบัติธรรม</td>
                            </tr>
                            <tr>
                                <td colspan="2" class='p-5'>
                                    <div class="m-2">
                                        <h5>Key Message</h5>
                                        <ul>
                                            <li>
                                                มูลนิธิได้รับใบสมัคร และบันทึกคําขอยืนใบสมัครของท่านไว้แล้ว โดยต้องได้รับโทรศัพท์สัมภาษณ์ และ ยืนยันการเข้าคอร์สจากเจ้าหน้าทีมูลนิธิ
                                                <br>         หมายเลข   02-117-4063 ถึง 64  ราว 1-3 สัปดาห์ก่อนเข้าคอร์ส  *เท่านัน* จึงเป็นผู้มีสิทธิเข้ารับการอบรม
                                            </li>
                                            <li>
                                                กรุณาอ่านหลักเกณฑ์เกียวกับการรับสมัคร กฎ และ ระเบียบของธรรมสถานโดยละเอียด
                                            </li>
                                            <li>
                                                คอร์สเตโชฯ แจ้งเรื่องวินัยภาวนา ซึงต้องมีวินัยในการภาวนา วันละ 1 ชัวโมง 5 วันต่อสัปดาห์ ต่อเนืองกันอย่างน้อย 1 เดือน ก่อนเข้ารับการอบรม
                                            </li>
                                            <li>
                                                คอร์สเตโชฯ แจ้งเรืองการยกเลิกล่วงหน้าไม่น้อยกว่า 10 วัน การไม่มาเข้ารับการอบรมโดยไม่แจ้ง หรือ การออกจากคอร์สก่อนโดยไม่ได้รับอนุญาต ถือเป็นการขัดความทาง
                                            </li>
                                            <li>
                                                ธรรมของผู้อืนและจะถูกตัดสิทธิไม่ให้เข้ารับการอบรมไม่น้อยกว่า 2 ปี
                                            </li>
                                            <li>
                                                แจ้งเรื่องอืนๆ เช่น การปฏิบัติตัวในช่วงโควิด และ การนํากระติกนํามาเอง
                                            </li>
                                            <li>
                                                ขอบคุณ
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; width: 35%">สมัครคอร์ส/course</td>
                                <td>
                                    {{$course->courseName}}
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; width: 35%">หน้าที/Role</td>
                                <td>
                                    <select disabled name="role" id="role" class="form-select" style="width: 70%">
                                        <option @if($my_apply->role == 'ผู้เข้าอบรม') selected
                                                @endif value="ผู้เข้าอบรม">ผู้เข้าอบรม/ Attendee
                                        </option>
                                        <option @if($my_apply->role == 'ธรรมะบริกร') selected @endif value="ธรรมะบริกร">
                                            ธรรมะบริกร
                                        </option>
                                        <option @if($my_apply->role == 'ผู้ช่วยแม่ครัว') selected
                                                @endif value="ผู้ช่วยแม่ครัว">ผู้ช่วยแม่ครัว
                                        </option>
                                        <option @if($my_apply->role == 'แม่ครัว') selected @endif value="แม่ครัว">
                                            แม่ครัว
                                        </option>
                                        <option @if($my_apply->role == 'ผู้ดูแลคอร์ส') selected
                                                @endif value="ผู้ดูแลคอร์ส">ผู้ดูแลคอร์ส
                                        </option>
                                        <option @if($my_apply->role == 'อาจารย์ผู้ช่วยสอน') selected
                                                @endif value="อาจารย์ผู้ช่วยสอน">อาจารย์ผู้ช่วยสอน
                                        </option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; width: 35%">ที่พัก/shelter</td>
                                <td>
                                    <select disabled name="role" id="role" class="form-select" style="width: 70%">
                                        <option @if($my_apply->shelter == 'ทั่วไป') selected @endif value="ทั่วไป">
                                            ทั่วไป/normal
                                        </option>
                                        <option @if($my_apply->shelter == 'กุฏิพิเศษ') selected
                                                @endif value="กุฏิพิเศษ">กุฏิพิเศษ/special
                                        </option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; width: 35%">บันทึกเพิ่มเติม/Remark</td>
                                <td>
                                    <input disabled value="{{$my_apply->remark}}" style="width: 70%" name="remark" id="remark"
                                           class="form form-control" maxlength="100" type="text"
                                           placeholder="บันทึกเพิ่มเติม/Remark">
                                </td>
                            </tr>
                            <tr>
                                <td class="table-light" colspan="2">
                                    ใบสมัคร/your application
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <a href="{{asset($my_apply->application)}}"> เปิดดูใบสมัคร</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row justify-content-center " style="padding-top: 10px; ">
                    <div class="col-lg-6 col-md-12 thing">
                        <div class="row pt-2 justify-content-center">
                            <table class="table">
                                <tr>
                                    <td class="table-light" colspan="2">ข้อมูลผู้สมัคร/Personal</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ชื่อจริง/Firstname <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled value="{{$member->name}}" style="width: 70%" name="name"
                                               id="name" class="form form-control" maxlength="50" type="text"
                                               placeholder="ชื่อจริง/Firstname">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">นามสกุล/Surname <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled value="{{$member->surname}}" style="width: 70%"
                                               name="surname" id="surname" class="form form-control" maxlength="50"
                                               type="text"
                                               placeholder="นามสกุล/Surname">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ชื่อเล่น/Nickname <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled value="{{$member->nickname}}" style="width: 70%"
                                               name="nickname" id="nickname" class="form form-control"
                                               maxlength="50" type="text"
                                               placeholder="ชื่อเล่น/Nickname">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">เพศ/Gender <i class="req-in">*</i>
                                    </td>
                                    <td>
                                        <select disabled name="gender" id="gender" class="form-select"
                                                style="width: 70%">
                                            <option @if($member->gender == 'หญิง') selected @endif value="หญิง">
                                                หญิง/Female
                                            </option>
                                            <option @if($member->gender == 'ชาย') selected @endif value="ชาย">
                                                ชาย/Male
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">พุทธบริษัท/Buddhist <i
                                            class="req-in">*</i>
                                    </td>
                                    <td>
                                        <select required name="buddhism" id="buddhism" class="form-select"
                                                style="width: 70%">
                                            <option @if($member->buddhism == 'ฆราวาส') selected @endif value="หญิง">
                                                ฆราวาส/secular
                                            </option>
                                            <option @if($member->buddhism == 'แม่ชี') selected @endif value="ชาย">
                                                แม่ชี
                                            </option>
                                            <option @if($member->buddhism == 'ภิกษุ') selected @endif value="ชาย">
                                                ภิกษุ
                                            </option>
                                            <option @if($member->buddhism == 'สามเณร') selected @endif value="ชาย">
                                                สามเณร
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">วันเกิด/Birthday <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <select disabled class="select-form" name="day" id="day">
                                            <option value="0">วัน</option>
                                            @for($day = 1;$day <= 31 ; $day++)
                                                <option @if($member->birthdate->day == $day) selected
                                                        @endif value="{{$day}}">{{$day}}</option>
                                            @endfor
                                        </select>
                                        <select disabled class="select-form" name="month" id="month">
                                            <option value="0">เดือน</option>
                                            @for($month = 1;$month <= 12 ; $month++)
                                                <option @if($member->birthdate->month == $month) selected
                                                        @endif value="{{$month}}">{{$month}}</option>
                                            @endfor
                                        </select>
                                        <select disabled class="select-form" name="year" id="year">
                                            <option value="0">ปี</option>
                                            @php $now = \Carbon\Carbon::now();  ;@endphp
                                            @for($year = $now->year - 80 ; $year <= ($now->year - 7) ; $year++)
                                                <option @if($member->birthdate->year == $year) selected
                                                        @endif value="{{$year}}">{{$year+543}} ({{$year}})
                                                </option>
                                            @endfor
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">สัญชาติ/Nationality <i
                                            class="req-in">*</i>
                                    </td>
                                    <td>
                                        <select disabled class="select-form" style="width: 70%" name="nationality"
                                                id="nationality">
                                            <option @if($member->nationality == 'ไทย') selected @endif value="ไทย">
                                                ชาวไทย/Thai
                                            </option>
                                            <option @if($member->nationality == 'ต่างชาติ') selected
                                                    @endif value="ต่างชาติ">ชาวต่างชาติ/Foreigner
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;" colspan="2">ที่อยู่/address</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ประเทศ/Country <i class="req-in">*</i>
                                    </td>
                                    <td>
                                        <input disabled value="{{$member->country}}" style="width: 70%"
                                               name="country" id="country" class="form form-control" maxlength="50"
                                               type="text"
                                               placeholder="ประเทศ/Country">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">จังหวัด/Province</td>
                                    <td>
                                        <input disabled value="{{$member->province}}" style="width: 70%"
                                               name="province" id="province" class="form form-control"
                                               maxlength="50" type="text"
                                               placeholder="จังหวัด/Province">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;" colspan="2">ติดต่อ/contact</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number (1) <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled value="{{$member->phone}}" style="width: 70%" name="phone"
                                               id="phone" class="form form-control" maxlength="50" type="text"
                                               placeholder="เบอร์โทรศัพท์/Phone number (1)">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number (2)</td>
                                    <td>
                                        <input disabled value="{{$member->phone_2}}" style="width: 70%"
                                               name="phone_2" id="phone_2" class="form form-control" maxlength="50"
                                               type="text"
                                               placeholder="เบอร์โทรศัพท์/Phone number (2)">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">อีเมล/email</td>
                                    <td>
                                        <input disabled value="{{$member->email}}" style="width: 70%" name="email"
                                               id="email" class="form form-control" maxlength="50" type="text"
                                               placeholder="อีเมล/email">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">Line ID</td>
                                    <td>
                                        <input disabled value="{{$member->line}}" style="width: 70%" name="line"
                                               id="line" class="form form-control" maxlength="50" type="text"
                                               placeholder="Line ID">
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: center;" colspan="2">ประวัติ/Profile</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ระดับการศึกษา/Education<i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled value="{{$member->education}}" style="width: 70%"
                                               name="education" id="education" class="form form-control"
                                               maxlength="50" type="text"
                                               placeholder="ระดับการศึกษา/Education Level">
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right; width: 35%">อาชีพ/Career</td>
                                    <td>
                                        <input disabled value="{{$member->career}}" style="width: 70%" name="career"
                                               id="career" class="form form-control" maxlength="50" type="text"
                                               placeholder="อาชีพ/Career">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ที่ทำงาน/Organization</td>
                                    <td>
                                        <input value="{{$member->organization}}" style="width: 70%"
                                               name="organization" id="organization" class="form form-control"
                                               maxlength="50" type="text"
                                               placeholder="ที่ทำงาน/Organization">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ความสามารถพิเศษ/Expertise</td>
                                    <td>
                                        <input disabled value="{{$member->expertise}}" style="width: 70%"
                                               name="expertise" id="expertise" class="form form-control"
                                               maxlength="50" type="text"
                                               placeholder="ความสามารถพิเศษ/Expertise">
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <tr>
                                    <td style="text-align: center;" colspan="2">ประวัติการฝึกปฏิบัติธรรม/Dharma
                                        practice profile
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 40%">ท่านเคยปฏิบัติธรรมมาก่อนหรือไม่ <br>
                                        Do you have any experience in dharma course.
                                    </td>
                                    <td style="text-align: left; justify-content: left;">
                                        <select disabled class="select-form" style="width:200px; text-align: left"
                                                name="dharma_ex" id="dharma_ex">
                                            <option value="ไม่เคย">ไม่เคย/no</option>
                                            <option value="เคย">เคย/yes</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 40%">โปรดระบุการปฏิบัติธรรม<br>Please
                                        describe your previous dharma course.
                                    </td>
                                    <td>
                                        <input disabled style="width: 100%" name="dharma_ex_desc"
                                               id="dharma_ex_desc" class="form form-control" maxlength="50"
                                               type="text"
                                               placeholder="การปฏิบัติธรรม/Dharma course">
                                    </td>
                                </tr>

                                <tr>
                                    <td style="text-align: right; width: 40%">ท่านทราบข่าวการรับสมัครจากทีใด<br>Where
                                        did you know about this course?
                                    </td>
                                    <td>
                                        <input disabled style="width: 100%" name="know_source" id="know_source"
                                               class="form form-control" maxlength="50" type="text"
                                               placeholder="ทราบจากที่ใด/source of this course">
                                    </td>
                                </tr>
                            </table>

                            <table class="table">
                                <tr>
                                    <td style="text-align: center;" colspan="2">ชื่อผู้ติดต่อได้กรณีฉุกเฉิน/Emergency
                                        Contact Person
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ชื่อจริง/Firstname <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled required value="{{$member->name_emergency}}"
                                               style="width: 70%" name="name_emergency" id="name_emergency"
                                               class="form form-control" maxlength="50" type="text"
                                               placeholder="ชื่อจริง/Firstname">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">นามสกุล/Surname <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled required value="{{$member->surname_emergency}}"
                                               style="width: 70%" name="surname_emergency" id="surname_emergency"
                                               class="form form-control" maxlength="50" type="text"
                                               placeholder="นามสกุล/Surname">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">เบอร์โทรศัพท์<br>Phone number <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled required value="{{$member->phone_emergency}}"
                                               style="width: 70%" name="phone_emergency" id="phone_emergency"
                                               class="form form-control" maxlength="50" type="text"
                                               placeholder="เบอร์โทรศัพท์/Phone number">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right; width: 35%">ความสัมพันธ์/Relationship <i
                                            class="req-in">*</i></td>
                                    <td>
                                        <input disabled required value="{{$member->relation_emergency}}"
                                               style="width: 70%" name="relation_emergency" id="relation_emergency"
                                               class="form form-control" maxlength="50" type="text"
                                               placeholder="ความสัมพันธ์/Relationship">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="row pt-2">
                            <div class="col-lg-12 text-center">
                                <button class="btn btn-primary"> ยืนยันการสมัครคอร์สปฏิบัติธรรม / Confirm Register Dharma Course
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>

    <script type="text/javascript">

        $('#member_search').submit(function () {
            let year = $('#year').val();

            let month = $('#month').val();

            let day = $('#day').val();

            let name = $('#name').val();

            let lastname = $('#lastname').val();


            if (year === 0 || month === 0 || day === 0 || name === "" || lastname === "") {

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
    <style>

        .select-form {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            -moz-padding-start: calc(0.75rem - 3px);
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            appearance: none;
        }


        .thing {
            margin-top: 30px;
            padding: 1rem;
            box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.11),
            0 5px 15px 0 rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
            border-radius: 0.5rem;

            border-left: 0 solid #00ff99;
            transition: border-left 300ms ease-in-out, padding-left 300ms ease-in-out;
        }

        .thing:hover {
            padding-left: 0.5rem;
            border-left: 0.5rem solid #00ff99;
        }

        .thing > :first-child {
            margin-top: 0;
        }

        .thing > :last-child {
            margin-bottom: 0;
        }

    </style>

@endsection
