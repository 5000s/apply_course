@extends('layouts.bodhi')

@section('title', ($lang ?? 'th') === 'th' ? 'แบบฟอร์มสมัครคอร์ส: ' . $course->coursename : 'Application Form: ' .
    $course->coursename)

    @push('head')
        <style>
            .hero-image-wrap {
                width: 100%;
                height: 240px;
                overflow: hidden;
                border-top-left-radius: 1rem;
                border-top-right-radius: 1rem;
            }

            .hero-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            @media (min-width:992px) {
                .hero-image-wrap {
                    height: 300px;
                }
            }
        </style>
    @endpush

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- header --}}
                {{-- header --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="hero-image-wrap">
                            <img src="{{ $vm['image_url'] }}" alt="{{ $vm['alt'] }}" class="hero-image">
                        </div>

                        <div class="p-3 p-md-4">
                            @php
                                $lang = $lang ?? 'th';
                                $t = [
                                    'th' => [
                                        'place' => 'สถานที่',
                                        'course' => 'คอร์ส',
                                        'date' => 'วันที่',
                                        'status' => 'สถานะ',
                                        'note_label' => 'หมายเหตุ',
                                        'foreigner_only' => 'คอร์สนี้เปิดรับเฉพาะชาวต่างชาติเท่านั้น',
                                        'state_open' => 'เปิดรับสมัคร',
                                        'state_soon' => 'ใกล้เริ่มแล้ว',
                                        'state_closed' => 'สิ้นสุดการรับสมัคร',
                                        'header_info' => 'ข้อมูลผู้สมัคร',
                                        'name' => 'ชื่อ',
                                        'surname' => 'นามสกุล',
                                        'phone' => 'โทรศัพท์',
                                        'phone_placeholder' => 'กรอกเบอร์โทรศัพท์',
                                        'phone_warn' => 'กรุณาตรวจสอบเบอร์ของท่าน เพื่อให้เจ้าหน้าที่ติดต่อกลับ',
                                        'confirm_phone_q' => 'ข้อมูลเบอร์โทรถูกต้องหรือไม่?',
                                        'confirm_email_q' => 'อีเมลถูกต้องหรือไม่?',
                                        'answer_correct' => 'ถูกต้อง',
                                        'answer_edit' => 'ไม่ถูกต้องและแก้ไข',
                                        'no_email' => 'ไม่มีอีเมล',
                                        'edit_contact' => 'แก้ไขข้อมูล',
                                        'use_original' => 'ใช้ข้อมูลเดิม',
                                        'contact_edit_note' => 'กรุณากรอกข้อมูลที่ถูกต้อง เพื่อให้เจ้าหน้าที่มูลนิธิฯ สามารถติดต่อได้',
                                        'country_code' => 'รหัสประเทศ',
                                        'allow_whatsapp' => 'อนุญาตให้เจ้าหน้าที่ยืนยันคอร์สผ่าน WhatsApp',
                                        'no_email_note' => 'ท่านจะไม่ได้รับอีเมลยืนยันการสมัครจากระบบ',
                                        'email' => 'อีเมล',
                                        'phone2' => 'โทรศัพท์ที่ 2',
                                        'gender' => 'เพศ',
                                        'line' => 'ไลน์',
                                        'contact_via' => 'ช่องทางที่สะดวกให้ติดต่อ',
                                        'select_contact' => 'เลือกช่องทาง',
                                        'contact_phone' => 'โทรศัพท์',
                                        'contact_email' => 'อีเมล',
                                        'contact_line' => 'ไลน์',
                                        'contact_whatsapp' => 'Whatsapp',
                                        'contact_missing' => 'กรุณากรอกข้อมูลช่องทางที่เลือกให้ติดต่อ',
                                        'dob' => 'วันเกิด',
                                        'day' => 'วัน',
                                        'month' => 'เดือน',
                                        'year' => 'ปี',
                                        'year_disp' => 'ปี (พ.ศ.)',
                                        'nationality' => 'สัญชาติ',
                                        'select_nation' => '-- เลือกสัญชาติ --',
                                        'province' => 'จังหวัดที่อยู่ในไทย',
                                        'select_province' => '-- เลือกจังหวัด --',
                                        'country' => 'ประเทศ',
                                        'disease' => 'ข้อจำกัดทางด้านสุขภาพ (Dietary Restriction/Health Condition)',
                                        'disease_placeholder' => 'ระบุข้อจำกัดด้านอาหารหรือสุขภาพ เช่น ไม่ทานไข่ แพ้ถั่ว มีโรคประจำตัว (อาหารในคอร์สไม่มีเนื้อสัตว์อยู่แล้ว)',
                                        'header_edu' => 'การศึกษา และ อาชีพ',
                                        'education' => 'ระดับการศึกษา',
                                        'organization' => 'องค์กร',
                                        'career' => 'อาชีพ',
                                        'expertise' => 'ความเชี่ยวชาญ',
                                        'header_emerg' => 'ผู้ติดต่อฉุกเฉิน',
                                        'name_emerg' => 'ชื่อผู้ติดต่อฉุกเฉิน',
                                        'surname_emerg' => 'นามสกุลผู้ติดต่อฉุกเฉิน',
                                        'phone_emerg' => 'เบอร์โทรผู้ติดต่อฉุกเฉิน',
                                        'relation' => 'ความสัมพันธ์',
                                        'header_travel' => 'วิธีการเดินทาง',
                                        'travel_self' => 'เดินทางด้วยตนเอง',
                                        'travel_van' =>
                                            'โดยรถตู้ที่จัดเตรียมให้ สำหรับไปยัง อ.แก่งคอย จ.สระบุรี ขึ้นรถที่อ่อนนุช ซ.8',
                                        'submit' => 'ยืนยันการสมัครเข้าคอร์ส',
                                        'confirm_notice' => 'กรุณาตรวจสอบความถูกต้อง และกดปุ่มยืนยันการสมัครเข้าคอร์สด้านล่าง',
                                        'female' => 'หญิง',
                                        'male' => 'ชาย',
                                        'buddhism' => 'พุทธบริษัท / สถานะทางธรรม',
                                        'monk' => 'ภิกษุ',
                                        'novice' => 'สามเณร',
                                        'nun' => 'แม่ชี',
                                        'secular' => 'ฆราวาส',
                                    ],
                                    'en' => [
                                        'place' => 'Location',
                                        'course' => 'Course',
                                        'date' => 'Date',
                                        'status' => 'Status',
                                        'note_label' => 'Note',
                                        'foreigner_only' => 'This course is open to foreigners only',
                                        'state_open' => 'Open',
                                        'state_soon' => 'Starting Soon',
                                        'state_closed' => 'Closed',
                                        'header_info' => 'Applicant Information',
                                        'name' => 'First Name',
                                        'surname' => 'Last Name',
                                        'email' => 'Email',
                                        'phone' => 'Phone',
                                        'phone_placeholder' => 'Enter phone number',
                                        'phone_warn' => 'Please verify your phone number so staff can contact you.',
                                        'confirm_phone_q' => 'Is this phone number correct?',
                                        'confirm_email_q' => 'Is this email correct?',
                                        'answer_correct' => 'Correct',
                                        'answer_edit' => 'Incorrect, edit',
                                        'no_email' => 'No email',
                                        'edit_contact' => 'Edit',
                                        'use_original' => 'Use original info',
                                        'contact_edit_note' => 'Please provide correct contact info so foundation staff can reach you.',
                                        'country_code' => 'Country code',
                                        'allow_whatsapp' => 'Allow staff to confirm the course via WhatsApp',
                                        'no_email_note' => 'You will not receive the confirmation email from the system.',
                                        'phone2' => 'Phone 2',
                                        'gender' => 'Gender',
                                        'line' => 'Line ID',
                                        'contact_via' => 'Preferred contact channel',
                                        'select_contact' => 'Select channel',
                                        'contact_phone' => 'Phone',
                                        'contact_email' => 'Email',
                                        'contact_line' => 'Line',
                                        'contact_whatsapp' => 'WhatsApp',
                                        'contact_missing' => 'Please fill in the selected contact channel.',
                                        'dob' => 'Date of Birth',
                                        'day' => 'Day',
                                        'month' => 'Month',
                                        'year' => 'Year',
                                        'year_disp' => 'Year',
                                        'nationality' => 'Nationality',
                                        'select_nation' => '-- Select Nationality --',
                                        'province' => 'Province (in Thailand)',
                                        'select_province' => '-- Select Province --',
                                        'country' => 'Country',
                                        'disease' => 'Dietary Restriction / Health Condition',
                                        'disease_placeholder' => 'Specify dietary or health restrictions, e.g. no egg, nut allergy, medical condition (course meals are already meat-free)',
                                        'header_edu' => 'Education & Career',
                                        'education' => 'Education Level',
                                        'organization' => 'Organization',
                                        'career' => 'Career',
                                        'expertise' => 'Expertise',
                                        'header_emerg' => 'Emergency Contact',
                                        'name_emerg' => 'Contact Name',
                                        'surname_emerg' => 'Contact Surname',
                                        'phone_emerg' => 'Contact Phone',
                                        'relation' => 'Relationship',
                                        'header_travel' => 'Transportation',
                                        'travel_self' => 'Travel by yourself',
                                        'travel_van' =>
                                            'By provided van to Kaeng Khoi, Saraburi (Depart from On Nut Soi 8)',
                                        'submit' => 'Confirm Application',
                                        'confirm_notice' => 'Please review your information and click the button below to confirm your course registration.',
                                        'female' => 'Female',
                                        'male' => 'Male',
                                        'buddhism' => 'Buddhist Status',
                                        'monk' => 'Monk',
                                        'novice' => 'Novice',
                                        'nun' => 'Nun',
                                        'secular' => 'Layperson (Secular)',
                                    ],
                                ];
                                $txt = $t[$lang];

                                // ปิดบังเบอร์โทร: เก็บ 3 ตัวแรก + 2 ตัวท้าย ที่เหลือเป็น x (รองรับหลายเบอร์คั่น ,)
                                $maskPhone = function ($raw) {
                                    $parts = array_filter(array_map('trim', explode(',', (string) $raw)));
                                    $masked = array_map(function ($p) {
                                        $d = preg_replace('/\D+/', '', $p);
                                        if (strlen($d) < 5) {
                                            return str_repeat('x', max(1, strlen($d)));
                                        }
                                        return substr($d, 0, 3) . str_repeat('x', strlen($d) - 5) . substr($d, -2);
                                    }, $parts);
                                    return implode(', ', $masked);
                                };

                                // ปิดบังอีเมล: แสดง 2 ตัวแรกของ local part ที่เหลือเป็น * แล้วต่อด้วย @domain
                                $maskEmail = function ($email) {
                                    $email = (string) $email;
                                    $at = strpos($email, '@');
                                    if ($at === false) {
                                        return $email;
                                    }
                                    $local = substr($email, 0, $at);
                                    $domain = substr($email, $at);
                                    return mb_substr($local, 0, 2) . str_repeat('*', max(1, mb_strlen($local) - 2)) . $domain;
                                };

                                // รหัสประเทศสำหรับ dropdown เบอร์โทร (ไทยเป็นค่าเริ่มต้น)
                                $countryCodes = [
                                    '+66' => 'ไทย (+66)',
                                    '+1' => 'USA/Canada (+1)',
                                    '+44' => 'UK (+44)',
                                    '+61' => 'Australia (+61)',
                                    '+64' => 'New Zealand (+64)',
                                    '+65' => 'Singapore (+65)',
                                    '+60' => 'Malaysia (+60)',
                                    '+62' => 'Indonesia (+62)',
                                    '+63' => 'Philippines (+63)',
                                    '+84' => 'Vietnam (+84)',
                                    '+95' => 'Myanmar (+95)',
                                    '+856' => 'Laos (+856)',
                                    '+855' => 'Cambodia (+855)',
                                    '+86' => 'China (+86)',
                                    '+852' => 'Hong Kong (+852)',
                                    '+886' => 'Taiwan (+886)',
                                    '+81' => 'Japan (+81)',
                                    '+82' => 'South Korea (+82)',
                                    '+91' => 'India (+91)',
                                    '+49' => 'Germany (+49)',
                                    '+33' => 'France (+33)',
                                    '+39' => 'Italy (+39)',
                                    '+34' => 'Spain (+34)',
                                    '+41' => 'Switzerland (+41)',
                                    '+31' => 'Netherlands (+31)',
                                    '+46' => 'Sweden (+46)',
                                    '+7' => 'Russia (+7)',
                                    '+971' => 'UAE (+971)',
                                    '+972' => 'Israel (+972)',
                                ];

                                $formatDate = function ($d) use ($lang) {
                                    if (!$d) {
                                        return '-';
                                    }
                                    $c = \Illuminate\Support\Carbon::parse($d);
                                    if ($lang === 'th') {
                                        $c->locale('th');
                                        return $c->translatedFormat('j F') . ' ' . ($c->year + 543);
                                    }
                                    $c->locale('en');
                                    return $c->translatedFormat('j F Y');
                                };

                                // State Map
                                $stateMap = [
                                    'เปิดรับสมัคร' => ['text' => $txt['state_open'], 'class' => 'bg-success'],
                                    'ใกล้เริ่มแล้ว' => [
                                        'text' => $txt['state_soon'],
                                        'class' => 'bg-warning text-dark',
                                    ],
                                    'สิ้นสุดการรับสมัคร' => ['text' => $txt['state_closed'], 'class' => 'bg-secondary'],
                                ];
                                $currentState = $vm['state'] ?? null;
                                $displayState = $stateMap[$currentState]['text'] ?? ($currentState ?? '-');
                                $badgeClass = $stateMap[$currentState]['class'] ?? 'bg-light text-dark';
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <colgroup>
                                        <col style="width: 100px">
                                        <col>
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">{{ $txt['place'] }}</th>
                                            <td class="fs-5">
                                                @if ($lang === 'th')
                                                    {{ $vm['place_name'] ?? '-' }}
                                                @else
                                                    {{ $vm['place_name_en'] ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">{{ $txt['course'] }}</th>
                                            <td class="fs-5">
                                                @if ($lang === 'th')
                                                    {{ $course_cat->show_name ?? ($course_cat->name ?? '-') }}
                                                @else
                                                    {{ $course_cat->show_name_en ?? ($course_cat->name_en ?? '-') }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">{{ $txt['date'] }}</th>
                                            <td class="fs-5">
                                                @if ($course->date_start != $course->date_end)
                                                    {{ $formatDate($course->date_start ?? $course->start_date) }} –
                                                    {{ $formatDate($course->date_end ?? $course->end_date) }}
                                                @else
                                                    {{ $formatDate($course->date_start ?? $course->start_date) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">{{ $txt['status'] }}</th>
                                            <td class="fs-5">
                                                <span
                                                    class="badge {{ $badgeClass }} px-3 py-2">{{ $displayState }}</span>
                                            </td>
                                        </tr>
                                        @if (!empty($course->only_foreigner))
                                            <tr>
                                                <th scope="row" class="text-muted fw-semibold">
                                                    {{ $txt['note_label'] }}</th>
                                                <td class="fs-5">{{ $txt['foreigner_only'] }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- alert --}}
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="applicationForm" method="POST"
                    action="{{ route('apply.form.confirm', [$course->id, $member->id]) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <input type="hidden" name="full_form" value="1">

                    {{-- ข้อมูลผู้สมัคร --}}
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ $txt['header_info'] }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['name'] }} *</label>
                                    @if ($member_new == false)
                                        {{ $member->name }}
                                    @else
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $member->name) }}">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['surname'] }} *</label>
                                    @if ($member_new == false)
                                        {{ $member->surname }}
                                    @else
                                        <input type="text" name="surname" class="form-control"
                                            value="{{ old('surname', $member->surname) }}">
                                    @endif
                                </div>
                                {{-- ข้อมูลติดต่อ: แสดงแบบปิดบัง + ปุ่มแก้ไข (สมาชิกเดิมที่มีข้อมูล) / ฟอร์มกรอก (สมาชิกใหม่) --}}
                                @php $hasExistingContact = $member_new == false && ($member->phone || $member->email); @endphp
                                <div class="col-12" id="wrap_phone">
                                    @if ($hasExistingContact)
                                        <div id="contact_summary"
                                            class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 bg-light border rounded">
                                            <div>
                                                @if ($member->phone)
                                                    <div>{{ $txt['phone'] }}: <span
                                                            class="fw-semibold">{{ $maskPhone($member->phone) }}</span>
                                                    </div>
                                                @endif
                                                @if ($member->email)
                                                    <div>{{ $txt['email'] }}: <span
                                                            class="fw-semibold">{{ $maskEmail($member->email) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-primary" id="btn_edit_contact">
                                                <i class="fas fa-pen"></i> {{ $txt['edit_contact'] }}
                                            </button>
                                        </div>
                                    @endif

                                    <div id="contact_edit_wrap" class="row g-3" @if ($hasExistingContact) hidden @endif>
                                        <div class="col-12">
                                            <div
                                                class="alert alert-warning py-2 mb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                <span>
                                                    <i class="fas fa-exclamation-circle"></i> {{ $txt['contact_edit_note'] }}
                                                </span>
                                                @if ($hasExistingContact)
                                                    {{-- ปุ่มยกเลิกการแก้ไข: กลับไปใช้ข้อมูลเดิม (โหมดปิดบัง) --}}
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        id="btn_use_original">
                                                        <i class="fas fa-rotate-left"></i> {{ $txt['use_original'] }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- เบอร์โทรศัพท์ + รหัสประเทศ + whatsapp --}}
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $txt['phone'] }} *</label>
                                            <div class="input-group">
                                                <select name="phone_country_code" class="form-select"
                                                    style="max-width: 150px;" aria-label="{{ $txt['country_code'] }}">
                                                    @foreach ($countryCodes as $code => $cLabel)
                                                        <option value="{{ $code }}"
                                                            {{ old('phone_country_code', '+66') == $code ? 'selected' : '' }}>
                                                            {{ $cLabel }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ old('phone') }}" autocomplete="off"
                                                    placeholder="{{ $txt['phone_placeholder'] }}">
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="allow_whatsapp"
                                                    id="allow_whatsapp" value="1"
                                                    {{ old('allow_whatsapp', $member->contact_via == 'whatsapp' ? '1' : '') ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="allow_whatsapp">{{ $txt['allow_whatsapp'] }}</label>
                                            </div>
                                        </div>

                                        {{-- อีเมล + ไม่มีอีเมล --}}
                                        <div class="col-md-6">
                                            <label class="form-label">{{ $txt['email'] }}</label>
                                            <input type="text" name="email" class="form-control" id="email_input"
                                                value="{{ old('email', $hasExistingContact ? '' : $member->email) }}"
                                                autocomplete="off" placeholder="{{ $txt['email'] }}">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" id="no_email">
                                                <label class="form-check-label"
                                                    for="no_email">{{ $txt['no_email'] }}</label>
                                            </div>
                                            <div id="no_email_note" class="text-danger small mt-1" hidden>
                                                * {{ $txt['no_email_note'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['phone2'] }}</label>
                                    <input type="text" name="phone_2" class="form-control"
                                        value="{{ old('phone_2', $member->phone_2) }}">
                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['gender'] }}</label>
                                    <select name="gender" class="form-select">
                                        <option value="หญิง"
                                            {{ old('gender', $member->gender) == 'หญิง' ? 'selected' : '' }}>
                                            {{ $txt['female'] }}</option>
                                        <option value="ชาย"
                                            {{ old('gender', $member->gender) == 'ชาย' ? 'selected' : '' }}>
                                            {{ $txt['male'] }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['buddhism'] }}</label>
                                    <select name="buddhism" class="form-select">
                                        <option value="ฆราวาส"
                                            {{ old('buddhism', $member->buddhism) == 'ฆราวาส' ? 'selected' : '' }}>
                                            {{ $txt['secular'] }}</option>
                                        <option value="ภิกษุ"
                                            {{ old('buddhism', $member->buddhism) == 'ภิกษุ' ? 'selected' : '' }}>
                                            {{ $txt['monk'] }}</option>
                                        <option value="สามเณร"
                                            {{ old('buddhism', $member->buddhism) == 'สามเณร' ? 'selected' : '' }}>
                                            {{ $txt['novice'] }}</option>
                                        <option value="แม่ชี"
                                            {{ old('buddhism', $member->buddhism) == 'แม่ชี' ? 'selected' : '' }}>
                                            {{ $txt['nun'] }}</option>
                                    </select>
                                </div>

                                <div class="col-md-6" id="wrap_line" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['line'] }}</label>
                                    <input type="text" name="line" class="form-control"
                                        value="{{ old('line', $member->line) }}">
                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    @php
                                        $old = old('birthdate', optional($member->birthdate)->format('Y-m-d'));
                                        if ($old && preg_match('/^\d{4}-\d{2}-\d{2}$/', $old)) {
                                            [$defY, $defM, $defD] = array_map('intval', explode('-', $old));
                                        } else {
                                            $defY = 1977;
                                            $defM = 1;
                                            $defD = 1;
                                        }
                                        $defBE = $defY + 543;

                                        if ($lang === 'th') {
                                            $monthsArr = [
                                                'ม.ค.',
                                                'ก.พ.',
                                                'มี.ค.',
                                                'เม.ย.',
                                                'พ.ค.',
                                                'มิ.ย.',
                                                'ก.ค.',
                                                'ส.ค.',
                                                'ก.ย.',
                                                'ต.ค.',
                                                'พ.ย.',
                                                'ธ.ค.',
                                            ];
                                        } else {
                                            $monthsArr = [
                                                'Jan',
                                                'Feb',
                                                'Mar',
                                                'Apr',
                                                'May',
                                                'Jun',
                                                'Jul',
                                                'Aug',
                                                'Sep',
                                                'Oct',
                                                'Nov',
                                                'Dec',
                                            ];
                                        }
                                    @endphp
                                    <label class="form-label">{{ $txt['dob'] }}</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select id="dob_day" class="form-select" aria-label="{{ $txt['day'] }}">
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ $d }}"
                                                        {{ $d === $defD ? 'selected' : '' }}>
                                                        {{ $d }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_month" class="form-select" aria-label="{{ $txt['month'] }}">
                                                <option value="">{{ $txt['month'] }}</option>
                                                @foreach ($monthsArr as $i => $name)
                                                    <option value="{{ $i + 1 }}"
                                                        {{ $i + 1 === $defM ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_year" class="form-select"
                                                aria-label="{{ $txt['year_disp'] }}">
                                                <option value="">{{ $txt['year_disp'] }}</option>
                                                @for ($year = now()->year - 15; $year >= 1945; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $year === $defY ? 'selected' : '' }}>
                                                        @if ($lang === 'th')
                                                            {{ $year + 543 }} ({{ $year }})
                                                        @else
                                                            {{ $year }}
                                                        @endif
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="birthdate" id="birthdate"
                                        value="{{ sprintf('%04d-%02d-%02d', $defY, $defM, $defD) }}">
                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['nationality'] }} *</label>
                                    @php $selectedNation = old('nationality', $member->nationality ?: 'ไทย'); @endphp
                                    <select name="nationality" class="form-select">
                                        <option value="">{{ $txt['select_nation'] }}</option>
                                        @foreach ($nations as $nation)
                                            <option value="{{ $nation }}"
                                                {{ $selectedNation == $nation ? 'selected' : '' }}>
                                                {{ $nation === 'ไทย' ? 'ไทย (Thailand)' : $nation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{ $member->nationality }}
                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['province'] }} {{ $member->province }}</label>
                                    <select name="province" class="form-select">
                                        <option value="">{{ $txt['select_province'] }}</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['name_th'] }}"
                                                {{ old('province', $member->province) == $province['name_th'] ? 'selected' : '' }}>
                                                {{ $province['name_th'] }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-6" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['country'] }}</label>
                                    <input type="text" name="country" class="form-control"
                                        value="{{ old('country', $member->country ?? 'Thailand') }}">
                                </div>

                                <div class="col-md-12" @if ($member_new == false) hidden @endif>
                                    <label class="form-label">{{ $txt['disease'] }}</label>
                                    <textarea name="disease" class="form-control" rows="2"
                                        placeholder="{{ $txt['disease_placeholder'] }}">{{ old('disease', $member->medical_condition) }}</textarea>
                                    {{-- ถ้าคุณใช้ column อื่นสำหรับโรคประจำตัว ให้ปรับตรงนี้ --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- การศึกษา และ อาชีพ --}}
                    <div class="card mb-4" @if ($member_new == false) hidden @endif>
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ $txt['header_edu'] }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['education'] }}</label>
                                    <input type="text" name="degree" class="form-control"
                                        value="{{ old('degree', $member->degree) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['organization'] }}</label>
                                    <input type="text" name="organization" class="form-control"
                                        value="{{ old('organization', $member->organization) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['career'] }}</label>
                                    <input type="text" name="career" class="form-control"
                                        value="{{ old('career', $member->career) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['expertise'] }}</label>
                                    <input type="text" name="expertise" class="form-control"
                                        value="{{ old('expertise', $member->expertise) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ผู้ติดต่อฉุกเฉิน --}}
                    <div class="card mb-4" @if ($member_new == false) hidden @endif>
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ $txt['header_emerg'] }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['name_emerg'] }}</label>
                                    <input type="text" name="name_emergency" class="form-control"
                                        value="{{ old('name_emergency', $member->name_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['surname_emerg'] }}</label>
                                    <input type="text" name="surname_emergency" class="form-control"
                                        value="{{ old('surname_emergency', $member->surname_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['phone_emerg'] }}</label>
                                    <input type="text" name="phone_emergency" class="form-control"
                                        value="{{ old('phone_emergency', $member->phone_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['relation'] }}</label>
                                    <input type="text" name="relation_emergency" class="form-control"
                                        value="{{ old('relation_emergency', $member->relation_emergency) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- วิธีการเดินทาง (เฉพาะคอร์สวิปัสสนา + แก่งคอย) --}}
                    @if (
                        \Illuminate\Support\Str::contains($course_cat->show_name ?? '', 'วิปัสสนา') &&
                            \Illuminate\Support\Str::contains($vm['place_name'] ?? '', 'แก่งคอย'))
                        {{-- input hidden --}}
                        <input type="hidden" name="no_update" value="1">

                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">{{ $txt['header_travel'] }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="van" id="van0"
                                        value="0" checked>
                                    <label class="form-check-label" for="van0">
                                        {{ $txt['travel_self'] }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="van" id="van1"
                                        value="1">
                                    <label class="form-check-label" for="van1">
                                        {{ $txt['travel_van'] }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="text-center text-danger fw-semibold mb-3">
                        {{ $txt['confirm_notice'] }}
                    </p>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            {{ $txt['submit'] }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- SweetAlert2 & Validation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('applicationForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const lang = "{{ $lang ?? 'th' }}"; // 'th' or 'en'
                    let missingFields = [];
                    let invalidFields = [];
                    let thaiOnlyFields = []; // หน้าไทย: ชื่อ-นามสกุล ต้องเป็นภาษาไทยเท่านั้น

                    // Define field names for display
                    const fieldNames = {
                        th: {
                            name: 'ชื่อ',
                            surname: 'นามสกุล',
                            email: 'อีเมล',
                            phone: 'เบอร์โทรศัพท์',
                            nationality: 'สัญชาติ'
                        },
                        en: {
                            name: 'First Name',
                            surname: 'Last Name',
                            email: 'Email',
                            phone: 'Phone',
                            nationality: 'Nationality'
                        }
                    };

                    const currentNames = fieldNames[lang === 'en' ? 'en' : 'th'];

                    // Helper to check if input is visible
                    const isVisible = (elem) => !!(elem.offsetWidth || elem.offsetHeight || elem
                        .getClientRects()
                        .length);

                    // Fields to validate: name, surname, email, phone, nationality
                    const fields = ['name', 'surname', 'email', 'phone', 'nationality'];

                    fields.forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input && isVisible(input)) {
                            const val = input.value.trim();
                            if (!val) {
                                // อีเมลเป็น optional — ไม่บังคับกรอก
                                if (field !== 'email') {
                                    missingFields.push(currentNames[field]);
                                }
                            } else {
                                if (field === 'email') {
                                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                    if (!emailRegex.test(val)) {
                                        invalidFields.push(currentNames[field]);
                                    }
                                }
                                if (field === 'phone') {
                                    const phoneVal = val.replace(/[-\s]/g, '');
                                    const phoneRegex =
                                        /^\+?\d{9,15}$/; // optional leading +, 9-15 digits
                                    if (!phoneRegex.test(phoneVal)) {
                                        invalidFields.push(currentNames[field]);
                                    }
                                }
                                // หน้าไทย: ชื่อ/นามสกุล ต้องเป็นตัวอักษรไทยเท่านั้น (อนุญาตเว้นวรรค)
                                if (lang !== 'en' && (field === 'name' || field === 'surname')) {
                                    const thaiRegex = /^[฀-๿\s]+$/;
                                    if (!thaiRegex.test(val)) {
                                        thaiOnlyFields.push(currentNames[field]);
                                    }
                                }
                            }
                        }
                    });

                    if (missingFields.length > 0 || invalidFields.length > 0 || thaiOnlyFields.length > 0) {
                        e.preventDefault(); // Stop submission

                        let title = lang === 'en' ? 'Information Error' : 'ข้อมูลไม่ถูกต้อง';

                        let textMsgs = [];
                        if (missingFields.length > 0) {
                            textMsgs.push((lang === 'en' ?
                                'Please fill in the following required fields:\n' :
                                'กรุณากรอกข้อมูลดังต่อไปนี้ให้ครบถ้วน:\n') + missingFields.join(
                                ', '));
                        }
                        if (invalidFields.length > 0) {
                            textMsgs.push((lang === 'en' ?
                                'Please enter correct format for:\n' :
                                'กรุณากรอกข้อมูลให้ถูกต้องสำหรับ:\n') + invalidFields.join(', '));
                        }
                        if (thaiOnlyFields.length > 0) {
                            textMsgs.push('กรุณากรอกเป็นภาษาไทยเท่านั้นสำหรับ:\n' + thaiOnlyFields.join(', '));
                        }

                        let text = textMsgs.join('\n\n');
                        let confirmBtn = lang === 'en' ? 'OK' : 'ตกลง';

                        Swal.fire({
                            icon: 'warning',
                            title: title,
                            text: text,
                            confirmButtonText: confirmBtn,
                            confirmButtonColor: '#c9a750' // Bodhi Gold color
                        });
                    }
                });

                // ปุ่ม "แก้ไขข้อมูล": แสดงฟอร์มแก้ไขเบอร์/อีเมล แล้วซ่อนสรุปแบบปิดบัง
                const btnEditContact = document.getElementById('btn_edit_contact');
                const contactSummary = document.getElementById('contact_summary');
                const contactEditWrap = document.getElementById('contact_edit_wrap');
                if (btnEditContact && contactEditWrap) {
                    btnEditContact.addEventListener('click', function() {
                        contactEditWrap.hidden = false;
                        if (contactSummary) contactSummary.hidden = true;
                        const phoneInput = contactEditWrap.querySelector('[name="phone"]');
                        if (phoneInput) phoneInput.focus();
                    });
                }

                // ปุ่ม "ใช้ข้อมูลเดิม": ยกเลิกการแก้ไข กลับไปสรุปแบบปิดบัง และล้างช่องที่กรอกไว้
                const btnUseOriginal = document.getElementById('btn_use_original');
                if (btnUseOriginal && contactEditWrap) {
                    btnUseOriginal.addEventListener('click', function() {
                        contactEditWrap.hidden = true;
                        if (contactSummary) contactSummary.hidden = false;
                        const phoneInput = contactEditWrap.querySelector('[name="phone"]');
                        if (phoneInput) phoneInput.value = '';
                        const emailField = document.getElementById('email_input');
                        if (emailField) {
                            emailField.value = '';
                            emailField.disabled = false;
                        }
                        const noEmailField = document.getElementById('no_email');
                        if (noEmailField) noEmailField.checked = false;
                        const noEmailNoteEl = document.getElementById('no_email_note');
                        if (noEmailNoteEl) noEmailNoteEl.hidden = true;
                    });
                }

                // "ไม่มีอีเมล": ปิด/ล้างช่องอีเมล และแสดงหมายเหตุว่าจะไม่ได้รับอีเมลยืนยัน
                const emailInput = document.getElementById('email_input');
                const noEmailChk = document.getElementById('no_email');
                const noEmailNote = document.getElementById('no_email_note');
                if (noEmailChk && emailInput) {
                    noEmailChk.addEventListener('change', function() {
                        emailInput.disabled = this.checked;
                        if (this.checked) emailInput.value = '';
                        if (noEmailNote) noEmailNote.hidden = !this.checked;
                    });
                }
            }
        });
    </script>
