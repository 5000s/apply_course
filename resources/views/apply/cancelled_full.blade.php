{{-- อีเมลแจ้งผู้สมัครที่ถูกยกเลิกเพราะที่นั่งเต็ม (ตอนตัดรอบ 7 วันก่อนเริ่มคอร์ส) --}}
@extends('layouts.bodhi')

@section('title', ($lang ?? 'th') === 'th' ? 'ขออภัย ที่นั่งเต็มแล้ว' : 'Course Fully Booked')

@section('content')
    @php
        $lang = $lang ?? 'th';

        $t = [
            'th' => [
                'header' => 'ขออภัย ที่นั่งเต็มแล้ว',
                'dear_applicant' => 'เรียนท่านผู้สมัครหรือตัวแทน ที่นับถือ',
                'message' => 'ทางมูลนิธิฯ ขออภัยเป็นอย่างยิ่ง คอร์สที่ท่านสมัครมีผู้สมัครเต็มจำนวนที่รับได้แล้ว
                              <strong>ใบสมัครของท่านจึงถูกยกเลิก</strong>
                              ท่านสามารถสมัครคอร์สรอบถัดไปได้ที่เว็บไซต์ของมูลนิธิฯ',
                'contact' => 'สอบถามเพิ่มเติม โทร. :phone หรือ อีเมล info@bodhidhammayan.org',
                'regards' => 'ด้วยความนับถือ',
                'team' => 'ทีมงาน',
            ],
            'en' => [
                'header' => 'Sorry, the course is fully booked',
                'dear_applicant' => 'Dear Applicant',
                'message' => 'We are very sorry. The course you applied for is now fully booked,
                              <strong>so your application has been cancelled.</strong>
                              You are welcome to apply for a later course on the Foundation website.',
                'contact' => 'For more information, please call :phone or email info@bodhidhammayan.org',
                'regards' => 'Best Regards,',
                'team' => 'Team',
            ],
        ];
        $txt = $t[$lang];
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-danger">{{ $txt['header'] }}</h2>
                        </div>

                        <div class="alert alert-danger border-danger bg-danger bg-opacity-10">
                            <h5 class="alert-heading fw-bold">{{ $txt['dear_applicant'] }}</h5>
                            <p class="mb-0">{!! $txt['message'] !!}</p>
                        </div>

                        @include('apply.partials.cancel_details')

                        <p class="text-muted">{{ str_replace(':phone', \App\Services\SmsSender::CONTACT_PHONE, $txt['contact']) }}</p>

                        <div class="text-center mt-4">
                            <p class="mb-1">{{ $txt['regards'] }}</p>
                            <p class="fw-bold">{{ $txt['team'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
