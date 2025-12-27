@extends('layouts.bodhi')

@section('title', ($lang ?? 'th') === 'th' ? 'บันทึกคำขอสมัครคอร์สเรียบร้อย' : 'Application Saved')

@section('content')
    @php
        $lang = $lang ?? 'th';

        $t = [
            'th' => [
                'header_success' => 'บันทึกคำขอสมัครคอร์สเรียบร้อย',
                'dear_applicant' => 'เรียนท่านผู้สมัครหรือตัวแทน ที่นับถือ',
                'warning_msg' => 'ท่านได้รับการยื่นการสมัครแล้ว <br>
                                และได้รับ การยืนยันว่าท่านได้รับการอบรม <br><br>
                                หากท่านต้องการยกเลิกการสมัคร โปรดติดต่อ <br>
                                <strong>02-117-4063 ถึง 64</strong> <br>
                                ราว 1-3 สัปดาห์ก่อนเข้าคอร์ส <br>
                                เพื่อยกเลิกการสมัคร',
                'read_rules' => '*** กรุณาอ่านหลักเกณฑ์เกี่ยวกับการรับสมัคร กฎ และ ระเบียบของธรรมสถานโดยละเอียด ได้ที่',
                'strict_follow' => 'และปฏิบัติตามโดยเคร่งครัด *',
                'rule_clothing' =>
                    '<strong>เครื่องแต่งกาย :</strong> ชุดขาวปฏิบัติธรรม หรือเสื้อขาวล้วนและผ้าถุงหรือกางเกงขายาวสีขาว "ห้ามใส่เสื้อยืด"',
                'rule_appearance' =>
                    '<strong>ห้ามผู้ทำสีผมและทาเล็บทุกชนิด</strong> เข้ารับการอบรมปฏิบัติภาวนา และต้องตัดเล็บสั้น รวมทั้งหากผมยาวจะต้องรวบผมให้เรียบร้อย',
                'details_header' => 'รายละเอียด',
                'name_label' => 'ชื่อผู้สมัคร :',
                'course_label' => 'คอร์สที่สมัคร :',
                'date_prefix' => 'วันที่',
                'note_header' => 'หมายเหตุ :',
                'note_cancel' =>
                    'หากท่านได้รับการสัมภาษณ์และได้ตอบรับการอบรมแล้ว แต่ไม่สามารถมาเข้าคอร์สได้ <strong>*กรุณาแจ้งยกเลิกล่วงหน้าไม่น้อยกว่า 10 วัน</strong> การไม่มาเข้ารับการอบรมโดยไม่แจ้ง หรือ การออกจากคอร์สก่อนโดยไม่ได้รับอนุญาต <strong>**ถือเป็นการขัดขวางทางธรรมของผู้อื่นและจะถูกตัดสิทธิ์ไม่ให้เข้ารับการอบรมไม่น้อยกว่า 2 ปี</strong>',
                'note_water' =>
                    '*** ขอความร่วมมือให้ผู้เข้าอบรมทุกท่าน เตรียมกระบอกน้ำส่วนตัวเพื่อใส่น้ำดื่มระหว่างการเข้าอบรม ธรรมสถานงดการแจกน้ำดื่มบรรจุในขวดพลาสติก ***',
                'regards' => 'ด้วยความนับถือ',
                'team' => 'ทีมงาน',
                'back_home' => 'กลับสู่หน้าหลัก',
            ],
            'en' => [
                'header_success' => 'Application Request Saved Successfully',
                'dear_applicant' => 'Dear Applicant',
                'warning_msg' => 'Your application has been submitted <br>
                                and you are confirmed for the training. <br><br>
                                If you wish to cancel your application, please contact <br>
                                <strong>02-117-4063 to 64</strong> <br>
                                about 1-3 weeks before the course starts <br>
                                to cancel your application.',
                'read_rules' => '*** Please read the rules and regulations details at',
                'strict_follow' => 'and follow strictly *',
                'rule_clothing' =>
                    '<strong>Clothing :</strong> White practice set or plain white shirt and white sarong/trousers. "No T-shirts allowed"',
                'rule_appearance' =>
                    '<strong>No colored hair or painted nails</strong> allowed. Nails must be short. Long hair must be tied properly.',
                'details_header' => 'Details',
                'name_label' => 'Applicant Name :',
                'course_label' => 'Applied Course :',
                'date_prefix' => 'Date',
                'note_header' => 'Note :',
                'note_cancel' =>
                    'If you are accepted but cannot attend, <strong>*Please cancel at least 10 days in advance.</strong> Failure to attend without notice or leaving early without permission <strong>**is considered obstructing others and will be banned for at least 2 years.</strong>',
                'note_water' =>
                    '*** Please bring your own personal water bottle. The center does not distribute bottled water. ***',
                'regards' => 'Best Regards,',
                'team' => 'Team',
                'back_home' => 'Back to Home',
            ],
        ];
        $txt = $t[$lang];

        $member = $apply->member;
        $course = $apply->course;

        $formatDate = function ($start, $end) use ($lang) {
            $s = \Carbon\Carbon::parse($start);
            $e = \Carbon\Carbon::parse($end);

            if ($lang === 'th') {
                $s->locale('th');
                $e->locale('th');
                $y = $s->year + 543;
                if ($s->isSameDay($e)) {
                    return $s->translatedFormat('j F') . ' ' . $y;
                }
                if ($s->isSameMonth($e)) {
                    return $s->day . '-' . $e->translatedFormat('j F') . ' ' . $y;
                }
                return $s->translatedFormat('j F') . ' - ' . $e->translatedFormat('j F') . ' ' . $y;
            } else {
                $s->locale('en');
                $e->locale('en');
                $y = $s->year;
                if ($s->isSameDay($e)) {
                    return $s->translatedFormat('j F Y');
                }
                if ($s->isSameMonth($e)) {
                    return $s->day . '-' . $e->translatedFormat('j F Y');
                }
                return $s->translatedFormat('j M') . ' - ' . $e->translatedFormat('j M Y');
            }
        };

        $dateStr = $formatDate($course->date_start, $course->date_end);
        $locationName = $lang === 'th' ? $location->show_name : $location->show_name_en ?? $location->show_name;
        $courseTitle =
            $lang === 'th' ? $courseCategory->show_name : $courseCategory->show_name_en ?? $courseCategory->show_name;
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <div class="mb-3 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                                    class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                </svg>
                            </div>
                            <h2 class="fw-bold text-success">{{ $txt['header_success'] }}</h2>
                        </div>

                        <div class="alert alert-warning border-warning bg-warning bg-opacity-10">
                            <h5 class="alert-heading fw-bold">{{ $txt['dear_applicant'] }}</h5>
                            <p class="mb-0">
                                {!! $txt['warning_msg'] !!}
                            </p>
                        </div>

                        <div class="mb-4">
                            <p>
                                <strong>{{ $txt['read_rules'] }}</strong>
                                <a href="https://bodhidhammayan.org/th/course-saraburi-th/#Anapanasati"
                                    target="_blank">https://bodhidhammayan.org/th/course-saraburi-th/#Anapanasati</a>
                                <strong>{{ $txt['strict_follow'] }}</strong>
                            </p>
                            <ul class="mb-3">
                                <li>{!! $txt['rule_clothing'] !!}</li>
                                <li>{!! $txt['rule_appearance'] !!}</li>
                            </ul>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">{{ $txt['details_header'] }}</h5>
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 140px;">{{ $txt['name_label'] }}</th>
                                            <td>{{ $member->name }} {{ $member->surname }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ $txt['course_label'] }}</th>
                                            <td>

                                                {{ $courseTitle }} <br>
                                                {{ $txt['date_prefix'] }} {{ $dateStr }} <br>
                                                {{ $locationName }}


                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4 text-muted small">
                            <p class="fw-bold mb-1">{{ $txt['note_header'] }}</p>
                            <p>
                                {!! $txt['note_cancel'] !!}
                            </p>
                            <p class="mb-0">
                                {!! $txt['note_water'] !!}
                            </p>
                        </div>

                        <div class="text-center mt-5">
                            <p class="mb-1">{{ $txt['regards'] }}</p>
                            <p class="fw-bold">{{ $txt['team'] }}</p>
                            <a href="https://bodhidhammayan.org/" target="_self"
                                class="btn btn-outline-secondary mt-3">{{ $txt['back_home'] }}</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
