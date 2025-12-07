@extends('layouts.bodhi')

@section('title', 'บันทึกคำขอสมัครคอร์สเรียบร้อย')

@section('content')
    @php
        $member = $apply->member;
        $course = $apply->course;

        $thDate = function ($start, $end) {
            $s = \Carbon\Carbon::parse($start)->locale('th');
            $e = \Carbon\Carbon::parse($end)->locale('th');
            $y = $s->year + 543;

            if ($s->isSameDay($e)) {
                return $s->translatedFormat('j F') . ' ' . $y;
            }
            if ($s->isSameMonth($e)) {
                return $s->day . '-' . $e->translatedFormat('j F') . ' ' . $y;
            }
            return $s->translatedFormat('j F') . ' - ' . $e->translatedFormat('j F') . ' ' . $y;
        };

        $dateStr = $thDate($course->date_start, $course->date_end);
        $locationName = $location->show_name;
        $courseTitle = $courseCategory->show_name;
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
                            <h2 class="fw-bold text-success">บันทึกคำขอสมัครคอร์สเรียบร้อย</h2>
                        </div>

                        <div class="alert alert-warning border-warning bg-warning bg-opacity-10">
                            <h5 class="alert-heading fw-bold">เรียนท่านผู้สมัครหรือตัวแทน ที่นับถือ</h5>
                            <p class="mb-0">
                                **** ทีมงาน เพียงแค่รับใบสมัคร และบันทึกคำขอยื่นใบสมัครของท่านไว้เท่านั้น **** <br>
                                การสมัครนี้ <strong>* ไม่ใช่ *</strong> การยืนยันว่าท่านได้รับการอบรม
                                ท่านต้องได้รับโทรศัพท์สัมภาษณ์ และ ยืนยันการเข้าคอร์สจากเจ้าหน้าที่มูลนิธิหมายเลข
                                <strong>02-117-4063 ถึง 64</strong>
                                ราว 1-3 สัปดาห์ก่อนเข้าคอร์ส <strong>*เท่านั้น*</strong> จึงเป็นผู้มีสิทธิ์เข้ารับการอบรม
                                กรุณาบันทึกเบอร์โทรฯของมูลนิธิไว้เพื่อการติดต่อยืนยันการเข้าคอร์ส
                            </p>
                        </div>

                        <div class="mb-4">
                            <p>
                                <strong>*** กรุณาอ่านหลักเกณฑ์เกี่ยวกับการรับสมัคร กฎ และ ระเบียบของธรรมสถานโดยละเอียด
                                    ได้ที่</strong>
                                <a href="https://bodhidhammayan.org/th/course-saraburi-th/#Anapanasati"
                                    target="_blank">https://bodhidhammayan.org/th/course-saraburi-th/#Anapanasati</a>
                                <strong>และปฏิบัติตามโดยเคร่งครัด *</strong>
                            </p>
                            <ul class="mb-3">
                                <li><strong>เครื่องแต่งกาย :</strong> ชุดขาวปฏิบัติธรรม
                                    หรือเสื้อขาวล้วนและผ้าถุงหรือกางเกงขายาวสีขาว "ห้ามใส่เสื้อยืด"</li>
                                <li><strong>ห้ามผู้ทำสีผมและทาเล็บทุกชนิด</strong> เข้ารับการอบรมปฏิบัติภาวนา
                                    และต้องตัดเล็บสั้น รวมทั้งหากผมยาวจะต้องรวบผมให้เรียบร้อย</li>
                            </ul>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">รายละเอียด</h5>
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 140px;">ชื่อผู้สมัคร :</th>
                                            <td>คุณ{{ $member->name }} {{ $member->surname }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">คอร์สที่สมัคร :</th>
                                            <td>

                                                {{ $courseTitle }} <br>
                                                วันที่ {{ $dateStr }} <br>
                                                {{ $locationName }}


                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4 text-muted small">
                            <p class="fw-bold mb-1">หมายเหตุ :</p>
                            <p>
                                หากท่านได้รับการสัมภาษณ์และได้ตอบรับการอบรมแล้ว แต่ไม่สามารถมาเข้าคอร์สได้
                                <strong>*กรุณาแจ้งยกเลิกล่วงหน้าไม่น้อยกว่า 10 วัน</strong>
                                การไม่มาเข้ารับการอบรมโดยไม่แจ้ง หรือ การออกจากคอร์สก่อนโดยไม่ได้รับอนุญาต
                                <strong>**ถือเป็นการขัดขวางทางธรรมของผู้อื่นและจะถูกตัดสิทธิ์ไม่ให้เข้ารับการอบรมไม่น้อยกว่า
                                    2 ปี</strong>
                            </p>
                            <p class="mb-0">
                                *** ขอความร่วมมือให้ผู้เข้าอบรมทุกท่าน
                                เตรียมกระบอกน้ำส่วนตัวเพื่อใส่น้ำดื่มระหว่างการเข้าอบรม
                                ธรรมสถานงดการแจกน้ำดื่มบรรจุในขวดพลาสติก ***
                            </p>
                        </div>

                        <div class="text-center mt-5">
                            <p class="mb-1">ด้วยความนับถือ</p>
                            <p class="fw-bold">ทีมงาน</p>
                            <a href="https://bodhidhammayan.org/" target="_self"
                                class="btn btn-outline-secondary mt-3">กลับสู่หน้าหลัก</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
