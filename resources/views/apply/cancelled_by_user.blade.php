{{-- หน้าสรุปการยกเลิก (ผู้สมัครกดยกเลิกเอง) ใช้เป็นทั้งหน้าเว็บและอีเมล ($is_email = ซ่อนปุ่ม) --}}
@extends('layouts.bodhi')

@section('title', ($lang ?? 'th') === 'th' ? 'ยกเลิกการสมัครเรียบร้อย' : 'Registration Cancelled')

@section('content')
    @php
        $lang = ($lang ?? 'th') === 'en' ? 'en' : 'th';

        $t = [
            'th' => [
                'header' => 'ยกเลิกการสมัครเรียบร้อย',
                'dear_applicant' => 'เรียนท่านผู้สมัครหรือตัวแทนที่นับถือ',
                'received' => 'ทางมูลนิธิฯ ได้รับคำขอยกเลิกคอร์สจากท่านเรียบร้อยแล้ว',
                'other_courses' => 'หากท่านยังสนใจเข้าร่วมการอบรม สามารถตรวจสอบกำหนดการและสมัครคอร์สอื่น ๆ ได้ที่',
                'hope' => 'ทางมูลนิธิฯ หวังเป็นอย่างยิ่งว่าจะมีโอกาสได้พบท่านในคอร์สต่อ ๆ ไป',
                'regards' => 'ด้วยความนับถือ',
                'team' => 'ทีมงาน',
                'back_home' => 'กลับสู่หน้าหลัก',
                'close' => 'ปิดหน้านี้',
            ],
            'en' => [
                'header' => 'Registration Cancelled',
                'dear_applicant' => 'Dear Applicant',
                'received' => 'The Foundation has received your course cancellation request.',
                'other_courses' => 'If you are still interested in attending, you can check the schedule and apply for other courses at',
                'hope' => 'We sincerely hope to see you in a future course.',
                'regards' => 'Best Regards,',
                'team' => 'Team',
                'back_home' => 'Back to Home',
                'close' => 'Close',
            ],
        ];
        $txt = $t[$lang];
        $siteUrl = 'https://bodhidhammayan.org';
    @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-secondary">{{ $txt['header'] }}</h2>
                        </div>

                        <div class="alert alert-warning border-warning bg-warning bg-opacity-25 fs-5">
                            <p class="fw-bold">{{ $txt['dear_applicant'] }}</p>
                            <p>{{ $txt['received'] }}</p>
                            <p>
                                {{ $txt['other_courses'] }}
                                <a href="{{ $siteUrl }}" target="_blank">bodhidhammayan.org</a>
                            </p>
                            <p class="mb-0">{{ $txt['hope'] }}</p>
                        </div>

                        @include('apply.partials.cancel_details')

                        <div class="text-center mt-4">
                            <p class="mb-1">{{ $txt['regards'] }}</p>
                            <p class="fw-bold">{{ $txt['team'] }}</p>

                            @if (!isset($is_email))
                                <a href="{{ $siteUrl }}/" target="_self"
                                    class="btn btn-outline-secondary mt-3">{{ $txt['back_home'] }}</a>
                                <button type="button" class="btn btn-outline-secondary mt-3"
                                    onclick="window.close();">{{ $txt['close'] }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
