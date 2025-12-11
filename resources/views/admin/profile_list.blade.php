@extends('layouts.master')

@section('content')
    <div class="container-lg px-0">
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
                    <th> ปีเข้า </th>
                    <th> เบอร์ติดต่อ </th>
                    <th> email </th>
                    {{--                <th> จังหวัด </th> --}}
                    {{-- Hidden column for phone slug (index 9) --}}
                    <th style="display: none"> phone </th>
                    <th style="text-align: center">{{ __('messages.edit_info') }}</th>
                    <th style="text-align: center">{{ __('messages.register_course') }}</th>
                    <th style="text-align: center">{{ __('messages.history') }}</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
                {{-- Data loaded via AJAX --}}
            </tbody>
        </table>
    </div>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#member_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.members.ajax') }}", // Ensure this route exists
                    data: function(d) {
                        // Add custom search parameter if needed
                        // DataTables sends 'search[value]' automatically
                    }
                },
                columns: [{
                        data: 0
                    }, // ID
                    {
                        data: 1
                    }, // Name
                    {
                        data: 2
                    }, // Surname
                    {
                        data: 3
                    }, // Nickname
                    {
                        data: 4
                    }, // Gender
                    {
                        data: 5,
                        name: 'birthdate'
                    }, // Age (mapped to calculation in backend, sorted by birthdate)
                    {
                        data: 6
                    }, // Techo Year
                    {
                        data: 7
                    }, // Phone
                    {
                        data: 8
                    }, // Email
                    {
                        data: 9,
                        visible: false
                    }, // Phone Slug (Hidden)
                    {
                        data: 10,
                        orderable: false,
                        searchable: false
                    }, // Edit Action
                    {
                        data: 11,
                        orderable: false,
                        searchable: false
                    }, // Register Action
                    {
                        data: 12,
                        orderable: false,
                        searchable: false
                    }, // History Action
                ],
                order: [
                    [0, 'desc']
                ], // Default sort by ID desc
                searching: true, // Enable built-in search for now, or use custom input
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
