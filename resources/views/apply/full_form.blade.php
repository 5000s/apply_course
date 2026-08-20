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
                                <div class="col-md-6" id="wrap_email" @if ($member_new == false && $member->email != null && $member->email != '') hidden @endif>
                                    <label class="form-label">{{ $txt['email'] }}</label>
                                    <input type="text" name="email" class="form-control"
                                        value="{{ old('email', $member->email) }}">
                                </div>

                                <div class="col-md-3" id="wrap_phone" @if ($member_new == false && $member->phone != null && $member->phone != '') hidden @endif>
                                    <label class="form-label">{{ $txt['phone'] }} *</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $member->phone) }}" required>
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

                                <div class="col-md-6">
                                    <label class="form-label">{{ $txt['contact_via'] }}</label>
                                    <select name="contact_via" id="contact_via" class="form-select">
                                        <option value="">{{ $txt['select_contact'] }}</option>
                                        <option value="phone"
                                            {{ old('contact_via', $member->contact_via) == 'phone' ? 'selected' : '' }}>
                                            {{ $txt['contact_phone'] }}</option>
                                        <option value="email"
                                            {{ old('contact_via', $member->contact_via) == 'email' ? 'selected' : '' }}>
                                            {{ $txt['contact_email'] }}</option>
                                        <option value="line"
                                            {{ old('contact_via', $member->contact_via) == 'line' ? 'selected' : '' }}>
                                            {{ $txt['contact_line'] }}</option>
                                        <option value="whatsapp"
                                            {{ old('contact_via', $member->contact_via) == 'whatsapp' ? 'selected' : '' }}>
                                            {{ $txt['contact_whatsapp'] }}</option>
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
                    let contactMissing = false; // ช่องทางติดต่อที่เลือก ต้องมีข้อมูลจริง
                    const contactMissingMsg = @json($txt['contact_missing']);

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

                    // ช่องทางที่สะดวกให้ติดต่อ: optional (เลือกหรือไม่ก็ได้)
                    // แต่ถ้าเลือกแล้ว ช่องที่เลือกต้องมีข้อมูลจริง
                    const contactSelect = form.querySelector('[name="contact_via"]');
                    if (contactSelect && isVisible(contactSelect)) {
                        const choice = contactSelect.value;
                        if (choice) {
                            // Whatsapp ใช้เบอร์จากช่อง Phone
                            const targetName = choice === 'whatsapp' ? 'phone' : choice;
                            const target = form.querySelector(`[name="${targetName}"]`);
                            const targetVal = target ? target.value.trim() : '';
                            if (!targetVal) {
                                contactMissing = true;
                            }
                        }
                    }

                    if (missingFields.length > 0 || invalidFields.length > 0 || thaiOnlyFields.length > 0 ||
                        contactMissing) {
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
                        if (contactMissing) {
                            textMsgs.push(contactMissingMsg);
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

                // เผยช่องกรอกช่องทางติดต่อที่เลือก ถ้ายังไม่มีข้อมูล (สำหรับสมาชิกที่มีข้อมูลอยู่แล้ว)
                const contactSelectEl = form.querySelector('[name="contact_via"]');
                const contactWraps = {
                    phone: document.getElementById('wrap_phone'),
                    email: document.getElementById('wrap_email'),
                    line: document.getElementById('wrap_line')
                };
                const contactTargetOf = (choice) => (choice === 'whatsapp' ? 'phone' : choice);

                function syncContactInput() {
                    if (!contactSelectEl) return;
                    const target = contactTargetOf(contactSelectEl.value);

                    // ซ่อนช่องที่เคยเปิดไว้อัตโนมัติ และไม่ใช่ช่องที่เลือกอยู่
                    Object.keys(contactWraps).forEach(function(key) {
                        const wrap = contactWraps[key];
                        if (wrap && wrap.dataset.forced === '1' && key !== target) {
                            wrap.setAttribute('hidden', '');
                            delete wrap.dataset.forced;
                        }
                    });

                    // ถ้าช่องที่เลือกยังไม่มีข้อมูล ให้เปิดช่องกรอกให้
                    if (target && contactWraps[target]) {
                        const input = form.querySelector(`[name="${target}"]`);
                        const isEmpty = !input || !input.value.trim();
                        if (isEmpty) {
                            contactWraps[target].removeAttribute('hidden');
                            contactWraps[target].dataset.forced = '1';
                        }
                    }
                }

                if (contactSelectEl) {
                    contactSelectEl.addEventListener('change', syncContactInput);
                    syncContactInput(); // ตรวจตอนโหลดครั้งแรก
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
