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
                <th>Name</th>
                <th>Surname</th>
                <th>Phone</th>
                <th>Open</th>
            </tr>
            </thead>
            <tbody>
            @foreach($applies as $apply)
            <tr>
                <td>{{$apply->member->name}}</td>
                <td>{{$apply->member->surname}}</td>
                <td>{{$apply->member->phone}}</td>
                <td></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize the data table
            $("#myTable").DataTable({});

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
