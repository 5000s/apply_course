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
    </style>
<div class="container" id='hidden'>
    <div class="tableContainer">
            <table id="myTable" class="table table-striped">
                <thead>
                <tr>
                    <td class="text-center w-[5%]">ลำดับ</td>
                    <td class="text-center w-[10%]">สมัครเมื่อ</td>
                    <td class="text-center w-[15%]">ชื่อ</td>
                    <td class="text-center w-[15%]">นามสกุล</td>
                    <td class="text-center w-[20%]">เบอร์</td>
                    <td class="text-center w-[20%]">email</td>
                    <td class="text-center w-[5%]">อายุ</td>
                    <td class="text-center w-[20%]">เพศ</td>
                    <td class="text-center w-[20%]">buddhism</td>
                    <td class="text-center w-[20%]">ข้อมูล</td>
                    <td class="text-center w-[20%]">status</td>
                    <td class="text-center w-[20%]">แก้ไขโดย</td>
                </tr>
                </thead>
            
                <tbody>
                    @foreach ($members as $index => $member)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $member->apply_date }}</td>
                            <td class="text-center">{{ $member->name }}</td>
                            <td class="text-center">{{ $member->surname }}</td>
                            <td class="text-center">{{ $member->phone }}</td>
                            <td class="text-center">{{ $member->email }}</td>
                            <td class="text-center">{{ $member->age }}</td>
                            <td class="text-center">{{ $member->gender }}</td>
                            <td class="text-center">{{ $member->buddhism }}</td>

                            <td><button class="btn btn-sm btn-active">ใบสมัคร</button></td>
                            <td><button class="btn btn-sm btn-active">ยื่นใบสมัคร</button></td>

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
                    "order": [[ 0, "desc" ]], //or asc
                    "columnDefs" : [{"targets":0, "type":"date-en"}],
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
