@extends('layouts.master')

@section('content')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<!-- <script src="https://cdn.tailwindcss.com"></script> -->

<link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.3/dist/full.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.tailwindcss.com"></script>

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
        justify-content: space-between;
    
        /* space-betwe */
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

<div class="top-layer">
    <div class="headerBar py-3" >
            
            <div>
                <h4>สถานที่<h4/>
                <select class="select select-bordered max-w-xs">
                    <option value="location1">แก่งคอย</option>
                    <option value="location2">ลานหิน</option>
                    <option value="location3">หาดใหญ่</option>
                    <option value="location4">มูลนิธิ อ่อนนุช</option>
                </select>  
            </div>   

            <div>
                <h4>คอร์ส<h4/>
                <select class="select select-bordered max-w-xs">
                    <option value="courseType1">คอร์สอานาปานสติ</option>
                    <option value="courseType2">คอร์สเตโชวิปัสสนา</option>
                    <option value="courseType3">คอร์สเตโชฯ (ศิษย์เก่า)</option>
                    <option value="courseType4">คอร์สศิษย์เก่า (๓ วัน)</option>

                    <option value="courseType5">คอร์สวิถีอาสวะ</option>
                    <option value="courseType6">คอร์สฤาษี (๑๔ วัน)</option>
                    <option value="courseType7">ธรรมะแคมป์</option>
                    <option value="courseType8">คอร์สอานาปานสติ ๑ วัน</option>
                    <option value="courseType9">คอร์สเตโชฯ (อาวุโส)</option>
                    
                </select> 
            </div>   

            <div>
                <h4>วันที่ค้นหา<h4/>
                <select class="select select-bordered max-w-xs">
                    <option value="1">2018-11-16</option>
                    <!-- <option value="2">?</option> -->
                    <!-- <option value="3">?</option> -->
                    <!-- <option value="4">?</option> -->
                </select>  
            </div>   

            <div>
                <h4>ถึงวันที่<h4/>
                <select class="select select-bordered max-w-xs">
                    <option value="1">2018-11-16</option>
                    <!-- <option value="2">?</option>
                    <option value="3">?</option>
                    <option value="4">?</option> -->
                </select>  
            </div>   
            
        </div>
    </div>
    <div class="tableContainer">
    <table id="myTable" class="table table-striped">
        <thead>
        <tr>
            <td class="text-center w-[20%]">ชื่อคอร์ส</td>
            <td class="text-center w-[20%]">วันที่เริ่ม</td>
            <td class="text-center w-[20%]">วันที่จบ</td>
            <td class="text-center w-[20%]">จำนวนผู้สมัคร</td>
            <td class="text-center w-[20%]">จำนวนผู้สมัครที่ confirm</td>
            <td class="text-center w-[20%]">จำนวนผู้สมัครที่ผ่านการอบรม</td>
            <td class="text-center w-[20%]">ดูผู้สมัคร</td>
            <td class="text-center w-[20%]">Download excel</td>
        </tr>
        </thead>
      
        <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td class="text-center">{{ $course->category }}</td>
                    <td class="text-center">{{ $course->date_start}}</td>
                    <td class="text-center">{{ $course->date_end }}</td>
                    <td class="text-center">{{ $course->apply_count }}</td>
                    <td class="text-center">{{ $course->confirm_count }}</td>
                    <td class="text-center">{{ $course->pass_count }}</td>
                    <td><button class="btn btn-sm btn-active">Choose</button></td>
                    <td><button class="btn btn-sm btn-active">Download</button></td>
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

