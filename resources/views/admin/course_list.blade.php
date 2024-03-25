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

<div class="top-layer">
    <div class="headerBar py-3" >
            <div>
                <h4>สถานที่<h4/>
                <select class="select select-bordered max-w-xs" name="location" id="location">
                    <option value="1">แก่งคอย</option>
                    <option value="2">ลานหิน</option>
                    <option value="3">หาดใหญ่</option>
                    <option value="4">มูลนิธิ อ่อนนุช</option>
                </select>  
            </div>   

            <div>
                <h4>คอร์ส<h4/>
                <select class="select select-bordered max-w-xs" name="category", id="category">
                    <option value="5">คอร์สอานาปานสติ</option>
                    <option value="1">คอร์สเตโชวิปัสสนา</option>
                    <option value="3">คอร์สเตโชฯ (ศิษย์เก่า)</option>
                    <option value="6">คอร์สศิษย์เก่า (๓ วัน)</option>
                    <option value="4">คอร์สวิถีอาสวะ</option>
                    <option value="2">คอร์สฤาษี (๑๔ วัน)</option>
                    <option value="7">ธรรมะแคมป์</option>
                    <option value="8">คอร์สอานาปานสติ ๑ วัน</option>
                    <option value="9">คอร์สเตโชฯ (อาวุโส)</option>
                    
                </select> 
            </div>   

            <div>
                <h4>ปีที่ต้องการค้นหา<h4/>
                <select class="select select-bordered max-w-xs" name="year" id="year">
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                    <option value="2014">2014</option>
                    <option value="2015">2015</option>
                    <option value="2016">2016</option>
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
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

    // document.getElementById('locationSelect').addEventListener('change', function() {
    // const location = this.value;
    //     fetch('/admin/courses', { 
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ensure CSRF token is correctly retrieved
    //         },
    //         body: JSON.stringify({
    //             location: location
    //         })
    //     })
    //     .then(response => {
    //         if (!response.ok) {
    //             throw new Error('Network response was not ok');
    //         }
    //         return response.json();
    //     })
    //     .then(data => {
    //         console.log(data); 
    //     })
    //     .catch((error) => {
    //         console.error('Error:', error);
    //     });
    // });

</script>


@endsection

