@extends('layouts.master')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-lg px-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ $title ?? __('messages.applicant_list') }}</h1>
            <div>
                <button id="toggleDeletedBtn" class="btn btn-warning me-2">เปิดดูรายชื่อที่ถูกลบ</button>
                <a href="{{ route('member.create') }}" class="btn btn-primary">{{ __('messages.add_applicant') }}</a>
            </div>
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
                    <th style="text-align: center">แก้ไข</th>
                    <th style="text-align: center">สมัคร</th>
                    <th style="text-align: center">ประวัติ</th>
                    <th style="text-align: center">ลบ</th>
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
        var isViewingDeleted = false;

        function deleteMember(id, name) {
            if (confirm("คุณแน่ใจที่จะลบ " + name + " หรือไม่?")) {
                $.ajax({
                    url: '/admin/member/soft-delete/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#member_table').DataTable().ajax.reload(null, false);
                        } else {
                            alert('ไม่สามารถลบได้');
                        }
                    },
                    error: function(err) {
                        console.error('Error deleting member', err);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                    }
                });
            }
        }

        function restoreMember(id, name) {
            if (confirm("คุณแน่ใจที่จะกู้คืนสมาชิก " + name + " หรือไม่?")) {
                $.ajax({
                    url: '/admin/member/restore/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#member_table').DataTable().ajax.reload(null, false);
                        } else {
                            alert('ไม่สามารถกู้คืนได้');
                        }
                    },
                    error: function(err) {
                        console.error('Error restoring member', err);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                    }
                });
            }
        }

        $(document).ready(function() {
            var table = $('#member_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.members.ajax') }}", // Ensure this route exists
                    data: function(d) {
                        d.show_deleted = isViewingDeleted ? 1 : 0;
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
                    {
                        data: 13,
                        orderable: false,
                        searchable: false
                    }, // Delete Action
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

            $('#toggleDeletedBtn').click(function() {
                isViewingDeleted = !isViewingDeleted;
                if (isViewingDeleted) {
                    $(this).text('กลับไปดูรายชื่อปกติ');
                    $(this).removeClass('btn-warning').addClass('btn-secondary');
                    $('#member_table thead th:last-child').text('คืน');
                } else {
                    $(this).text('เปิดดูรายชื่อที่ถูกลบ');
                    $(this).removeClass('btn-secondary').addClass('btn-warning');
                    $('#member_table thead th:last-child').text('ลบ');
                }
                table.ajax.reload();
            });
        });
    </script>
@endsection
