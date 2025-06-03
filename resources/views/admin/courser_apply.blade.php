@extends('layouts.master')

@section('content')

    {{-- 3rd‑party assets --}}
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    {{-- DataTables Buttons + JSZip for Excel export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet"/>
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
    <h1 class="text-center my-4">{{ $course->category }}, {{ $course->location }}</h1>
    <h3 class="text-center my-4">{{ $course->date_start->format('d/m/Y') . "-". $course->date_end->format('d/m/Y') }}</h3>

    {{-- Row wrapper for tab menu (bigger & prettier) --}}
    <div class="row mb-8">
        <div class="col-12 flex justify-center">
            @php
                $active  = request('group', 'all');
                $base    = 'px-6 md:px-8 py-3 md:py-3.5 text-lg md:text-2xl font-semibold tracking-wide transition rounded-full shadow-sm';
                $makeTab = function($v,$label,$count) use($active,$base){
                    $activeCls = 'bg-primary text-white hover:bg-primary/90';
                    $normalCls = 'bg-base-200 text-gray-700 hover:bg-base-300';
                    // badge แสดงตัวเลข
                    $badge     = "<span class=\"badge badge-sm ml-2 align-top\">{$count}</span>";
                    return "<a href=?group={$v} class=\"{$base} ".($active==$v?$activeCls:$normalCls)."\">{$label}{$badge}</a>";
                };
            @endphp
            <div class="flex flex-wrap gap-3">
                {!! $makeTab('all',   'ทั้งหมด',   $stats['all']   ?? 0) !!}
                {!! $makeTab('monk',   'ภิกษุ', $stats['monk']   ?? 0) !!}
                {!! $makeTab('nun',    'แม่ชี', $stats['nun']    ?? 0) !!}
                {!! $makeTab('male',   'ชาย',   $stats['male']   ?? 0) !!}
                {!! $makeTab('female', 'หญิง',  $stats['female'] ?? 0) !!}
                {!! $makeTab('malespecial', 'ชาย กุฏิพิเศษ',  $stats['malespecial'] ?? 0) !!}
                {!! $makeTab('femalespecial', 'หญิง กุฏิพิเศษ',  $stats['femalespecial'] ?? 0) !!}
            </div>
        </div>
    </div>

    <div class="container-fluid" id='hidden'>

        <div class="tableContainer">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <td class="text-center ">#</td>
                    <td class="text-center ">uid</td>
                    <td class="text-center ">สมัครเมื่อ</td>
                    <td class="text-center " style="width: 100px;">ชื่อ</td>
                    <td class="text-center ">อายุ</td>
                    <td class="text-center ">เพศ</td>
                    <td class="text-center " style="width: 120px;">การศึกษา/อาชีพ</td>
                    <td class="text-center " style="display: none;">ความเชี่ยวชาญ</td>
                    <td class="text-center " style="display: none;">อาชีพ</td>
                    <td class="text-center " style="display: none;">การศึกษา</td>
                    <td class="text-center ">buddhism</td>
                    <td class="text-center ">ศิษย์</td>
                    <td class="text-center " style="width: 145px;">ติดต่อ</td>
                    <td class="text-center " style="display: none;">โทร</td>
                    <td class="text-center " style="display: none;">อีเมล</td>
                    <td class="text-center ">role</td>
                    <td class="text-center ">ที่พัก</td>
                    <td class="text-center " style="width: 200px;">คอร์สล่าสุด</td>
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


                        <td class="text-left">{{ $member->name }}&nbsp;<br>{{ $member->surname }}</td>
                        <td class="text-center">{{ $member->age }}</td>
                        <td class="text-center" >{{ $member->gender }}</td>


                        <td class="text-center">
                            @if(strlen($member->expertise) > 1)
                                {{ $member->expertise }} <br>
                            @endif
                            @if(strlen($member->career) > 1)
                                {{ $member->career }} <br>
                            @endif
                            @if(strlen($member->degree) > 1)
                                {{ $member->degree }}
                            @endif
                        </td>

                        <td class="text-center" style="display: none;">{{ $member->expertise }}</td>
                        <td class="text-center" style="display: none;">{{ $member->career }}</td>
                        <td class="text-center" style="display: none;">{{ $member->degree }}</td>


                        <td class="text-center">{{ $member->buddhism }}</td>
                        <td class="text-center">{{ $member->status }}</td>
                        <td class="text-left" style="font-size: 12px">

                            P: {{ $member->phone }}<br>
                            @php
                                $email = str_replace('@', '<wbr>@', e($member->email));
                            @endphp
                            E: {!! $email !!}
                        </td>

                        <td class="text-left" style="display: none">
                           {{ $member->phone }}
                        </td>
                        <td class="text-left" style="display: none">
                            {!! $member->email !!}
                        </td>

                        <td class="text-center">{{ $member->role }}</td>
                        <td class="text-center">{{ $member->shelter }}</td>
                        <td class="text-left" style="font-size: 12px">
                            @php
                                $courses = $completedCourses[$member->uid] ?? [];
                                $coursesService = $completedServiceCourses[$member->uid] ?? [];
                            @endphp

                            @foreach($courses as $co)
                                {{ str_replace('คอร์ส', '', $co->category) }}
                                ({{ \Carbon\Carbon::parse($co->date_start)->format('d/m/y') }})<br>
                            @endforeach

                            @if(count($coursesService) > 0)
                                <div style="font-weight: bolder;">ธรรมบริกร</div>
                                @foreach($coursesService as $co)
                                    {{ str_replace('คอร์ส', '', $co->category) }}
                                    ({{ \Carbon\Carbon::parse($co->date_start)->format('d/m/y') }})<br>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center" style="font-size: 12px">
                            {{ $member->state }} <br>
                            แก้ไขโดย: {{ $member->updated_by === 'Anonymous' ? 'NA' : $member->updated_by }}
                        </td>

                        <td class="text-center">
                            <div class="flex flex-col items-center gap-1">
                                <a href="{{ route('admin.courseApplyForm', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id]) }}"
                                   target="_blank">
                                    <button class="btn btn-sm btn-active btn-in-table">ดูข้อมูล</button>
                                </a>

                                <div class="dropdown dropdown-hover">
                                    <div tabindex="0" role="button" class="btn btn-sm btn-active">แก้ไข</div>
                                    <ul tabindex="0"
                                        class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li>
                                            <a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยื่นใบสมัคร']) }}">ยื่นใบสมัคร</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยืนยันแล้ว']) }}">ยืนยันแล้ว</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ผ่านการอบรม']) }}">ผ่านการอบรม</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => 'ยุติกลางคัน']) }}">ยุติกลางคัน</a>
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
    <script>
        $(function () {
            $('#myTable').DataTable({
                pageLength: 100,
                order: [[1, 'asc']],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    filename: '{{ $course->category .'_'. $course->date_start }}',
                    title: '{{ $course->category }} ({{ $course->date_start }})',
                    exportOptions: {
                        // include only columns up to "คอร์สล่าสุด" (0‑based index 0‑12)
                        columns: [  0, 1, 2 , 3, 4, 5, 7,8,9,10,11 , 13,14,15,16,18]
                    }
                }]
            }).columns.adjust();
        });
    </script>

@endsection
