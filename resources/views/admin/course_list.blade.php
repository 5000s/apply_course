@extends('layouts.master')

@section('content')

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .btn-center {
            display: block;
            text-align: center;
        }

        div.dataTables_wrapper {
            margin: 0 auto;
        }

        div#hidden {
            opacity: 0;
            transition: opacity 300ms ease-in-out;
        }

        /* New styles based on your screenshot */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9; /* Lighter shade for odd rows */
        }
        .table-striped tbody tr:hover {
            background-color: #f1f1f1; /* Slight highlight on hover */
        }
        thead {
            background-color: #007bff; /* Change to match your theme */
            color: #ffffff;
        }
        .table td, .table th {
            vertical-align: middle; /* Aligns text vertically in table cells */
        }
        .table th {
            text-align: center; /* Center header content */
        }
        .table td {
            text-align: left; /* Align table data to the left */
        }
        .headerBar {
            display: flex;
            justify-content: flex-start;
            gap: 100px ;
        }
        .container {
            /* justify-content: space-between; */
        }
        .dropdown {
            width: fit-content;
        }
        .tableContainer{
            overflow: auto;
        }
        .location-top-bar {
            background-color: blue;
            color: white;
            padding: 4px;
        }
    </style>


    <div class="container" id='hidden'>


        <form action="{{ route('admin.courses') }}" method="get">
            <div class="top-layer">
                <div class="headerBar py-3 d-flex justify-content-between align-items-center">

                    <!-- ส่วนซ้าย: ฟิลด์ค้นหาต่างๆ -->
                    <div class="d-flex gap-3">
                        <!-- สถานที่ -->
                        <div>
                            <h4>สถานที่</h4>
                            <select class="form-select" name="location" id="location">
                                @php $location = request()->query('location') @endphp
                                <option @if($location==0) selected @endif value="0">ทั้งหมด</option>
                                <option @if($location==1) selected @endif value="1">แก่งคอย</option>
                                <option @if($location==2) selected @endif value="2">ลานหิน</option>
                                <option @if($location==3) selected @endif value="3">หาดใหญ่</option>
                                <option @if($location==4) selected @endif value="4">มูลนิธิ อ่อนนุช</option>
                                <option @if($location==5) selected @endif value="5">ภูเก็ต</option>
                            </select>
                        </div>

                        <!-- คอร์ส -->
                        <div>
                            <h4>คอร์ส</h4>
                            <select class="form-select" name="category" id="category">
                                @php $category = request()->query('category') @endphp
                                <option @if($category==0) selected @endif value="0">ทั้งหมด</option>
                                <option @if($category==1) selected @endif value="1">คอร์สเตโชวิปัสสนา</option>
                                <option @if($category==2) selected @endif value="2">คอร์สฤาษี (๑๔ วัน)</option>
                                <option @if($category==3) selected @endif value="3">คอร์สเตโชฯ (ศิษย์เก่า)</option>
                                <option @if($category==4) selected @endif value="4">คอร์สวิถีอาสวะ</option>
                                <option @if($category==5) selected @endif value="5">คอร์สอานาปานสติ</option>
                                <option @if($category==6) selected @endif value="6">คอร์สศิษย์เก่า (๓ วัน)</option>
                                <option @if($category==7) selected @endif value="7">ธรรมะแคมป์</option>
                                <option @if($category==8) selected @endif value="8">คอร์สอานาปานสติ ๑ วัน</option>
                                <option @if($category==9) selected @endif value="9">คอร์สเตโชฯ (อาวุโส)</option>
                                <option @if($category==10) selected @endif value="10">คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส 3 วัน 2 คืน (ศิษย์เก่า)</option>
                                <option @if($category==11) selected @endif value="11">คอร์สสมาธิอานาปานสติ 1 วัน</option>
                                <option @if($category==12) selected @endif value="12">คอร์สวิปัสสานาสติปัฏฐานสี่ เผากิเลส</option>
                                <option @if($category==13) selected @endif value="13">คอร์สสมาธิอานาปานสติ 3 วัน 2 คืน</option>
                                <option @if($category==14) selected @endif value="14">คอร์สสมาธิอานาปานสติ 4 วัน 3 คืน</option>
                            </select>
                        </div>

                        <!-- ปีที่ต้องการค้นหา -->
                        <div>
                            <h4>ปีที่ต้องการค้นหา</h4>
                            <select class="form-select" name="year" id="year">
                                @for($y = \Carbon\Carbon::now()->year; $y >= 2011 ; $y--)
                                    @php $year_s = request()->query('year') @endphp
                                    <option @if($year_s==$y) selected @endif value="{{$y}}">{{$y+543}}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- ปุ่ม ค้นหา -->
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                        </div>
                    </div>

                    <!-- ส่วนขวา: ปุ่ม สร้างคอร์ส -->
                    <div>
                        <a class="btn btn-success" href="{{ route('admin.courses.create') }}">
                            สร้างคอร์ส
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="tableContainer">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <td class="text-center w-[5%]">id</td>
                    <td class="text-center w-[10%]">สถานะ</td>
                    <td class="text-center w-[20%]">วันที่</td>
                    <td class="text-center w-[15%]">สถานที่</td>
                    <td class="text-center w-[25%]">ชื่อคอร์ส</td>
                    <td class="text-center w-[5%]">จำนวนผู้สมัคร<br/> ทั้งหมด</td>
                    <td class="text-center w-[5%]">จำนวนผู้สมัคร<br/> ที่ confirm</td>
                    <td class="text-center w-[5%]">จำนวนผู้สมัคร<br/> ที่ผ่านการอบรม</td>
                    <td class="text-center w-[5%]">ดูผู้สมัคร</td>
                    <td class="text-center w-[5%]">รายการผู้สมัคร</td>
{{--                    <td class="text-center w-[5%]">ใบสมัครทั้งหมด</td>--}}
                </tr>
                </thead>

                <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td class="text-center">

                            <a class="btn btn-info" href="{{ route('admin.courses.edit', ['course_id' => $course->id]) }}">
                                {{ $course->id }}
                            </a>
                        </td>
                        <td class="text-center">{{ $course->state }}</td>
                        <td class="text-left ">{{ $course->date_range}}</td>
                        <td class="text-left">{{ $course->location }}</td>
                        <td class="text-left">{{ $course->name }}</td>

                        <td class="text-center">{{ $course->apply_all_count }}</td>
                        <td class="text-center">{{ $course->confirm_count }}</td>
                        <td class="text-center">{{ $course->pass_count }}</td>
                        <td><a href="{{url("admin/courses/applylist") . "/$course->id"}}" class="btn btn-sm btn-active">เปิดดู</a></td>
                        <td><a href="{{route("admin.applylist.download", $course->id )}}" class="btn btn-sm btn-active">Download</a></td>
{{--                        <td><a href="{{route("admin.applylist.totalform.zip", $course->id )}}" class="btn btn-sm btn-active">Download</a></td>--}}
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>


    <!-- $(document).ready(function() {
        $('#yourTableId').DataTable({
            "searching": false // This disables the search box
        });
    }); -->

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize the data table
            $("#myTable").DataTable({
                // "searching": false,
                pageLength: 20,
                "order": [[ 0, "desc" ]], //or asc
                "columnDefs" : [{"targets":0, "type":"date-en"}],
            });

            $('#myTable thead').css({
                'background-color': '#414C50',
                'color': 'white',
                'text-align': 'center'
            });

            $('#hidden').fadeTo(300,1);

        });


    </script>


@endsection

