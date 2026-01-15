{{-- resources/views/apply/direct.blade.php --}}
@extends('layouts.bodhi')

@section('title', 'สมัครคอร์ส: ' . $course->title)

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
            <div class="col-lg-9">

                {{-- HERO: รูปสถานที่ + รายละเอียดสั้น --}}
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
                                        'state_open' => 'เปิดรับสมัคร',
                                        'state_soon' => 'ใกล้เริ่มแล้ว',
                                        'state_closed' => 'สิ้นสุดการรับสมัคร',
                                        'state_unknown' => 'ไม่ระบุ',
                                        'form_header' => 'กรอกข้อมูลเพื่อสมัครคอร์ส',
                                        'instructions' =>
                                            'กรุณากรอก <strong>ชื่อ</strong>, <strong>นามสกุล</strong>, <strong>วันเกิด</strong> และ <strong>เบอร์โทรศัพท์</strong> แล้วกดปุ่มส่งคำขอสมัครคอร์ส',
                                        'gender' => 'เพศ',
                                        'firstname' => 'ชื่อ',
                                        'lastname' => 'นามสกุล',
                                        'birthdate' => 'วันเกิด',
                                        'day' => 'วัน',
                                        'month' => 'เดือน',
                                        'year' => 'ปี',
                                        'year_ph' => 'ปี (พ.ศ.)', // Placeholder
                                        'phone' => 'เบอร์โทรศัพท์',
                                        'phone_placeholder' => 'เบอร์โทรศัพท์ เช่น 08xxxxxxxx',
                                        'check_member' => 'ตรวจสอบข้อมูลสมาชิก',
                                        'btn_check' => 'ตรวจสอบข้อมูล',
                                        'robot_check' => 'ยืนยันว่าไม่ใช้หุ่นยนต์',
                                        'submit_btn' => 'ส่งคำขอสมัครคอร์ส',
                                        'privacy_note' =>
                                            '* ข้อมูลของท่านจะถูกเก็บรักษาเป็นความลับ ใช้เฉพาะการจัดการใบสมัครคอร์ส',
                                        'female' => 'หญิง',
                                        'male' => 'ชาย',
                                        'select_month' => 'เดือน',
                                        'msg_closed_online' => 'คอร์สนี้ปิดรับสมัครทางออนไลน์แล้ว',
                                        'msg_contact_seat' => 'กรุณาติดต่อมูลนิธิฯ เพื่อตรวจสอบที่นั่งว่างได้ที่',
                                        'email_label' => 'อีเมล',
                                        'tel_label' => 'โทร',
                                        // Modal & JS
                                        'modal_title' => 'แจ้งปัญหาไม่พบข้อมูล',
                                        'modal_body' =>
                                            'ระบบจะทำการส่งเรื่องแจ้งไปยังเจ้าหน้าที่ <br> โปรดตรวจสอบข้อมูลของท่านเพื่อให้ทางเจ้าหน้าที่ติดต่อกลับ',
                                        'modal_info_header' => 'ข้อมูลที่ค้นหา:',
                                        'modal_name' => 'ชื่อ-นามสกุล',
                                        'modal_gender' => 'เพศ',
                                        'modal_dob' => 'วันเกิด',
                                        'modal_phone' => 'เบอร์โทรศัพท์',
                                        'modal_email' => 'อีเมล (ไม่จำเป็น)',
                                        'modal_cancel' => 'ยกเลิก',
                                        'modal_submit' => 'ยืนยันการแจ้งปัญหา',
                                        'modal_sending' => 'กำลังส่งข้อมูล...',
                                        'modal_success' => 'ระบบได้รับข้อมูลเรียบร้อยแล้ว',
                                        'modal_contact_soon' => 'เจ้าหน้าที่จะติดต่อกลับโดยเร็วที่สุด',
                                        'modal_close' => 'ปิดหน้าต่างนี้',
                                        'modal_error' => 'เกิดข้อผิดพลาดในการส่งข้อมูล กรุณาลองใหม่อีกครั้ง',
                                        'modal_phone_req' => 'กรุณาระบุเบอร์โทรศัพท์',

                                        'js_fill_all' =>
                                            'กรุณากรอกข้อมูล ชื่อ นามสกุล วันเกิด และ เบอร์โทรศัพท์ ให้ครบถ้วน',
                                        'js_not_found_header' => 'ไม่พบข้อมูลเดิม',
                                        'js_new_member_msg' =>
                                            '<u> หากท่านเป็นสมาชิกใหม่ </u> สามารถกดสมัครได้ทันที <br><u> หากท่านเป็นศิษย์เก่า </u> แต่หาข้อมูลไม่พบ กรุณากดปุ่ม',
                                        'js_btn_not_found' => 'แจ้งไม่พบข้อมูล',
                                        'js_found_header' => 'พบข้อมูลที่อาจเป็นท่าน :count รายการ',
                                        'js_vipassana' => 'ศิษย์วิปัสสนา',
                                        'js_new_applicant' => 'ฉันเป็นผู้สมัครใหม่',
                                        'js_select_guide' =>
                                            'โปรดเลือกชื่อของท่าน หรือเลือก "ฉันผู้สมัครใหม่" แล้วกดปุ่มยืนยันด้านล่าง',
                                        'js_error_check' => 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล',
                                        'js_selected' =>
                                            'เลือกข้อมูลเรียบร้อยแล้ว: <br> <strong>:name</strong> <strong>:surname</strong> (อายุ :age ปี) <br> กรุณากดยืนยันว่าไม่ใช้หุ่นยนต์แล้วกดปุ่ม <br> "ส่งคำขอสมัครคอร์ส"',
                                        'js_selected_new' =>
                                            'เลือก: <strong>ผู้สมัครใหม่</strong> เรียบร้อยแล้ว <br> กรุณากดยืนยันว่าไม่ใช้หุ่นยนต์แล้วกดปุ่ม <br> "ส่งคำขอสมัครคอร์ส"',
                                        'js_age_format' => '(อายุ :age ปี)',
                                        'status_map' => [
                                            'ผู้สมัครใหม่' => 'ผู้สมัครใหม่',
                                            'ศิษย์อานาปานสติ' => 'ศิษย์อานาปานสติ',
                                            'ศิษย์เตโชวิปัสสนา' => 'ศิษย์วิปัสสนา',
                                            'ศิษย์อานาฯ ๑ วัน' => 'ศิษย์อานาฯ ๑ วัน',
                                        ],
                                    ],
                                    'en' => [
                                        'place' => 'Location',
                                        'course' => 'Course Category',
                                        'date' => 'Date',
                                        'status' => 'Status',
                                        'state_open' => 'Open',
                                        'state_soon' => 'Starting Soon',
                                        'state_closed' => 'Closed',
                                        'state_unknown' => 'Unknown',
                                        'form_header' => 'Application Form',
                                        'instructions' =>
                                            'Please fill in <strong>First Name</strong>, <strong>Last Name</strong>, <strong>Birth Date</strong> and <strong>Phone Number</strong>.',
                                        'gender' => 'Gender',
                                        'firstname' => 'First Name',
                                        'lastname' => 'Last Name',
                                        'birthdate' => 'Birth Date',
                                        'day' => 'Day',
                                        'month' => 'Month',
                                        'year' => 'Year',
                                        'year_ph' => 'Year (A.D.)', // Placeholder
                                        'phone' => 'Phone Number',
                                        'phone_placeholder' => 'Enter phone number e.g. 08xxxxxxxx',
                                        'check_member' => 'Check Member Info',
                                        'btn_check' => 'Check Info',
                                        'robot_check' => 'I am not a robot',
                                        'submit_btn' => 'Submit Application',
                                        'privacy_note' => '* Your data will be kept confidential.',
                                        'female' => 'Female',
                                        'male' => 'Male',
                                        'select_month' => 'Month',
                                        'msg_closed_online' => 'Online application for this course is closed.',
                                        'msg_contact_seat' =>
                                            'Please contact the foundation to check for available seats at:',
                                        'email_label' => 'Email',
                                        'tel_label' => 'Tel',
                                        // Modal & JS
                                        'modal_title' => 'Report Data Not Found',
                                        'modal_body' =>
                                            'The system will send a report to the staff. <br> Please verify your information so staff can contact you.',
                                        'modal_info_header' => 'Search Information:',
                                        'modal_name' => 'Name-Surname',
                                        'modal_gender' => 'Gender',
                                        'modal_dob' => 'Birth Date',
                                        'modal_phone' => 'Phone',
                                        'modal_email' => 'Email (Optional)',
                                        'modal_cancel' => 'Cancel',
                                        'modal_submit' => 'Confirm Report',
                                        'modal_sending' => 'Sending...',
                                        'modal_success' => 'Report received successfully.',
                                        'modal_contact_soon' => 'Staff will contact you shortly.',
                                        'modal_close' => 'Close Window',
                                        'modal_error' => 'Error sending data. Please try again.',
                                        'modal_phone_req' => 'Please enter phone number.',

                                        'js_fill_all' =>
                                            'Please fill in Name, Surname, Birth Date and Phone completely.',
                                        'js_not_found_header' => 'No existing data found',
                                        'js_new_member_msg' =>
                                            '<u>If you are a new member</u>, you can apply immediately. <br><u>If you are an old student</u> but cannot find data, please click',
                                        'js_btn_not_found' => 'Report Not Found',
                                        'js_found_header' => 'Found :count possible matches',
                                        'js_vipassana' => 'Vipassana Student',
                                        'js_new_applicant' => 'I am a new applicant',
                                        'js_select_guide' =>
                                            'Please select your name or "I am a new applicant" then click confirm below.',
                                        'js_error_check' => 'Error checking information.',
                                        'js_selected' =>
                                            'Selected: <br> <strong>:name</strong> <strong>:surname</strong> (Age :age Years) <br> Please confirm captcha and click <br> "Submit Application".',
                                        'js_selected_new' =>
                                            'Selected: <strong>New Applicant</strong>. <br> Please confirm you are not a robot and click <br> "Submit Application".',
                                        'js_age_format' => '(Age :age Years)',
                                        'status_map' => [
                                            'ผู้สมัครใหม่' => 'New Applicant',
                                            'ศิษย์อานาปานสติ' => 'Anapanasati Student',
                                            'ศิษย์เตโชวิปัสสนา' => 'Vipassana Student',
                                            'ศิษย์อานาฯ ๑ วัน' => '1-Day Anapanasati Student',
                                        ],
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

                                // Map state to display text
                                $stateMap = [
                                    'เปิดรับสมัคร' => ['text' => $txt['state_open'], 'class' => 'bg-success'],
                                    'ใกล้เริ่มแล้ว' => [
                                        'text' => $txt['state_soon'],
                                        'class' => 'bg-warning text-dark',
                                    ],
                                    'สิ้นสุดการรับสมัคร' => ['text' => $txt['state_closed'], 'class' => 'bg-secondary'],
                                ];

                                $currentState = $vm['state'] ?? null;
                                $displayState = $stateMap[$currentState]['text'] ?? ($currentState ?? '-'); // Fallback to raw state if no map
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>


                @if ($vm['state'] === 'ใกล้เริ่มแล้ว')
                    <div class="card shadow-sm border-warning mt-4">
                        <div class="card-body text-center p-4 p-md-5">
                            <h4 class="text-danger mb-3 fw-bold">
                                <i class="bi bi-x-circle-fill me-2"></i> {{ $txt['msg_closed_online'] }}
                            </h4>
                            <p class="fs-5 mb-4 text-muted">
                                {{ $txt['msg_contact_seat'] }}
                            </p>
                            <div class="d-inline-block text-start bg-light p-4 rounded-3 border">
                                <p class="mb-2 fs-5">
                                    <i class="bi bi-envelope-fill me-2 text-primary"></i>
                                    <strong>{{ $txt['email_label'] }}:</strong>
                                    <a href="mailto:info@bodhidhammayan.org"
                                        class="text-decoration-none fw-semibold">info@bodhidhammayan.org</a>
                                </p>
                                <p class="mb-0 fs-5">
                                    <i class="bi bi-telephone-fill me-2 text-success"></i>
                                    <strong>{{ $txt['tel_label'] }}:</strong>
                                    <a href="tel:021174063" class="text-decoration-none fw-semibold">02-117-4063</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif ($vm['is_open'])
                    {{-- ฟอร์มสมัคร (ไม่มี OTP แล้ว ใช้แค่ Captcha) --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ $txt['form_header'] }}</h5>
                        </div>
                        <div class="card-body">

                            {{-- แสดง error รวม --}}
                            @if ($errors->any())
                                <div class="alert alert-danger fs-5">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- แสดงข้อความสำเร็จจาก session (ถ้ามี) --}}
                            @if (session('status'))
                                <div class="alert alert-success fs-5">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <p class="text-muted fs-5">
                                {!! $txt['instructions'] !!}
                            </p>

                            {{-- เปลี่ยน action เป็น route สมัครจริง (ปรับตามหลังบ้าน) --}}
                            <form id="applyForm" class="row g-3" method="POST" action="{{ route('apply.direct.apply') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="member_id" id="member_id" value="">

                                <input type="hidden" name="lang" value="{{ $lang }}">

                                <div class="col-md-2">
                                    <label class="form-label fs-5">{{ $txt['gender'] }}</label>
                                    <select class="form-select form-control form-control-lg" id="gender" name="gender"
                                        required>
                                        <option value="หญิง" {{ old('gender') === 'หญิง' ? 'selected' : '' }}>
                                            {{ $txt['female'] }}
                                        </option>
                                        <option value="ชาย" {{ old('gender') === 'ชาย' ? 'selected' : '' }}>
                                            {{ $txt['male'] }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-5">{{ $txt['firstname'] }}</label>
                                    <input type="text" name="first_name" class="form-control form-control-lg"
                                        maxlength="100" required autocomplete="given-name" value="{{ old('first_name') }}">
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-5">{{ $txt['lastname'] }}</label>
                                    <input type="text" name="last_name" class="form-control form-control-lg"
                                        maxlength="100" required autocomplete="family-name" value="{{ old('last_name') }}">
                                </div>

                                @php
                                    $old = old('birth_date');
                                    if ($old && preg_match('/^\d{4}-\d{2}-\d{2}$/', $old)) {
                                        [$defY, $defM, $defD] = array_map('intval', explode('-', $old));
                                    } else {
                                        $defY = 1977;
                                        $defM = 1;
                                        $defD = 1;
                                    }
                                    $defBE = $defY + 543;

                                    if ($lang === 'th') {
                                        $displayMonths = [
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
                                        $displayMonths = [
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

                                {{-- วันเกิด เต็มแถว --}}
                                <div class="col-md-6">
                                    <label class="form-label fs-5">{{ $txt['birthdate'] }}</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select id="dob_day" class="form-select form-select-lg"
                                                aria-label="{{ $txt['day'] }}">
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ $d }}"
                                                        {{ $d === $defD ? 'selected' : '' }}>
                                                        {{ $d }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_month" class="form-select form-select-lg"
                                                aria-label="{{ $txt['month'] }}">
                                                <option value="">{{ $txt['select_month'] }}</option>
                                                @foreach ($displayMonths as $i => $name)
                                                    <option value="{{ $i + 1 }}"
                                                        {{ $i + 1 === $defM ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_year" class="form-select form-select-lg"
                                                aria-label="{{ $txt['year_ph'] }}">
                                                <option value="">{{ $txt['year_ph'] }}</option>
                                                @if ($lang === 'th')
                                                    @for ($year = now()->year - 15; $year >= 1945; $year--)
                                                        <option value="{{ $year }}"
                                                            {{ $year === $defY ? 'selected' : '' }}>
                                                            {{ $year + 543 }}
                                                        </option>
                                                    @endfor
                                                @else
                                                    @for ($year = now()->year - 15; $year >= 1945; $year--)
                                                        <option value="{{ $year }}"
                                                            {{ $year === $defY ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endfor
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="birth_date" id="birth_date"
                                        value="{{ sprintf('%04d-%02d-%02d', $defY, $defM, $defD) }}">
                                    <div class="form-text">เลือก วัน / เดือน / ปีเกิด (พ.ศ.)</div>
                                </div>

                                {{-- ข้อมูลติดต่อ --}}
                                {{-- <div class="col-md-6">
                                    <label class="form-label fs-5">{{ $txt['phone'] }}</label>
                                    <div class="mb-2">
                                        <input type="tel" id="phone" name="phone"
                                            class="form-control form-control-lg" maxlength="30" inputmode="tel"
                                            pattern="[0-9\s\-+]*" placeholder="{{ $txt['phone_placeholder'] }}"
                                            value="{{ old('phone') }}">
                                    </div> --}}
                                {{--                                    <div> --}}
                                {{--                                        <input type="email" --}}
                                {{--                                               name="email" --}}
                                {{--                                               class="form-control form-control-lg" --}}
                                {{--                                               maxlength="190" --}}
                                {{--                                               placeholder="อีเมล เช่น you@example.com" --}}
                                {{--                                               value="{{ old('email') }}"> --}}
                                {{--                                    </div> --}}
                                {{--                                    <div class="form-text"> --}}
                                {{--                                        อย่างน้อย 1 ช่องทาง (เบอร์โทรศัพท์ หรือ อีเมล) สำหรับติดต่อกลับ --}}
                                {{--                                    </div> --}}
                                {{-- </div> --}}

                                {{-- เส้นแบ่ง --}}
                                <div class="col-12">
                                    <hr>
                                </div>

                                {{-- ส่วนตรวจสอบสมาชิก --}}
                                <div class="col-12">
                                    <label class="form-label fs-5">{{ $txt['check_member'] }}</label>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button type="button" id="btnCheckMember"
                                            class="btn btn-info text-white btn-lg px-4">
                                            {{ $txt['btn_check'] }}
                                        </button>
                                    </div>
                                    <div id="search-result-area" class="mt-3"></div>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                {{-- Captcha --}}
                                <div class="col-12 d-none" id="recaptcha-section">
                                    <label class="form-label fs-5 d-block mb-2">{{ $txt['robot_check'] }}</label>

                                    @if (config('services.recaptcha.site_key'))
                                        <div class="mb-2">
                                            <div class="g-recaptcha"
                                                data-sitekey="{{ config('services.recaptcha.site_key') }}"
                                                data-callback="enableSubmit" data-expired-callback="disableSubmit">
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            ⚠ ยังไม่ได้ตั้งค่า <code>services.recaptcha.site_key</code> ในระบบ
                                        </div>
                                    @endif

                                    @error('g-recaptcha-response')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 d-none" id="submit-section">
                                    <button type="submit" id="submitBtn" class="btn btn-bodhi btn-lg px-4" disabled>
                                        {{ $txt['submit_btn'] }}
                                    </button>
                                </div>
                            </form>

                            <div class="text-muted mt-3" style="font-size:1.05rem;">
                                {{ $txt['privacy_note'] }}
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>


    {{-- Modal แจ้งไม่พบข้อมูล --}}
    <div class="modal fade" id="notFoundModal" tabindex="-1" aria-labelledby="notFoundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="notFoundModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $txt['modal_title'] }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        {!! $txt['modal_body'] !!}
                    </p>

                    <div class="alert alert-light border mb-3">
                        <strong>{{ $txt['modal_info_header'] }}</strong>
                        <ul class="mb-0 mt-1 small text-muted">
                            <li>{{ $txt['modal_name'] }}: <span id="modal-name"></span></li>
                            <li>{{ $txt['modal_gender'] }}: <span id="modal-gender"></span></li>
                            <li>{{ $txt['modal_dob'] }}: <span id="modal-dob"></span></li>
                        </ul>
                    </div>

                    {{-- <form id="notFoundForm">
                        <div class="mb-3">
                            <label for="modal-phone" class="form-label">{{ $txt['modal_phone'] }} <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="modal-phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-email" class="form-label">{{ $txt['modal_email'] }}</label>
                            <input type="email" class="form-control" id="modal-email" placeholder="name@example.com">
                        </div>
                    </form> --}}
                </div>
                <div class="modal-footer" id="notFoundModalFooter">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ $txt['modal_cancel'] }}</button>
                    <button type="button" class="btn btn-primary" onclick="submitNotFoundReport()">
                        {{ $txt['modal_submit'] }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Birth date helper --}}
    <script>
        (function() {
            const daySel = document.getElementById('dob_day');
            const monthSel = document.getElementById('dob_month');
            const yearSel = document.getElementById('dob_year');
            const hidden = document.getElementById('birth_date');

            function pad(n) {
                n = parseInt(n || 0, 10);
                return String(n).padStart(2, '0');
            }

            function maxDays(month, be) {
                month = parseInt(month || 0, 10);
                if (!month) return 31;
                if (month === 2) {
                    const ce = parseInt(be || 0, 10) - 543;
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
                const ce = parseInt(be, 10);
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

    <script>
        const TRANS = @json($txt);

        var isCaptchaValid = false;
        // Check if captcha is actually present on page. If not, we don't block on it.
        // PHP check: {{ config('services.recaptcha.site_key') ? 'true' : 'false' }} 
        // But better to check runtime if the widget container exists? 
        // Let's use the config value as source of truth for "Requiredness".
        var captchaRequired = {{ config('services.recaptcha.site_key') ? 'true' : 'false' }};

        // If not required, we initialize as true.
        if (!captchaRequired) {
            isCaptchaValid = true;
        }

        var isMemberChecked = false;

        function updateSubmitButton() {
            var btn = document.getElementById('submitBtn');
            if (btn) {
                // Enabled only if both are satisfied
                if (isCaptchaValid && isMemberChecked) {
                    btn.disabled = false;
                } else {
                    btn.disabled = true;
                }
            }
        }

        function enableSubmit() {
            isCaptchaValid = true;
            updateSubmitButton();
        }

        function disableSubmit() {
            isCaptchaValid = false;
            updateSubmitButton();
        }

        window.setMemberChecked = function(status) {
            isMemberChecked = status;

            // Toggle ReCaptcha visibility
            var recap = document.getElementById('recaptcha-section');
            if (recap) {
                if (status) {
                    recap.classList.remove('d-none');
                } else {
                    recap.classList.add('d-none');
                }
            }

            // Toggle Submit Section visibility
            var subSec = document.getElementById('submit-section');
            if (subSec) {
                if (status) {
                    subSec.classList.remove('d-none');
                } else {
                    subSec.classList.add('d-none');
                }
            }

            updateSubmitButton();
        };
    </script>
    {{-- Google reCAPTCHA --}}
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initial button state check
            if (typeof updateSubmitButton === 'function') {
                updateSubmitButton();
            }

            $('#btnCheckMember').click(function() {
                let gender = $('#gender').val();
                let firstName = $('input[name="first_name"]').val();
                let lastName = $('input[name="last_name"]').val();
                let birthDate = $('#birth_date').val();
                // let phone = $('#phone').val();

                if (!firstName || !lastName || !birthDate) {
                    alert(TRANS.js_fill_all);
                    return;
                }

                $('#search-result-area').html(
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                );

                // Reset status while searching
                if (window.setMemberChecked) window.setMemberChecked(false);

                $.ajax({
                    url: "{{ route('search.member') }}",
                    method: 'GET',
                    data: {
                        gender: gender,
                        first_name: firstName,
                        last_name: lastName,
                        birth_date: birthDate
                    },
                    success: function(res) {
                        let html = '';
                        if (res.count === 0) {
                            html = `<div class="alert alert-success d-flex align-items-center">
                                        <i class="bi bi-person-check-fill fs-3 me-3"></i>
                                        <div>
                                            <strong>${TRANS.js_not_found_header}</strong><br>
                                           ${TRANS.js_new_member_msg}
                                           <button type="button" class="btn btn-primary" onclick="showNotFoundModal()">${TRANS.js_btn_not_found}</button>

                                        </div>
                                    </div>`;
                            $('#member_id').val('');
                            // Auto-confirm for new user
                            if (window.setMemberChecked) window.setMemberChecked(true);

                        } else {
                            // count >= 1 (Found 1 or more)
                            html = `<div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            ${TRANS.js_found_header.replace(':count', res.count)}
                                        </div>
                                        <div class="list-group list-group-flush">`;

                            res.members.forEach(m => {
                                html += `<button type="button" class="list-group-item list-group-item-action" onclick="selectMember(${m.id}, '${m.name}', '${m.surname}', ${m.age_years})">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">${m.name} ${m.surname} ${TRANS.js_age_format.replace(':age', m.age_years)}</h5>
                                                <small>${TRANS.status_map[m.status] || m.status}</small>
                                            </div>
                                         </button>`;
                            });

                            html += `<button type="button" class="list-group-item list-group-item-action list-group-item-light fw-bold text-primary" onclick="selectNewMember()">
                                        <i class="bi bi-plus-circle"></i> ${TRANS.js_new_applicant}
                                     </button>`;

                            html += `</div></div>`;
                            html +=
                                `<div class="mt-2 text-muted small">${TRANS.js_select_guide}</div>`;
                        }

                        $('#search-result-area').html(html);
                    },
                    error: function(err) {
                        console.error(err);
                        $('#search-result-area').html(
                            `<div class="alert alert-danger">${TRANS.js_error_check}</div>`
                        );
                    }
                });
            });
        });

        function selectMember(id, name, surname, age) {
            $('#member_id').val(id);
            // Visual feedback
            $('#search-result-area').html(`<div class="alert alert-success">
                                            <i class="bi bi-check-circle-fill"></i> ${TRANS.js_selected.replace(':name', name).replace(':surname', surname).replace(':age', age)}
                                           </div>`);
            if (window.setMemberChecked) window.setMemberChecked(true);
        }

        function selectNewMember() {
            $('#member_id').val('');
            $('#search-result-area').html(`<div class="alert alert-success">
                                            <i class="bi bi-check-circle-fill"></i> ${TRANS.js_selected_new}
                                           </div>`);
            if (window.setMemberChecked) window.setMemberChecked(true);
        }

        function showNotFoundModal() {
            // Reset Footer
            $('#notFoundModalFooter').html(`
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${TRANS.modal_cancel}</button>
                <button type="button" class="btn btn-primary" onclick="submitNotFoundReport()">
                    ${TRANS.modal_submit}
                </button>
            `);

            // ดึงค่าจากฟอร์มหลัก
            let gender = $('#gender').val();
            let firstName = $('input[name="first_name"]').val();
            let lastName = $('input[name="last_name"]').val();
            let birthDate = $('#birth_date').val();
            // let phone = $('#phone').val();

            // แสดงใน Modal
            $('#modal-name').text(firstName + ' ' + lastName);
            $('#modal-gender').text(gender);
            $('#modal-dob').text(birthDate);
            // $('#modal-phone').val(phone); // Pre-fill phone

            $('#notFoundModal').modal('show');
        }

        function submitNotFoundReport() {
            // let phone = $('#modal-phone').val();
            // let email = $('#modal-email').val();

            // if (!phone) {
            //     alert(TRANS.modal_phone_req);
            //     return;
            // }

            // Disable button
            let btn = $('#notFoundModal .btn-primary');
            let oldText = btn.text();
            btn.prop('disabled', true).text(TRANS.modal_sending);
            let data = {
                _token: "{{ csrf_token() }}",
                gender: $('#gender').val(),
                first_name: $('input[name="first_name"]').val(),
                last_name: $('input[name="last_name"]').val(),
                birth_date: $('#birth_date').val(),
                // phone: "",
                // email: "",
            };

            // Fetch location data
            $.get('https://ipapi.co/json/')
                .done(function(locationData) {
                    // Update data with location info
                    data.city = locationData.city;
                    data.province = locationData.region;
                    data.country = locationData.country_name;
                    data.latitude = locationData.latitude;
                    data.longitude = locationData.longitude;

                    // Send report with location
                    sendReport(data, btn, oldText);
                })
                .fail(function() {
                    // Fallback: Send report without location
                    console.warn('Failed to fetch location data');
                    sendReport(data, btn, oldText);
                });
        }

        function sendReport(data, btn, oldText) {
            $.ajax({
                url: "{{ route('report.member') }}",
                method: 'POST',
                data: data,
                success: function(res) {
                    $('#notFoundModalFooter').html(`
                        <div class="d-flex flex-column align-items-center w-100">
                            <span class="text-success fw-bold mb-2">
                                <i class="bi bi-check-circle-fill me-1"></i> ${TRANS.modal_success}
                            </span>
                            <small class="text-muted mb-3">${TRANS.modal_contact_soon}</small>
                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">${TRANS.modal_close}</button>
                        </div>
                    `);
                },
                error: function(err) {
                    console.error(err);
                    alert(TRANS.modal_error);
                    btn.prop('disabled', false).text(oldText);
                }
            });
        }
    </script>
@endpush
