{{-- resources/views/apply/direct.blade.php --}}
@extends('layouts.bodhi')

@section('title', 'สมัครคอร์ส: '.$course->title)

@push('head')
    <style>
        .hero-image-wrap{ width:100%; height:240px; overflow:hidden; border-top-left-radius:1rem; border-top-right-radius:1rem; }
        .hero-image{ width:100%; height:100%; object-fit:cover; display:block; }
        @media (min-width:992px){ .hero-image-wrap{ height:300px; } }

        /* ปุ่มเลือกช่องทาง รหัสสำหรับสมัคร */
        .otp-channel .btn { border-width: 2px; font-weight: 700; padding: .65rem 1.25rem; }
        .otp-channel .btn-outline-bodhi{ color:#2b2b2b; background:#fff; border-color:#c9c9c9; }
        .otp-channel .btn-outline-bodhi:hover{ background:#fff7e6; border-color:var(--bodhi-gold); }
        .otp-channel .btn-check:checked + .btn-outline-bodhi,
        .otp-channel .btn-outline-bodhi.active{
            color:#fff !important; background:var(--bodhi-gold) !important; border-color:var(--bodhi-gold-2) !important;
            box-shadow:0 0 0 3px rgba(201,167,80,.25) inset;
        }
        .otp-channel .btn-outline-bodhi:focus-visible{ outline:3px solid rgba(36,75,60,.4); outline-offset:2px; }

        /* ทำให้ปุ่มสูงเท่า input และสองปุ่มกว้างเท่ากัน */
        :root { --control-lg-h: calc(1.5em + 1rem + 2px); }
        .otp-line { display:flex; align-items:stretch; gap:.75rem; flex-wrap:nowrap; }
        @media (max-width: 767.98px){ .otp-line { flex-wrap:wrap; } }
        .otp-channel{
            display:flex; align-items:stretch; min-width:360px; max-width:360px;
            border-radius:.5rem; overflow:hidden; height:var(--control-lg-h);
        }
        .otp-channel .otp-btn{
            flex:1 0 0; display:flex; align-items:center; justify-content:center;
            padding-top:0; padding-bottom:0; line-height:1.5; height:100%; font-size:1.25rem; border-width:1px;
        }
        .otp-channel .otp-btn:first-of-type{ border-right-width:0; }
        .otp-channel .otp-btn + .otp-btn{ margin-left:0; }
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
                                $th = function($d){
                                    if(!$d) return '-';
                                    $c = \Illuminate\Support\Carbon::parse($d)->locale('th');
                                    return $c->translatedFormat('j F').' '.($c->year + 543);
                                };
                                $badgeClass = match(true) {
                                    ($vm['state'] ?? null) === 'เปิดรับสมัคร'        => 'bg-success',
                                    ($vm['state'] ?? null) === 'ใกล้เริ่มแล้ว'        => 'bg-warning text-dark',
                                    ($vm['state'] ?? null) === 'สิ้นสุดการรับสมัคร'  => 'bg-secondary',
                                    default                                            => 'bg-light text-dark'
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
                                        <td class="fs-5">{{ $course_cat->show_name ?? ($course_cat->name ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted fw-semibold">วันที่</th>
                                        <td class="fs-5">
                                            @if($course->date_start != $course->date_end)
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
                                            <span class="badge {{ $badgeClass }} px-3 py-2">{{ $vm['state'] ?? '-' }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                @if($vm['is_open'])

                    {{-- ฟอร์มสมัคร + รหัสสำหรับสมัคร --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">กรอกข้อมูลเพื่อขอ/ใช้รหัสสำหรับสมัครคอร์ส</h5>
                        </div>
                        <div class="card-body">
                            <div id="alertBox" class="alert d-none fs-5" role="alert" aria-live="polite"></div>
                            <div id="successBox" class="alert alert-success d-none fs-5" role="alert">
                                ✅ ดำเนินการสำเร็จ ขอบพระคุณค่ะ/ครับ
                            </div>

                            <p class="text-muted fs-5">
                                กรุณากรอก <strong>ชื่อ</strong>, <strong>นามสกุล</strong>, <strong>วันเกิด</strong> และเลือก
                                <strong>เบอร์โทรศัพท์</strong> หรือ <strong>อีเมล</strong> เพื่อ
                                <u>ขอรับรหัสสำหรับสมัคร</u> หรือ <u>ใช้รหัสที่มีอยู่แล้ว</u> ในการยืนยันการสมัครเข้าคอร์ส
                            </p>

                            <form id="applyForm" class="row g-3" method="POST" action="{{ route('apply.direct.requestOtp') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                @auth <input type="hidden" name="member_id" value="{{ auth()->id() }}"> @endauth
                                <input type="hidden" name="recipient" id="otpRecipient">

                                <div class="col-md-2">

                                    <label class="form-label fs-5">เพศ</label>
                                    <select class="form-select form-control form-control-lg" id="gender" name="gender" required>
                                        <option value="หญิง">{{ __('messages.female') }}</option>
                                        <option value="ชาย">{{ __('messages.male') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fs-5">ชื่อ</label>
                                    <input type="text" name="first_name" class="form-control form-control-lg" maxlength="100" required autocomplete="given-name">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fs-5">นามสกุล</label>
                                    <input type="text" name="last_name" class="form-control form-control-lg" maxlength="100" required autocomplete="family-name">
                                </div>

                                @php
                                    $old = old('birth_date');
                                    if ($old && preg_match('/^\d{4}-\d{2}-\d{2}$/', $old)) {
                                        [$defY,$defM,$defD] = array_map('intval', explode('-', $old));
                                    } else { $defY=1977; $defM=1; $defD=1; }
                                    $defBE = $defY + 543;
                                    $thMonths = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
                                @endphp

                                {{-- แถวเดียวกัน: วันเกิด (ซ้าย) + กรอกรหัสสำหรับสมัคร (ขวา) --}}
                                <div class="col-md-6">
                                    <label class="form-label fs-5">วันเกิด</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select id="dob_day" class="form-select form-select-lg" aria-label="วัน">
                                                @for($d=1;$d<=31;$d++)
                                                    <option value="{{ $d }}" {{ $d===$defD ? 'selected' : '' }}>{{ $d }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_month" class="form-select form-select-lg" aria-label="เดือน">
                                                <option value="">เดือน</option>
                                                @foreach($thMonths as $i=>$name)
                                                    <option value="{{ $i+1 }}" {{ ($i+1)===$defM ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_year" class="form-select form-select-lg" aria-label="ปี (พ.ศ.)">
                                                <option value="">ปี (พ.ศ.)</option>
                                                @for($be = now()->year + 543; $be >= 2443; $be--)
                                                    <option value="{{ $be }}" {{ $be===$defBE ? 'selected' : '' }}>{{ $be }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="birth_date" id="birth_date" value="{{ sprintf('%04d-%02d-%02d',$defY,$defM,$defD) }}">
                                    <div class="form-text">เลือก วัน / เดือน / ปีเกิด (พ.ศ.)</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-5">กรอกรหัสสำหรับสมัคร</label>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control form-control-lg" id="otpCode" name="code" maxlength="6"
                                               inputmode="numeric" pattern="[0-9]*" placeholder="กรอกรหัสเลข 4 หลัก">
                                        <button id="btnConfirmOtp" class="btn btn-success btn-lg" type="button">ยืนยันการสมัคร</button>
                                    </div>
                                    <div class="text-muted" style="font-size:1.05rem;">
                                        • ใช้ <strong>รหัสเดิม</strong> ที่เคยได้รับเพื่อยืนยันการสมัครได้<br>
                                        • หากไม่ทราบ/ทำรหัสหาย สามารถขอรหัสใหม่ได้ด้านล่าง<br>
                                        • <strong>รหัสจะเชื่อมกับชื่อของผู้สมัครเท่านั้น</strong>
                                    </div>
                                </div>

                                {{-- เส้นแบ่งก่อนส่วน "ขอรับรหัส" --}}
                                <div class="col-12"><hr></div>

                                {{-- ส่วนขอรับรหัสใหม่ --}}
                                <div class="col-12">
                                    <label class="form-label fs-5 mb-2 d-block">ต้องการรับรหัสสำหรับสมัคร ทาง</label>

                                    <div class="otp-line">
                                        {{-- Toggle ช่องทาง --}}
                                        <div class="otp-channel btn-group" role="group" aria-label="ช่องทางรับรหัส">
                                            <input type="radio" class="btn-check" name="channel" id="chPhone" value="phone" checked>
                                            <label class="btn btn-outline-bodhi otp-btn" for="chPhone">เบอร์โทรศัพท์</label>

                                            <input type="radio" class="btn-check" name="channel" id="chEmail" value="email">
                                            <label class="btn btn-outline-bodhi otp-btn" for="chEmail">อีเมล</label>
                                        </div>

                                        {{-- Phone input --}}
                                        <div class="flex-grow-1" id="phoneWrap">
                                            <input type="tel" name="phone" class="form-control form-control-lg"
                                                   maxlength="30" inputmode="tel" pattern="[0-9\s\-+]*"
                                                   placeholder="ตัวอย่าง 08xxxxxxx" autocomplete="tel">
                                            <div class="form-text">รหัสสำหรับสมัครจะถูกส่งไปยังหมายเลขนี้</div>
                                        </div>

                                        {{-- Email input --}}
                                        <div class="flex-grow-1 d-none" id="emailWrap">
                                            <input type="email" name="email" class="form-control form-control-lg"
                                                   maxlength="190" placeholder="เช่น you@example.com" autocomplete="email">
                                            <div class="form-text">รหัสสำหรับสมัครจะถูกส่งไปยังอีเมลนี้</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button id="btnRequestOtp" type="button" class="btn btn-bodhi btn-lg px-4">
                                        ขอรับรหัสสำหรับสมัคร (ขอรหัสใหม่)
                                    </button>
                                </div>
                            </form>

                            <div class="text-muted mt-3" style="font-size:1.05rem;">
                                * ข้อมูลของท่านจะถูกเก็บรักษาเป็นความลับ ใช้เฉพาะการยืนยันการสมัคร
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function(){
            const daySel   = document.getElementById('dob_day');
            const monthSel = document.getElementById('dob_month');
            const yearSel  = document.getElementById('dob_year');
            const hidden   = document.getElementById('birth_date');

            function pad(n){ n = parseInt(n||0,10); return String(n).padStart(2,'0'); }
            function maxDays(month, be){
                month = parseInt(month||0,10); if(!month) return 31;
                if (month === 2){
                    const ce = parseInt(be||0,10) - 543;
                    if (!ce) return 28;
                    const leap = (ce%4===0 && ce%100!==0) || (ce%400===0);
                    return leap ? 29 : 28;
                }
                return [4,6,9,11].includes(month) ? 30 : 31;
            }
            function clampDay(){
                const m  = monthSel.value;
                const be = yearSel.value;
                const max = maxDays(m, be);
                if (parseInt(daySel.value,10) > max) daySel.value = String(max);
                updateHidden();
            }
            function updateHidden(){
                const d  = daySel.value;
                const m  = monthSel.value;
                const be = yearSel.value;
                if (!d || !m || !be) { hidden.value = ''; return; }
                const ce = parseInt(be,10) - 543;
                hidden.value = `${ce}-${pad(m)}-${pad(d)}`;
            }
            monthSel.addEventListener('change', clampDay);
            yearSel.addEventListener('change',  clampDay);
            daySel.addEventListener('change',   updateHidden);
            clampDay();

            const btnReq = document.getElementById('btnRequestOtp');
            const btnCfm = document.getElementById('btnConfirmOtp');
            if (btnReq) btnReq.addEventListener('click', updateHidden, {capture:true});
            if (btnCfm) btnCfm.addEventListener('click', updateHidden, {capture:true});
        })();
    </script>

    <script>
        (function(){
            const form           = document.getElementById('applyForm');
            if (!form) return;

            const alertBox       = document.getElementById('alertBox');
            const successBox     = document.getElementById('successBox');

            const btnRequestOtp  = document.getElementById('btnRequestOtp');
            const btnConfirmOtp  = document.getElementById('btnConfirmOtp');
            const otpCodeInput   = document.getElementById('otpCode');

            const recipientInput = document.getElementById('otpRecipient');
            const phoneWrap      = document.getElementById('phoneWrap');
            const emailWrap      = document.getElementById('emailWrap');
            const phoneInput     = form.querySelector('input[name="phone"]');
            const emailInput     = form.querySelector('input[name="email"]');
            const channelRadios  = form.querySelectorAll('input[name="channel"]');

            const requestUrl     = form.getAttribute('action'); // apply.direct.requestOtp
            const confirmUrl     = "{{ route('apply.direct.confirm') }}";

            function resetBoxes(){
                if (alertBox){
                    alertBox.classList.add('d-none');
                    alertBox.classList.remove('alert-danger');
                    alertBox.textContent = '';
                }
                if (successBox){
                    successBox.classList.add('d-none');
                    successBox.textContent = '';
                }
            }
            function showError(message){
                if (!alertBox) return;
                alertBox.textContent = message || 'เกิดข้อผิดพลาด';
                alertBox.classList.add('alert-danger');
                alertBox.classList.remove('d-none');
            }
            function showSuccess(message){
                if (!successBox) return;
                successBox.textContent = message || 'ดำเนินการสำเร็จ';
                successBox.classList.remove('d-none');
            }
            function firstError(errors){
                if (!errors) return '';
                for (const key of Object.keys(errors)){
                    if (errors[key] && errors[key].length){
                        return errors[key][0];
                    }
                }
                return '';
            }

            function toggleRecipientFields(){
                if (!phoneWrap || !emailWrap) return;
                const selected = form.querySelector('input[name="channel"]:checked');
                const usePhone = !selected || selected.value === 'phone';
                phoneWrap.classList.toggle('d-none', !usePhone);
                emailWrap.classList.toggle('d-none', usePhone);
                if (phoneInput) phoneInput.required = usePhone;
                if (emailInput) emailInput.required = !usePhone;

                // ไม่ซ่อนส่วนกรอกรหัสอีกต่อไป
                if (recipientInput) recipientInput.value = '';
                if (otpCodeInput) otpCodeInput.value = '';
            }
            channelRadios.forEach(r => r.addEventListener('change', toggleRecipientFields));
            toggleRecipientFields();

            async function submitRequest(){
                if (!btnRequestOtp || !requestUrl) return;
                if (!form.reportValidity()) return;

                resetBoxes();
                const originalText = btnRequestOtp.textContent;
                btnRequestOtp.disabled = true;
                btnRequestOtp.textContent = 'กำลังขอรหัส...';

                try{
                    const formData = new FormData(form);
                    const response = await fetch(requestUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });
                    const payload = await response.json().catch(() => null);
                    if (!response.ok || !payload || payload.ok === false){
                        const message = payload?.message || firstError(payload?.errors) || 'ไม่สามารถออก “รหัสสำหรับสมัคร” ได้';
                        throw new Error(message);
                    }

                    const msg = (payload.message || 'ออก “รหัสสำหรับสมัคร” ใหม่สำเร็จ.')
                        + (payload.recipient ? ` (ส่งไปที่ ${payload.recipient})` : '');
                    showSuccess(msg);
                    if (recipientInput) recipientInput.value = payload.recipient_raw || '';
                    if (otpCodeInput) otpCodeInput.focus();
                } catch (error){
                    if (recipientInput) recipientInput.value = '';
                    showError(error.message || 'Unexpected error.');
                } finally {
                    btnRequestOtp.disabled = false;
                    btnRequestOtp.textContent = originalText;
                }
            }

            async function submitConfirm(){
                if (!btnConfirmOtp || !confirmUrl) return;
                if (!form.reportValidity()) return;

                // ถ้าหลังบ้านไม่ต้องใช้ recipient แล้ว ลบบล็อกนี้ได้
                if (recipientInput && !recipientInput.value){
                    showError('กรุณาเลือกช่องทางและกด “ขอรับรหัสสำหรับสมัคร (ขอรหัสใหม่)” ก่อนยืนยัน');
                    return;
                }
                if (!otpCodeInput || !otpCodeInput.value.trim()){
                    if (otpCodeInput) otpCodeInput.focus();
                    showError('กรุณากรอกรหัสสำหรับสมัคร');
                    return;
                }

                resetBoxes();
                const originalText = btnConfirmOtp.textContent;
                btnConfirmOtp.disabled = true;
                btnConfirmOtp.textContent = 'กำลังยืนยัน...';

                try{
                    const formData = new FormData(form);
                    if (recipientInput) formData.set('recipient', recipientInput.value);
                    formData.set('code', (otpCodeInput.value || '').trim());

                    const response = await fetch(confirmUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });
                    const payload = await response.json().catch(() => null);
                    if (!response.ok || !payload || payload.ok === false){
                        const message = payload?.message || firstError(payload?.errors) || 'ไม่สามารถยืนยันรหัสสำหรับสมัครได้';
                        throw new Error(message);
                    }

                    showSuccess(payload.message || 'ยืนยันรหัสสำหรับสมัครเรียบร้อย');
                    btnConfirmOtp.textContent = 'ยืนยันสำเร็จ';
                } catch (error){
                    showError(error.message || 'Unexpected error.');
                    btnConfirmOtp.disabled = false;
                    btnConfirmOtp.textContent = originalText;
                }
            }

            if (btnRequestOtp) btnRequestOtp.addEventListener('click', submitRequest);
            if (btnConfirmOtp) btnConfirmOtp.addEventListener('click', submitConfirm);
        })();
    </script>
@endpush
