@extends('layouts.master')

@section('content')
    {{-- 3rd‑party assets --}}
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    {{-- DataTables Buttons + JSZip for Excel export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .btn-center {
            display: block;
            text-align: center
        }

        .btn-in-table {
            margin: 3px;
            /* font-size: 90%; */
            text-align: center;
            white-space: nowrap;
        }

        #myTable {
            font-size: 14px;
        }
    </style>

    <div class="row mb-8">
        <div class="col-12">
            <div
                class="bg-base-100 rounded-3xl p-6 shadow-xl border border-base-200 flex flex-col xl:flex-row justify-between items-center gap-6 relative overflow-hidden">

                {{-- Decorative background blob --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-primary/5 blur-3xl -z-10"></div>

                <div class="text-center xl:text-left space-y-3 z-10 w-full xl:w-auto">
                    <h1
                        class="text-2xl md:text-3xl font-extrabold text-base-content flex flex-col md:flex-row items-center justify-center xl:justify-start gap-3">
                        <span class="p-3 bg-primary/10 text-primary rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        {{ $course->category }}
                    </h1>

                    <div
                        class="flex flex-col md:flex-row items-center justify-center xl:justify-start gap-4 text-base-content/70 font-medium text-sm md:text-base">
                        <div class="flex items-center gap-2 bg-base-200/50 px-3 py-1.5 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $course->location }}
                        </div>
                        <div class="flex items-center gap-2 bg-base-200/50 px-3 py-1.5 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $course->date_start->format('d/m/') . ($course->date_start->year + 543) }} -
                            {{ $course->date_end->format('d/m/') . ($course->date_end->year + 543) }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto z-10 justify-center xl:justify-end">

                    {{-- Add Existing Member --}}
                    <button
                        class="btn btn-secondary btn-outline hover:btn-active btn-md shadow-sm gap-2 font-bold px-6 btn-open-existing-member rounded-xl flex-1 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        เพิ่มผู้สมัคร (ที่มีในระบบ)
                    </button>

                    {{-- Add New Member --}}
                    <a href="{{ route('apply.direct', ['course_id' => $course->id, 'admin' => 1]) }}" target="_blank"
                        class="flex-1 sm:flex-none">
                        <button
                            class="btn btn-primary btn-md shadow-lg shadow-primary/30 gap-2 font-bold px-6 rounded-xl w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            เพิ่มผู้สมัคร (ใหม่)
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>



    {{-- Row wrapper for tab menu (bigger & prettier) --}}
    <div class="row mb-8">
        <div class="col-12 flex justify-center">
            @php
                $active = request('group', 'all');
                $base =
                    'px-6 md:px-8 py-3 md:py-3.5 text-lg md:text-2xl font-semibold tracking-wide transition rounded-full shadow-sm';
                $makeTab = function ($v, $label, $count) use ($active, $base) {
                    $activeCls = 'bg-primary text-white hover:bg-primary/90';
                    $normalCls = 'bg-base-200 text-gray-700 hover:bg-base-300';
                    // badge แสดงตัวเลข
                    $badge = "<span class=\"badge badge-sm ml-2 align-top\">{$count}</span>";
                    return "<a href=?group={$v} class=\"{$base} " .
                        ($active == $v ? $activeCls : $normalCls) .
                        "\">{$label}{$badge}</a>";
                };
            @endphp
            <div class="flex flex-wrap gap-3">
                {!! $makeTab('all', 'ทั้งหมด', $stats['all'] ?? 0) !!}
                {!! $makeTab('monk', 'ภิกษุ', $stats['monk'] ?? 0) !!}
                {!! $makeTab('nun', 'แม่ชี', $stats['nun'] ?? 0) !!}
                {!! $makeTab('male', 'ชาย', $stats['male'] ?? 0) !!}
                {!! $makeTab('female', 'หญิง', $stats['female'] ?? 0) !!}
                {!! $makeTab('malespecial', 'ชาย กุฏิพิเศษ', $stats['malespecial'] ?? 0) !!}
                {!! $makeTab('femalespecial', 'หญิง กุฏิพิเศษ', $stats['femalespecial'] ?? 0) !!}
                {!! $makeTab('walkin', 'สมัครหน้างาน', $stats['walkin'] ?? 0) !!}
                {!! $makeTab('volunteer', 'ธรรมบริกร', $stats['volunteer'] ?? 0) !!}
                {!! $makeTab('cook', 'แม่ครัว', $stats['cook'] ?? 0) !!}
                {!! $makeTab('cancel', 'ยกเลิก', $stats['cancel'] ?? 0) !!}
            </div>
        </div>
    </div>

    <div class="container-fluid" id='hidden'>

        <div class="tableContainer">
            <table id="myTable" class="table table-striped">
                <thead>
                    <tr>
                        <td class="text-center " style="width: 30px;">#</td>
                        <td class="text-center eprint" style="width: 30px;">uid</td>
                        <td class="text-center eprint" style="width: 60px;">สมัครเมื่อ</td>
                        <td class="text-center eprint">เพศ</td>
                        <td class="text-center eprint" style="width: 100px;">ชื่อ</td>
                        <td class="text-center eprint">อายุ</td>
                        <td class="text-center eprint" style="display: none;">โรคประจำตัว</td>
                        <td class="text-center " style="width: 120px;">การศึกษา/อาชีพ</td>
                        <td class="text-center " style="display: none;">ความเชี่ยวชาญ</td>
                        <td class="text-center " style="display: none;">อาชีพ</td>
                        <td class="text-center " style="display: none;">การศึกษา</td>
                        <td class="text-center eprint">สัญชาติ</td>
                        <td class="text-center eprint" style="width: 20px;">ศิษย์</td>
                        <td class="text-center eprint" style="width: 20px;">รถตู้</td>
                        <td class="text-center eprint" style="display: none;">โทร</td>
                        <td class="text-center " style="display: none;">อีเมล</td>
                        <td class="text-center eprint">role</td>
                        <td class="text-center eprint">ที่พัก</td>
                        <td class="text-center " style="width: 145px;">ติดต่อ</td>
                        <td class="text-center eprint" style="width: 20px;">ห่างคอร์ส<br>(เดือน)</td>
                        <td class="text-center " style="width: 200px;">คอร์สล่าสุด</td>
                        <td class="text-center remark-col eprint" style="max-width: 200px;">เพิ่มเติม</td>
                        <td class="text-center ">สถานะ</td>
                        <td class="text-center ">ข้อมูล/status</td>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($members as $index => $member)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $member->uid }}</td>
                            @php
                                $date = \Carbon\Carbon::parse($member->apply_date);
                            @endphp
                            <td class="text-left" style="font-size: 12px">
                                {{ $date->format('d-m-y') }}&nbsp;<br>{{ $date->format('H:i:s') }}


                                @php
                                    // compute sort-order
                                    $order =
                                        $member->buddhism !== 'ฆราวาส'
                                            ? // if not a layperson, พระ first (1), แม่ชี second (2)
                                            ($member->buddhism === 'ภิกษุ'
                                                ? 1
                                                : 2)
                                            : // otherwise ชาย third (3), หญิง fourth (4), else fifth (5)
                                            ($member->gender === 'ชาย'
                                                ? 3
                                                : ($member->gender === 'หญิง'
                                                    ? 4
                                                    : 5));

                                    // compute display text
                                    $display = $member->buddhism !== 'ฆราวาส' ? $member->buddhism : $member->gender;
                                @endphp


                            <td class="text-center" data-order="{{ $order }}">
                                {{ $display }}
                            </td>

                            <td class="text-left">{{ $member->name }}&nbsp;<br>{{ $member->surname }}</td>
                            <td class="text-center">{{ $member->age }}</td>

                            <td class="text-center" style="display: none;">{{ $member->medical_condition }}</td>

                            <td class="text-center">
                                @if (strlen($member->expertise) > 1)
                                    {{ $member->expertise }} <br>
                                @endif
                                @if (strlen($member->career) > 1)
                                    {{ $member->career }} <br>
                                @endif
                                @if (strlen($member->degree) > 1)
                                    {{ $member->degree }}
                                @endif
                            </td>

                            <td class="text-center" style="display: none;">{{ $member->expertise }}</td>
                            <td class="text-center" style="display: none;">{{ $member->career }}</td>
                            <td class="text-center" style="display: none;">{{ $member->degree }}</td>

                            <td class="text-center">{{ $member->nationality }}</td>


                            <td class="text-center">
                                {{ $member->status === 'ศิษย์เตโชวิปัสสนา' ? 'O' : 'N' }}
                            </td>
                            <td class="text-center">
                                {{ $member->van === 'yes' ? 'Y' : '' }}
                            </td>



                            <td class="text-left" style="display: none">
                                {{ $member->phone }}
                            </td>
                            <td class="text-left" style="display: none">
                                {!! $member->email !!}
                            </td>

                            <td class="text-center">{{ $member->role }}</td>

                            <td class="text-center">
                                {{ $member->shelter }} @if ($member->shelter == 'กุฏิพิเศษ')
                                    ({{ $member->shelter_number }})
                                @endif
                            </td>


                            <td class="text-left" style="font-size: 12px">

                                P: {{ $member->phone }}<br>
                                @php
                                    $email = str_replace('@', '<wbr>@', e($member->email));
                                @endphp
                                E: {!! $email !!}
                            </td>

                            <td class="text-center" style="font-size: 12px" data-order="{{ $member->gap }}">
                                {{ $member->gap ?? '—' }}
                            </td>
                            <td class="text-left" style="font-size: 12px">
                                @php
                                    $courses = $completedCourses[$member->uid] ?? [];
                                    $coursesService = $completedServiceCourses[$member->uid] ?? [];
                                @endphp

                                @foreach ($courses as $co)
                                    {{ str_replace('คอร์ส', '', $co->category) }}
                                    ({{ \Carbon\Carbon::parse($co->date_start)->format('d/m/y') }})
                                    <br>
                                @endforeach

                                @if (count($coursesService) > 0)
                                    <div style="font-weight: bolder;">ธรรมบริกร</div>
                                    @foreach ($coursesService as $co)
                                        {{ str_replace('คอร์ส', '', $co->category) }}
                                        ({{ \Carbon\Carbon::parse($co->date_start)->format('d/m/y') }})
                                        <br>
                                    @endforeach
                                @endif
                            </td>
                            <td class="text-left align-center relative px-2 remark-col"
                                data-order="{{ $member->remark ?? 0 }}">
                                <div class="remark-display truncate pr-8 remark-display">
                                    {{ $member->remark ?? '' }}
                                </div>
                                <button class="btn btn-sm btn-circle btn-outline absolute top-1 right-1 edit-remark"
                                    data-apply-id="{{ $member->apply_id }}" data-value="{{ e($member->remark) }}"
                                    title="แก้ไขข้อมูลเพิ่มเติม">✏️</button>
                            </td>

                            <td class="text-center" style="font-size: 12px">
                                {{ $member->state }} <br>
                                แก้ไขโดย: {{ $member->updated_by === 'Anonymous' ? 'NA' : $member->updated_by }}
                            </td>

                            <td class="text-center">
                                <div class="flex flex-col items-center gap-1">

                                    <a href="{{ route('courses.show', ['course_id' => $member->course_id, 'member_id' => $member->uid]) }}"
                                        target="_blank">
                                        <button class="btn btn-sm btn-active btn-in-table">ใบสมัคร</button>
                                    </a>


                                    <a href="{{ route('admin.courseApplyForm', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id]) }}"
                                        target="_blank">
                                        <button class="btn btn-sm btn-active btn-in-table">ดูข้อมูล</button>
                                    </a>

                                    <div class="dropdown dropdown-hover">
                                        <div tabindex="0" role="button" class="btn btn-sm btn-active">แก้ไข</div>
                                        <ul tabindex="0"
                                            class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยื่นใบสมัคร']) }}">ยื่นใบสมัคร</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยืนยันแล้ว']) }}">ยืนยันแล้ว</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ผ่านการอบรม']) }}">ผ่านการอบรม</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยุติกลางคัน']) }}">ยุติกลางคัน</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยกเลิกการสมัคร']) }}">ยกเลิกการสมัคร</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="dropdown dropdown-hover">
                                        <div tabindex="0" role="button" class="btn btn-sm btn-active">สมัครอื่นๆ</div>
                                        <ul tabindex="0"
                                            class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'volunteer']) }}">ธรรมบริกร</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'cook']) }}">แม่ครัว</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'walk_in']) }}">walk
                                                    in</a>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'normal']) }}">ผู้เข้าอบรม</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <input type="checkbox" id="modal-remark" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box max-w-md p-4 relative">
            <label for="modal-remark" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
            <h3 class="text-xl font-medium mb-2">แก้ไขข้อมูลเพิ่มเติม</h3>

            <form id="form-remark">
                @csrf
                <input type="hidden" name="apply_id" id="remark-apply-id" />
                <textarea id="remark-value" name="remark" class="w-full textarea textarea-bordered mb-4" rows="3"
                    placeholder="ระบุข้อมูลเพิ่มเติม…">{{ old('remark') }}</textarea>

                <div class="flex justify-end space-x-2">
                    <label for="modal-remark" class="btn btn-ghost btn-sm">ยกเลิก</label>
                    <button type="submit" class="btn btn-primary btn-sm">บันทึก</button>
                </div>
            </form>
        </div>
    </div>




    {{-- Modal: Add Existing Member --}}
    <input type="checkbox" id="modal-add-existing" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box w-11/12 max-w-5xl h-[80vh] flex flex-col p-0">
            <div class="p-4 border-b flex justify-between items-center bg-base-200">
                <h3 class="font-bold text-lg">เพิ่มผู้สมัครที่มีในระบบ</h3>
                <label for="modal-add-existing" class="btn btn-sm btn-circle btn-ghost">✕</label>
            </div>

            <div class="p-4 flex-shrink-0 bg-base-100 z-10">
                <div class="flex flex-wrap gap-2 items-end">
                    <div class="form-control w-full max-w-xs">
                        <label class="label"><span class="label-text">ชื่อ / นามสกุล</span></label>
                        <input type="text" id="search-name" class="input input-bordered w-full"
                            placeholder="พิมพ์ชื่อ หรือนามสกุล..." />
                    </div>
                    <div class="form-control w-full max-w-xs">
                        <label class="label"><span class="label-text">เบอร์โทร</span></label>
                        <input type="text" id="search-phone" class="input input-bordered w-full"
                            placeholder="พิมพ์เบอร์โทร..." />
                    </div>
                    <button class="btn btn-primary" id="btn-search-member">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        ค้นหา
                    </button>
                    <span id="search-loading" class="loading loading-spinner text-primary hidden"></span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 bg-base-100">
                <table class="table table-zebra w-full" id="table-search-results">
                    <thead class="sticky top-0 bg-base-100 shadow-sm z-0">
                        <tr>
                            <th>ชื่อ-นามสกุล</th>
                            <th>เพศ/อายุ</th>
                            <th>เบอร์โทร</th>
                            <th>Email</th>
                            <th class="text-right">เลือก</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- results here -->
                        <tr id="row-no-result" class="hidden">
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                ไม่พบข้อมูล (ลองค้นหาด้วยคำอื่น)
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t bg-base-200 flex justify-end">
                <label for="modal-add-existing" class="btn">ปิด</label>
            </div>
        </div>
    </div>


    <script>
        $(function() {

            // 1) init DataTable + Buttons
            var table = $('#myTable').DataTable({
                pageLength: 100,
                order: [
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
                columnDefs: [{
                        targets: 18,
                        type: 'num'
                    } // your gap column
                ],
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    filename: '{{ $course->category . '_' . $course->date_start }}',
                    title: '{{ $course->category }} ({{ $course->date_start }})',
                    exportOptions: {
                        columns: '.eprint',
                        format: {
                            body: function(data, row, col, node) {
                                var $cell = $(node);
                                // if this cell has a .remark-display inside, export only that
                                var $disp = $cell.find('.remark-display');
                                if ($disp.length) {
                                    return $disp.text().trim();
                                }
                                // otherwise fall back to the cell’s plain text
                                return $cell.text().trim();
                            }
                        }
                    },

                }]
            });

            // find the zero-based index of our “เพิ่มเติม” column
            var remarkColIndex = table.column('.remark-col').index();

            // 2) open modal
            $('#myTable').on('click', '.edit-remark', function() {
                var btn = $(this);
                $('#remark-apply-id').val(btn.data('apply-id'));
                $('#remark-value').val(btn.data('value') || '');
                $('#modal-remark').prop('checked', true);
            });

            // 3) AJAX submit
            $('#form-remark').on('submit', function(e) {
                e.preventDefault();

                var applyId = $('#remark-apply-id').val(),
                    newVal = $('#remark-value').val().trim() || '—';

                $.post('/admin/apply/' + applyId + '/remark', {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        remark: newVal
                    })
                    .done(function() {
                        // locate the row and its <td>
                        var $btn = $('#myTable button[data-apply-id="' + applyId + '"]'),
                            $tr = $btn.closest('tr'),
                            $td = $tr.children('td').eq(remarkColIndex);

                        // 1️⃣ update the displayed text
                        $td.find('.remark-display').text(newVal);

                        // 2️⃣ update the data-order (for sorting/export)
                        $td.attr('data-order', newVal === '—' ? 0 : newVal);

                        // 3️⃣ let DataTables refresh its view (no cell.data() needed)
                        table.row($tr).invalidate().draw(false);

                        // 4️⃣ update the button’s stored value
                        $btn.data('value', newVal);

                        // close modal
                        $('#modal-remark').prop('checked', false);
                    })
                    .fail(function(xhr) {
                        alert('บันทึกไม่สำเร็จ: ' + xhr.responseText);
                    });
            });


            // --- EXISTING MEMBER SEARCH LOGIC ---
            var $modalExisting = $('#modal-add-existing');
            var $inputName = $('#search-name');
            var $inputPhone = $('#search-phone');
            var $btnSearch = $('#btn-search-member');
            var $tableBody = $('#table-search-results tbody');
            var $rowNoResult = $('#row-no-result');
            var $loading = $('#search-loading');

            // Trigger modal open
            $('.btn-open-existing-member').on('click', function(e) {
                e.preventDefault();
                $modalExisting.prop('checked', true);
                // focus input
                setTimeout(() => $inputName.focus(), 300);
            });

            // Search Trigger
            $btnSearch.on('click', doSearch);
            $inputName.on('keyup', function(e) {
                if (e.key === 'Enter') doSearch();
            });
            $inputPhone.on('keyup', function(e) {
                if (e.key === 'Enter') doSearch();
            });

            function doSearch() {
                var nameVal = $inputName.val().trim();
                var phoneVal = $inputPhone.val().trim();

                if (!nameVal && !phoneVal) {
                    alert('กรุณากรอกชื่อ หรือเบอร์โทรอย่างน้อยหนึ่งช่อง');
                    return;
                }

                $loading.removeClass('hidden');
                $rowNoResult.addClass('hidden');
                $tableBody.find('tr:not(#row-no-result)').remove();

                $.ajax({
                    url: "{{ route('admin.members.similar') }}",
                    method: 'GET',
                    data: {
                        first: nameVal,
                        phone: phoneVal
                    },
                    success: function(res) {
                        $loading.addClass('hidden');
                        if (res.ok && res.count > 0) {
                            renderRows(res.data);
                        } else {
                            $rowNoResult.removeClass('hidden');
                        }
                    },
                    error: function(err) {
                        $loading.addClass('hidden');
                        console.error(err);
                        alert('เกิดข้อผิดพลาดขณะค้นหา');
                    }
                });
            }

            function renderRows(members) {
                members.forEach(function(m) {
                    var html = `
                        <tr class="hover">
                            <td>
                                <div class="font-bold text-primary">${m.name}</div>
                                <div class="text-xs text-gray-400">ID: ${m.id}</div>
                            </td>
                            <td>
                                <div>${m.gender || '-'}</div>
                                <div class="text-xs opacity-70">อายุ ${m.age || '-'}</div>
                            </td>
                            <td>${m.phone || '-'}</td>
                            <td>${m.email || '-'}</td>
                            <td class="text-right">
                                <button class="btn btn-sm btn-outline btn-success btn-select-member"
                                    data-id="${m.id}" data-name="${m.name}">
                                    เลือก
                                </button>
                            </td>
                        </tr>
                    `;
                    $tableBody.append(html);
                });
            }

            // Select Member Logic
            $tableBody.on('click', '.btn-select-member', function() {
                var memId = $(this).data('id');
                var memName = $(this).data('name');
                if (!confirm('ยืนยันเพิ่มคุณ "' + memName + '" เข้าสู่คอร์สนี้?')) {
                    return;
                }

                // Call add endpoint
                $.post("{{ route('admin.courses.addExistingMember') }}", {
                        _token: "{{ csrf_token() }}",
                        course_id: "{{ $course->id }}",
                        member_id: memId
                    })
                    .done(function(res) {
                        if (res.ok) {
                            // alert(res.message);
                            location.reload(); // reload to see new applicant
                        } else {
                            alert('ไม่สามารถเพิ่มได้: ' + res.message);
                        }
                    })
                    .fail(function(xhr) {
                        var msg = 'เกิดข้อผิดพลาด';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += ': ' + xhr.responseJSON.message;
                        }
                        alert(msg);
                    });
            });

        });
    </script>
@endsection
