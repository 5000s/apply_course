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
                                @else
                                    <input type="hidden" name="member_id" id="member_id" value="">
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

                                {{-- ส่วนตรวจสอบสมาชิก --}}
                                <div class="col-12">
                                    <label class="form-label fs-5">ตรวจสอบข้อมูลสมาชิก</label>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button type="button" id="btnCheckMember"
                                            class="btn btn-info text-white btn-lg px-4">
                                            ตรวจสอบข้อมูล
                                        </button>
                                    </div>
                                    <div id="search-result-area" class="mt-3"></div>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                {{-- Captcha --}}
                                <div class="col-12 d-none" id="recaptcha-section">
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

                                <div class="col-12 d-none" id="submit-section">
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

                if (!firstName || !lastName || !birthDate) {
                    alert('กรุณากรอกข้อมูล ชื่อ นามสกุล และ วันเกิด ให้ครบถ้วน');
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
                                            <strong>ไม่พบข้อมูลเดิม</strong><br>
                                            ท่านเป็นสมาชิกใหม่ สามารถกดสมัครได้ทันที
                                        </div>
                                    </div>`;
                            $('#member_id').val('');
                            // Auto-confirm for new user
                            if (window.setMemberChecked) window.setMemberChecked(true);

                        } else if (res.count === 1) {
                            let m = res.members[0];
                            html = `<div class="alert alert-info">
                                        <h5><i class="bi bi-info-circle-fill"></i> ท่านเป็นศิษย์เก่า</h5>
                                        <p class="mb-2">ระบบพบข้อมูล: <strong>${m.name} ${m.surname}</strong> (รหัส: ${m.id})</p>
                                        <button type="button" class="btn btn-primary" onclick="selectMember(${m.id}, '${m.name}')">
                                            ใช้ข้อมูลนี้สมัคร
                                        </button>
                                    </div>`;

                        } else {
                            html = `<div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            พบข้อมูลที่อาจเป็นท่าน ${res.count} รายการ
                                        </div>
                                        <div class="list-group list-group-flush">`;

                            res.members.forEach(m => {
                                html += `<button type="button" class="list-group-item list-group-item-action" onclick="selectMember(${m.id}, '${m.name}')">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">${m.name} ${m.surname}</h5>
                                                <small>${m.status}</small>
                                            </div>
                                            <p class="mb-1">เบอร์โทร: ${m.phone || '-'}</p>
                                         </button>`;
                            });

                            html += `<button type="button" class="list-group-item list-group-item-action list-group-item-light fw-bold text-primary" onclick="selectNewMember()">
                                        <i class="bi bi-plus-circle"></i> ฉันเป็นผู้สมัครใหม่
                                     </button>`;

                            html += `</div></div>`;
                            html +=
                                `<div class="mt-2 text-muted small">โปรดเลือกชื่อของท่าน หรือเลือก "ฉันผู้สมัครใหม่" แล้วกดปุ่มยืนยันด้านล่าง</div>`;
                        }

                        $('#search-result-area').html(html);
                    },
                    error: function(err) {
                        console.error(err);
                        $('#search-result-area').html(
                            '<div class="alert alert-danger">เกิดข้อผิดพลาดในการตรวจสอบข้อมูล</div>'
                        );
                    }
                });
            });
        });

        function selectMember(id, name) {
            $('#member_id').val(id);
            // Visual feedback
            $('#search-result-area').html(`<div class="alert alert-success">
                                            <i class="bi bi-check-circle-fill"></i> เลือกข้อมูล: <strong>${name}</strong> เรียบร้อยแล้ว <br>
                                            กรุณากรอกข้อมูลส่วนอื่นให้ครบถ้วนแล้วกดปุ่ม "ส่งคำขอสมัครคอร์ส"
                                           </div>`);
            if (window.setMemberChecked) window.setMemberChecked(true);
        }

        function selectNewMember() {
            $('#member_id').val('');
            $('#search-result-area').html(`<div class="alert alert-success">
                                            <i class="bi bi-check-circle-fill"></i> เลือก: <strong>ผู้สมัครใหม่</strong> เรียบร้อยแล้ว <br>
                                            กรุณากรอกข้อมูลส่วนอื่นให้ครบถ้วนแล้วกดปุ่ม "ส่งคำขอสมัครคอร์ส"
                                           </div>`);
            if (window.setMemberChecked) window.setMemberChecked(true);
        }
    </script>
@endpush
