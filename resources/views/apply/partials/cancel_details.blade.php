{{-- กล่องรายละเอียดผู้สมัคร/คอร์ส สำหรับหน้า-อีเมลยกเลิก (ต้องมี $apply, $course, $location, $courseCategory, $lang) --}}
@php
    $isTh = ($lang ?? 'th') === 'th';
    $member = $apply->member;

    // คำนำหน้าชื่อตามสถานะ (buddhism) เหมือนอีเมลยืนยันการสมัคร
    $namePrefix = ($isTh
        ? ['ฆราวาส' => 'คุณ', 'ภิกษุ' => 'พระคุณเจ้า', 'แม่ชี' => 'แม่ชี']
        : ['ฆราวาส' => $member->gender === 'ชาย' ? 'Mr.' : 'Ms.', 'ภิกษุ' => 'Venerable', 'แม่ชี' => 'Maechee'])[$member->buddhism] ?? '';

    $start = \Carbon\Carbon::parse($course->date_start)->locale($isTh ? 'th' : 'en');
    $end = \Carbon\Carbon::parse($course->date_end)->locale($isTh ? 'th' : 'en');
    $year = $isTh ? $start->year + 543 : $start->year;
    if ($start->isSameDay($end)) {
        $dateStr = $start->translatedFormat('j F') . ' ' . $year;
    } elseif ($start->isSameMonth($end)) {
        $dateStr = $start->day . '-' . $end->translatedFormat('j F') . ' ' . $year;
    } else {
        $dateStr = $start->translatedFormat('j F') . ' - ' . $end->translatedFormat('j F') . ' ' . $year;
    }

    $courseTitle = $isTh
        ? $courseCategory->show_name ?? $course->category
        : $courseCategory->show_name_en ?? ($courseCategory->show_name ?? $course->category);
    $locationName = $isTh ? $location->show_name ?? '' : $location->show_name_en ?? ($location->show_name ?? '');
@endphp

<div class="card bg-light border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">{{ $isTh ? 'รายละเอียด' : 'Details' }}</h5>
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <th scope="row" style="width: 140px;">{{ $isTh ? 'ชื่อผู้สมัคร :' : 'Applicant Name :' }}</th>
                    <td>{{ $namePrefix ? $namePrefix . ' ' : '' }}{{ $member->name }} {{ $member->surname }}</td>
                </tr>
                <tr>
                    <th scope="row">{{ $isTh ? 'คอร์สที่สมัคร :' : 'Applied Course :' }}</th>
                    <td>
                        {{ $courseTitle }} <br>
                        {{ $locationName }} <br>
                        {{ $isTh ? 'วันที่' : 'Date' }} {{ $dateStr }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
