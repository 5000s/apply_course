@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>{{ $title ?? 'ตารางศิษย์อาวุโส (senior level)' }}</h1>
                    <input type="text" id="member_search" class="form-control w-25" placeholder="ค้นหาชื่อ/นามสกุล">
                </div>
            </div>
            <div class="col-12">
                <table class="table table-bordered table-striped" id="senior_member_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อจริง</th>
                            <th>นามสกุล</th>
                            <th>ชื่อเล่น</th>
                            <th>เพศ</th>
                            <th>อายุ</th>
                            <th>ขั้นปัจจุบัน</th>
                            <th>วันที่ได้ขั้น 1</th>
                            <th>วันที่ได้ขั้น 2</th>
                            <th>วันที่ได้ขั้น 3</th>
                            <th>วันที่ได้ขั้น 4</th>
                            <th>แก้ไขข้อมูล</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
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
                                <td>
                                    <a target="_blank" href="{{ route('admin.members.senior.edit', $member->id) }}"
                                        class="btn btn-secondary">แก้ไข</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#senior_member_table').DataTable({
                searching: false,
                language: {
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

            // API Search Logic
            $('#member_search').on('keyup', _.debounce(function() {
                var query = $(this).val();
                if (query.length < 2 && query.length > 0) return; // Min 2 chars

                // If empty, reload page to reset (or handle differently if preferred)
                if (query.length === 0) {
                    window.location.reload();
                    return;
                }

                $.ajax({
                    url: '/api/members/search',
                    type: 'GET',
                    data: {
                        q: query
                    },
                    success: function(response) {
                        // Clear DataTable
                        table.clear();

                        // Add new rows
                        response.forEach(function(member) {
                            var editUrl = `/admin/member/senior/${member.id}/edit`;

                            // Create row array matching column order
                            var rowData = [
                                member.id,
                                member.name || '-',
                                member.surname || '-',
                                member.nickname || '-',
                                member.gender || '-',
                                member.age || '-',
                                member.current_level || '-',
                                member.level_1_date || '-',
                                member.level_2_date || '-',
                                member.level_3_date || '-',
                                member.level_4_date || '-',
                                `<div style="text-align: center"><a target="_blank" href="${editUrl}" class="btn btn-secondary">แก้ไข</a></div>`
                            ];

                            table.row.add(rowData);
                        });

                        table.columns.adjust().draw();
                    },
                    error: function(err) {
                        console.error('Search error:', err);
                    }
                });
            }, 500));
        });
    </script>
@endsection
