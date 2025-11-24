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
                                $th = function ($d) {
                                    if (!$d) {
                                        return '-';
                                    }
                                    $c = \Illuminate\Support\Carbon::parse($d)->locale('th');
                                    return $c->translatedFormat('j F') . ' ' . ($c->year + 543);
                                };
                                $badgeClass = match (true) {
                                    ($vm['state'] ?? null) === 'เปิดรับสมัคร' => 'bg-success',
                                    ($vm['state'] ?? null) === 'ใกล้เริ่มแล้ว' => 'bg-warning text-dark',
                                    ($vm['state'] ?? null) === 'สิ้นสุดการรับสมัคร' => 'bg-secondary',
                                    default => 'bg-light text-dark',
                                };
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <colgroup>
                                        <col style="width: 100px">
                                        <col>
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">สถานที่</th>
                                            <td class="fs-5">{{ $vm['place_name'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">คอร์ส</th>
                                            <td class="fs-5">{{ $course_cat->show_name ?? ($course_cat->name ?? '-') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">วันที่</th>
                                            <td class="fs-5">
                                                @if ($course->date_start != $course->date_end)
                                                    {{ $th($course->date_start ?? $course->start_date) }} –
                                                    {{ $th($course->date_end ?? $course->end_date) }}
                                                @else
                                                    {{ $th($course->date_start ?? $course->start_date) }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-muted fw-semibold">สถานะ</th>
                                            <td class="fs-5">
                                                <span
                                                    class="badge {{ $badgeClass }} px-3 py-2">{{ $vm['state'] ?? '-' }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                @if ($vm['is_open'])

                    {{-- ฟอร์มสมัคร (ไม่มี OTP แล้ว ใช้แค่ Captcha) --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">กรอกข้อมูลเพื่อสมัครคอร์ส</h5>
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
                                กรุณากรอก <strong>ชื่อ</strong>, <strong>นามสกุล</strong>, <strong>วันเกิด</strong> และ
                                <strong>เบอร์โทรศัพท์</strong> แล้วกดปุ่มส่งคำขอสมัครคอร์ส
                            </p>

                            {{-- เปลี่ยน action เป็น route สมัครจริง (ปรับตามหลังบ้าน) --}}
                            <form id="applyForm" class="row g-3" method="POST" action="{{ route('apply.direct.apply') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                @auth
                                    <input type="hidden" name="member_id" value="{{ auth()->id() }}">
                                @endauth

                                <div class="col-md-2">
                                    <label class="form-label fs-5">เพศ</label>
                                    <select class="form-select form-control form-control-lg" id="gender" name="gender"
                                        required>
                                        <option value="หญิง" {{ old('gender') === 'หญิง' ? 'selected' : '' }}>
                                            {{ __('messages.female') }}
                                        </option>
                                        <option value="ชาย" {{ old('gender') === 'ชาย' ? 'selected' : '' }}>
                                            {{ __('messages.male') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-5">ชื่อ</label>
                                    <input type="text" name="first_name" class="form-control form-control-lg"
                                        maxlength="100" required autocomplete="given-name" value="{{ old('first_name') }}">
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-5">นามสกุล</label>
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
                                    $thMonths = [
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
                                @endphp

                                {{-- วันเกิด เต็มแถว --}}
                                <div class="col-md-6">
                                    <label class="form-label fs-5">วันเกิด</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select id="dob_day" class="form-select form-select-lg" aria-label="วัน">
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ $d }}"
                                                        {{ $d === $defD ? 'selected' : '' }}>
                                                        {{ $d }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_month" class="form-select form-select-lg" aria-label="เดือน">
                                                <option value="">เดือน</option>
                                                @foreach ($thMonths as $i => $name)
                                                    <option value="{{ $i + 1 }}"
                                                        {{ $i + 1 === $defM ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_year" class="form-select form-select-lg"
                                                aria-label="ปี (พ.ศ.)">
                                                <option value="">ปี (พ.ศ.)</option>
                                                @for ($year = now()->year - 15; $year >= 1945; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $year === $defBE ? 'selected' : '' }}>
                                                        {{ $year + 543 }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="birth_date" id="birth_date"
                                        value="{{ sprintf('%04d-%02d-%02d', $defY, $defM, $defD) }}">
                                    <div class="form-text">เลือก วัน / เดือน / ปีเกิด (พ.ศ.)</div>
                                </div>

                                {{-- ข้อมูลติดต่อ --}}
                                <div class="col-md-6">
                                    <label class="form-label fs-5">เบอร์โทรศัพท์</label>
                                    <div class="mb-2">
                                        <input type="tel" name="phone" class="form-control form-control-lg"
                                            maxlength="30" inputmode="tel" pattern="[0-9\s\-+]*"
                                            placeholder="เบอร์โทรศัพท์ เช่น 08xxxxxxxx" value="{{ old('phone') }}">
                                    </div>
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
                                </div>

                                {{-- เส้นแบ่ง --}}
                                <div class="col-12">
                                    <hr>
                                </div>

                                {{-- Captcha --}}
                                <div class="col-12">
                                    <label class="form-label fs-5 d-block mb-2">ยืนยันว่าไม่ใช้หุ่นยนต์</label>

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

                                <div class="col-12">
                                    <button type="submit" id="submitBtn" class="btn btn-bodhi btn-lg px-4" disabled>
                                        ส่งคำขอสมัครคอร์ส
                                    </button>
                                </div>
                            </form>

                            <div class="text-muted mt-3" style="font-size:1.05rem;">
                                * ข้อมูลของท่านจะถูกเก็บรักษาเป็นความลับ ใช้เฉพาะการจัดการใบสมัครคอร์ส
                            </div>
                        </div>
                    </div>

                @endif
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
        function enableSubmit() {
            document.getElementById('submitBtn').disabled = false;
        }

        function disableSubmit() {
            document.getElementById('submitBtn').disabled = true;
        }
    </script>
    {{-- Google reCAPTCHA --}}
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