@endsection

@push('scripts')
    {{-- Birth date helper --}}
    <script>
        (function() {
            const daySel = document.getElementById('dob_day');
            const monthSel = document.getElementById('dob_month');
            const yearSel = document.getElementById('dob_year');
            const hidden = document.getElementById('birthdate');

            function pad(n) {
                n = parseInt(n || 0, 10);
                return String(n).padStart(2, '0');
            }

            function maxDays(month, be) {
                month = parseInt(month || 0, 10);
                if (!month) return 31;
                if (month === 2) {
                    const ce = parseInt(be || 0, 10);
                    if (!ce) return 28;
                    const leap = (ce % 4 === 0 && ce % 100 !== 0) || (ce % 400 === 0);
                    return leap ? 29 : 28;
                }
                return [4, 6, 9, 11].includes(month) ? 30 : 31;
            }

            function clampDay() {
                const m = monthSel.value;
                const be = yearSel.value;
                const max = maxDays(m, be);
                if (parseInt(daySel.value, 10) > max) daySel.value = String(max);
                updateHidden();
            }

            function updateHidden() {
                const d = daySel.value;
                const m = monthSel.value;
                const be = yearSel.value;
                if (!d || !m || !be) {
                    hidden.value = '';
                    return;
                }
                const ce = parseInt(be,
                    10); // The year in the select is already CE (value="{{ $year }}") but displayed as BE
                // Wait, in the loop: value="{{ $year }}" (CE), text is {{ $year + 543 }} (BE).
                // So yearSel.value IS CE.
                // My logic in direct.blade.php might have been slightly different or I need to be careful.
                // In direct.blade.php:
                // <option value="{{ $year }}" ...> {{ $year + 543 }} </option>
                // So value is CE.
                // JS: const ce = parseInt(be,10); -> variable name 'be' is confusing if it holds CE value.
                // Let's stick to the logic: value is CE.

                hidden.value = `${ce}-${pad(m)}-${pad(d)}`;
            }

            if (monthSel && yearSel && daySel && hidden) {
                monthSel.addEventListener('change', clampDay);
                yearSel.addEventListener('change', clampDay);
                daySel.addEventListener('change', updateHidden);
                clampDay();
            }
        })();
    </script>
@endpush
