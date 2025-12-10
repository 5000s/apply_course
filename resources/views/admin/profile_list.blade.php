@extends('layouts.master')

@section('content')
    <div  class="container-lg px-0" >
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ $title ?? __('messages.applicant_list') }}</h1>
            <a href="{{ route('member.create') }}" class="btn btn-primary">{{ __('messages.add_applicant') }}</a>
        </div>
        <table class="table" id="member_table">
            <thead>
            <tr>
                <th> ID</th>
                <th> ชื่อจริง</th>
                <th> นามกสุล </th>
                <th> ชื่อเล่น </th>
                <th> เพศ </th>
                <th> อายุ </th>
                <th > ปีเข้า </th>
                <th> เบอร์ติดต่อ </th>
                <th> email </th>
{{--                <th> จังหวัด </th>--}}
                <th style="display: none"> phone </th>
                <th  style="text-align: center">{{ __('messages.edit_info') }}</th>
                <th  style="text-align: center">{{ __('messages.register_course') }}</th>
                <th  style="text-align: center">{{ __('messages.history') }}</th>
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
                    <td>{{ $member->birthdate?->age ?? 'ไม่ทราบ' }}</td>
                    <td>{{ $member->techo_year }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->email }}</td>
{{--                    <td>{{ $member->province }}</td>--}}
                    <td style="display: none"> {{ str_replace(' ', '', $member->phone) }} </td>

                    <td style="text-align: center">
                        <a target="_blank" href="{{ route('member.edit', $member->id) }}" class="btn btn-secondary">{{ __('messages.edit') }}</a>
                    </td>
                    <td style="text-align: center">
                        <a target="_blank" href="{{ route('courses.index', $member->id) }}" class="btn btn-secondary">{{ __('messages.register') }}</a>
                    </td>
                    <td style="text-align: center">
                        <a target="_blank" href="{{ route('courses.history', $member->id) }}" class="btn btn-secondary">{{ __('messages.history') }}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#member_table').DataTable({
                // Optional: customize or localize strings (e.g., Thai language)
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
                }
            });
        });
    </script>
@endsection
