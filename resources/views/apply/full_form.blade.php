@extends('layouts.bodhi')

@section('title', 'แบบฟอร์มสมัครคอร์ส: ' . $course->coursename)

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
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('apply.form.confirm', [$course->id, $member->id]) }}">
                    @csrf
                    @method('PUT')

                    {{-- ข้อมูลผู้สมัคร --}}
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">ข้อมูลผู้สมัคร</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อ</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $member->name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" name="surname" class="form-control"
                                        value="{{ old('surname', $member->surname) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">โทรศัพท์</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $member->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">โทรศัพท์ที่ 2</label>
                                    <input type="text" name="phone_2" class="form-control"
                                        value="{{ old('phone_2', $member->phone_2) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">เพศ</label>
                                    <select name="gender" class="form-select">
                                        <option value="หญิง"
                                            {{ old('gender', $member->gender) == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                        <option value="ชาย"
                                            {{ old('gender', $member->gender) == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ไลน์</label>
                                    <input type="text" name="line" class="form-control"
                                        value="{{ old('line', $member->line) }}">
                                </div>

                                <div class="col-md-6">
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
                                    <label class="form-label">วันเกิด</label>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select id="dob_day" class="form-select" aria-label="วัน">
                                                @for ($d = 1; $d <= 31; $d++)
                                                    <option value="{{ $d }}"
                                                        {{ $d === $defD ? 'selected' : '' }}>
                                                        {{ $d }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select id="dob_month" class="form-select" aria-label="เดือน">
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
                                            <select id="dob_year" class="form-select" aria-label="ปี (พ.ศ.)">
                                                <option value="">ปี (พ.ศ.)</option>
                                                @for ($year = now()->year - 15; $year >= 1945; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $year === $defY ? 'selected' : '' }}>
                                                        {{ $year + 543 }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="birthdate" id="birthdate"
                                        value="{{ sprintf('%04d-%02d-%02d', $defY, $defM, $defD) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">สัญชาติ</label>
                                    <select name="nationality" class="form-select">
                                        <option value="">-- เลือกสัญชาติ --</option>
                                        @foreach ($nations as $nation)
                                            <option value="{{ $nation }}"
                                                {{ old('nationality', $member->nationality) == $nation ? 'selected' : '' }}>
                                                {{ $nation }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{ $member->nationality }}
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">จังหวัดที่อยู่ในไทย {{ $member->province }}</label>
                                    <select name="province" class="form-select">
                                        <option value="">-- เลือกจังหวัด --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['name_th'] }}"
                                                {{ old('province', $member->province) == $province['name_th'] ? 'selected' : '' }}>
                                                {{ $province['name_th'] }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ประเทศ</label>
                                    <input type="text" name="country" class="form-control"
                                        value="{{ old('country', $member->country ?? 'Thailand') }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">โรคประจำตัว / ภาวะสุขภาพที่ควรแจ้ง</label>
                                    <textarea name="disease" class="form-control" rows="2">{{ old('disease', $member->blacklist_remark) }}</textarea>
                                    {{-- ถ้าคุณใช้ column อื่นสำหรับโรคประจำตัว ให้ปรับตรงนี้ --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- การศึกษา และ อาชีพ --}}
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">การศึกษา และ อาชีพ</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ระดับการศึกษา</label>
                                    <input type="text" name="degree" class="form-control"
                                        value="{{ old('degree', $member->degree) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">องค์กร</label>
                                    <input type="text" name="organization" class="form-control"
                                        value="{{ old('organization', $member->organization) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">อาชีพ</label>
                                    <input type="text" name="career" class="form-control"
                                        value="{{ old('career', $member->career) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ความเชี่ยวชาญ</label>
                                    <input type="text" name="expertise" class="form-control"
                                        value="{{ old('expertise', $member->expertise) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ผู้ติดต่อฉุกเฉิน --}}
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">ผู้ติดต่อฉุกเฉิน</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                    <input type="text" name="name_emergency" class="form-control"
                                        value="{{ old('name_emergency', $member->name_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุลผู้ติดต่อฉุกเฉิน</label>
                                    <input type="text" name="surname_emergency" class="form-control"
                                        value="{{ old('surname_emergency', $member->surname_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                    <input type="text" name="phone_emergency" class="form-control"
                                        value="{{ old('phone_emergency', $member->phone_emergency) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ความสัมพันธ์</label>
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
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">วิธีการเดินทาง</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="van" id="van0"
                                        value="0" checked>
                                    <label class="form-check-label" for="van0">
                                        เดินทางด้วยตนเอง
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="van" id="van1"
                                        value="1">
                                    <label class="form-check-label" for="van1">
                                        โดยรถตู้ที่จัดเตรียมให้ สำหรับไปยัง อ.แก่งคอย จ.สระบุรี ขึ้นรถที่อ่อนนุช ซ.8
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            ยืนยันการสมัครเข้าคอร์ส
                        </button>
                    </div>

                </form>
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
