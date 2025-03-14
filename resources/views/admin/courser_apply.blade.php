@extends('layouts.master')

@section('content')

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
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
    </style>
    <h1 class="text-center my-4">{{ $course->category }}, {{ $course->location }}</h1>
    <h3 class="text-center my-4">{{ $course->date_start->format('d/m/Y') . "-". $course->date_end->format('d/m/Y') }}</h3>

<div class="container" id='hidden'>
    <div class="tableContainer">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <td class="text-center ">ลำดับ</td>
                    <td class="text-center ">สมัครเมื่อ</td>
                    <td class="text-center ">ชื่อ</td>
                    <td class="text-center ">นามสกุล</td>
                    <td class="text-center ">อายุ</td>
                    <td class="text-center ">เพศ</td>
                    <td class="text-center ">buddhism</td>
                    <td class="text-center ">ศิษย์</td>
                    <td class="text-center ">ติดต่อ</td>
                    <td class="text-center ">สถานะ</td>
                    <td class="text-center ">ข้อมูล</td>
                    <td class="text-center ">status</td>
                    <td class="text-center ">แก้ไขโดย</td>
                </tr>
                </thead>

                <tbody>
                    @foreach ($members as $index => $member)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $member->apply_date }}</td>
                            <td class="text-center">{{ $member->name }}</td>
                            <td class="text-center">{{ $member->surname }}</td>
                            <td class="text-center">{{ $member->age }}</td>
                            <td class="text-center">{{ $member->gender }}</td>
                            <td class="text-center">{{ $member->buddhism }}</td>
                            <td class="text-center">{{ $member->status }}</td>
                            <td class="text-center">
                                <div style="text-align: left">
                                    P: {{ $member->phone }} <br>
                                    E: {{ $member->email }}
                                </div>
                            </td>
                            <td class="text-center">{{ $member->state }}</td>

                            <td>
                                <a href="{{ route('admin.courseApplyForm', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id]) }}" target="_blank"> <button class="btn btn-sm btn-active btn-in-table">ดูข้อมูล</button></a>
                            </td>

                            <td>
                                <div class="dropdown dropdown-hover">
                                    <div tabindex="0" role="button" class="btn btn-sm btn-active">แก้ไข</div>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => "ยื่นใบสมัคร"]) }}" target="self">ยื่นใบสมัคร</a></li>
                                        <li><a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => "ยืนยันแล้ว"]) }}" target="self">ยืนยันแล้ว</a></li>
                                        <li><a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => "ผ่านการอบรม"]) }}" target="self">ผ่านการอบรม</a></li>
                                        <li><a href="{{ route('admin.courseApplyStatus', ['course_id' => $member->course_id, 'apply_id' => $member->apply_id, 'status' => "ยุติกลางคัน"]) }}" target="self">ยุติกลางคัน</a></li>
                                    </ul>
                                </div>

                            </td>

                            <td class="text-center">{{ $member->updated_by }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {

                // Initialize the data table
                $("#myTable").DataTable({
                    // "searching": false,
                    "pageLength": 100,
                    "order": [[ 1, "asc" ]], //or asc
                    // "columnDefs" : [{"targets":0, "type":"date-en"}],
                });

                $('#myTable thead').css({
                'background-color': '#414C50',
                'color': 'white',
                'text-align': 'center'
                });


                $('#hidden').fadeTo(300,1);
                $('#myTable').DataTable().columns.adjust().draw();
            });

    </script>

@endsection
