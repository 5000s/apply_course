@extends('layouts.master')

@section('content')
    <div  class="container-lg px-0" >
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ $title ?? 'ตารางศิษย์อาวุโส (senior level)' }}</h1>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="senior_member_table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อจริง</th>
                    <th>นามสกุล</th>
                    <th>ชื่อเล่น</th>
                    <th>เพศ</th>
                    <th>อายุ</th>
                    <th style="background-color: #fcece7;">Current status</th>
                    <th style="background-color: #fcece7;">S1 date</th>
                    <th style="background-color: #fcece7;">S2 date</th>
                    <th style="background-color: #fcece7;">S3 date</th>
                    <th style="background-color: #fcece7;">S4 date</th>
                    <th style="background-color: #f9f9f9;">Death date</th>
                    <th style="background-color: #f9f9f9;">Leave date</th>
                    <th style="background-color: #f9f9f9;">Leave description</th>
                    <th style="text-align: center">แก้ไขข้อมูล</th>
                </tr>
                </thead>
                <tbody>
                @foreach($members as $member)
                    <tr>
                        <td>{{ $member->id }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->surname }}</td>
                        <td>{{ $member->nickname }}</td>
                        <td>{{ $member->gender }}</td>
                        <td>{{ $member->birthdate?->age ?? '-' }}</td>

                        <td>{{ $member->current_level }}</td>
                        <td>{{ $member->level_1_date }}</td>
                        <td>{{ $member->level_2_date }}</td>
                        <td>{{ $member->level_3_date }}</td>
                        <td>{{ $member->level_4_date }}</td>
                        <td>{{ $member->death_date }}</td>
                        <td>{{ $member->leave_date }}</td>
                        <td>{{ $member->leave_description }}</td>
                        <td style="text-align: center">
                            <a target="_blank" href="{{ route('admin.members.senior.edit', $member->id) }}" class="btn btn-secondary">แก้ไข</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#senior_member_table').DataTable({
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                    info: "แสดงหน้า _PAGE_ จาก _PAGES_",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                },
                scrollX: true 
            });
        });
    </script>
@endsection
