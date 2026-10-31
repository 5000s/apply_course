@extends('layouts.bodhi')

@section('title', ($lang ?? 'th') === 'th' ? 'ท่านเคยสมัครคอร์สนี้แล้ว' : 'You already applied')

@section('content')
    @php
        $lang = $lang ?? 'th';
        $isTh = $lang === 'th';

        $t = [
            'applicant' => $isTh ? 'ข้อมูลผู้สมัคร' : 'Applicant',
            'name' => $isTh ? 'ชื่อ' : 'First name',
            'surname' => $isTh ? 'นามสกุล' : 'Last name',
            'place' => $isTh ? 'สถานที่' : 'Location',
            'course' => $isTh ? 'คอร์ส' : 'Course',
            'date' => $isTh ? 'วันที่' : 'Date',
            'status' => $isTh ? 'สถานะ' : 'Status',
            'already' => $isTh ? 'ท่านได้เคยสมัครคอร์สนี้ไปแล้วเมื่อวันที่' : 'You already registered for this course on',
            'current_status' => $isTh ? 'สถานะปัจจุบัน' : 'Current status',
            'cancel' => $isTh ? 'ยกเลิกการสมัครคอร์ส' : 'Cancel registration',
            'reconfirm' => $isTh ? 'ยืนยันการสมัครคอร์สอีกครั้ง' : 'Confirm registration again',
            'close' => $isTh ? 'ปิดหน้าต่างนี้' : 'Close this window',
            'confirm_cancel' => $isTh
                ? 'ยืนยันการยกเลิกการสมัครคอร์สนี้?'
                : 'Confirm cancelling this registration?',
        ];

        $courseTitle = $isTh
            ? ($courseCat->show_name ?? $course->category)
            : ($courseCat->show_name_en ?? $courseCat->show_name ?? $course->category);

        $placeName = $isTh
            ? ($location->show_name ?? '-')
            : ($location->show_name_en ?? ($location->show_name ?? '-'));

        $fmt = function ($d) use ($isTh) {
            if (!$d) return '-';
            $c = \Carbon\Carbon::parse($d);
            if ($isTh) {
                $c->locale('th');
                return $c->translatedFormat('j F') . ' ' . ($c->year + 543);
            }
            return $c->locale('en')->translatedFormat('j F Y');
        };

        $appliedDate = $fmt($apply->created_at);
    @endphp

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                {{-- การ์ดคอร์ส --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <img src="{{ $imageUrl }}" alt="{{ $placeName }}"
                            style="width:100%; height:240px; object-fit:cover; border-top-left-radius:.5rem; border-top-right-radius:.5rem;">
                        <div class="p-3 p-md-4">
                            <table class="table table-sm table-borderless mb-0 align-middle">
                                <tr>
                                    <th class="text-muted" style="width:120px;">{{ $t['place'] }}</th>
                                    <td class="fs-5">{{ $placeName }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ $t['course'] }}</th>
                                    <td class="fs-5">{{ $courseTitle }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">{{ $t['date'] }}</th>
                                    <td class="fs-5">{{ $fmt($course->date_start) }} – {{ $fmt($course->date_end) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ข้อมูลผู้สมัคร --}}
                <h5 class="mb-3">{{ $t['applicant'] }}</h5>
                <div class="mb-3">
                    {{ $t['name'] }}: <strong>{{ $member->name }}</strong>
                    &nbsp;&nbsp; {{ $t['surname'] }}: <strong>{{ $member->surname }}</strong>
                </div>

                {{-- กล่องแจ้งว่าเคยสมัครแล้ว --}}
                <div class="alert alert-warning">
                    <div class="fs-5">{{ $t['already'] }} <strong>{{ $appliedDate }}</strong></div>
                    <div class="mt-1">{{ $t['current_status'] }}: <strong>{{ $apply->state }}</strong></div>
                </div>

                {{-- ปุ่ม --}}
                <div class="d-flex justify-content-end gap-2 flex-wrap">
                    @if ($isCancelled)
                        {{-- สถานะเดิมเป็นยกเลิก → ยืนยันสมัครอีกครั้ง --}}
                        <form method="POST" action="{{ route('apply.form.confirm', [$course->id, $member->id]) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <button type="submit" class="btn btn-primary btn-lg">{{ $t['reconfirm'] }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('apply.direct.cancel', [$course->id, $member->id]) }}"
                            onsubmit="return confirm('{{ $t['confirm_cancel'] }}');">
                            @csrf
                            <input type="hidden" name="lang" value="{{ $lang }}">
                            <button type="submit" class="btn btn-primary btn-lg">{{ $t['cancel'] }}</button>
                        </form>
                    @endif

                    <button type="button" class="btn btn-primary btn-lg" onclick="window.close();">
                        {{ $t['close'] }}
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
