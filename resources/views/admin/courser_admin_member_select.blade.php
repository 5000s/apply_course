@extends('layouts.master')

@section('content')

    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>


    <style>
        .btn-center {
            display: block;
            text-align: center
        }
    </style>

    <div class="container" id='hidden'>
        <table id="myTable" class="table table-striped">
            <thead>
                <tr>
                    <td style="width: 25%">วันที่</td>
                    <td style="width: 25%">ชื่อคอร์ส</td>
                    <td style="width: 25%">สถานที่</td>
                    <td >เลือก</td>
                </tr>
            </thead>
            <tbody>
            @foreach($courses as $applyCourse)
                @php $applyLocation = $applyCourse->location()->first(); @endphp
                <tr>
                    <td>{{$applyCourse->startEn}}</td>
                    <td>{{$applyCourse->category}}</td>
                    <td>{{$applyLocation->name}}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize the data table
            $("#myTable").DataTable({
                "order": [[ 0, "desc" ]], //or asc
                "columnDefs" : [{"targets":0, "type":"date-en"}],
            });

            $('#hidden').fadeTo(300,1);
            $('#myTable').DataTable().columns.adjust().draw();

        });

    </script>

    <style>
        div.dataTables_wrapper {
            margin: 0 auto;
        }

        div#hidden {
            opacity: 0;
        }
    </style>

@endsection
