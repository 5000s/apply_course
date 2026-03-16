@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>{{ $title ?? 'ตาราง Shelters' }}</h1>
                    <div>
                        {{-- <a href="{{ route('shelters.autoSetMemberID') }}" class="btn btn-warning me-2"
                            onclick="return confirm('ระบบจะทำการค้นหาและผูก member_id อัตโนมัติ ยืนยันการทำงาน?');">
                            Auto-Set Member ID
                        </a> --}}
                        <a href="{{ route('shelters.create') }}" class="btn btn-primary">เพิ่ม Shelter</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table class="table table-bordered table-striped" id="shelter_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Index</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>Note</th>
                            <th>Note Master</th>
                            <th>Extra User</th>
                            <th>Member ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shelters as $shelter)
                            <tr>
                                <td>{{ $shelter->number }}</td>
                                <td>{{ $shelter->index }}</td>
                                <td>{{ $shelter->name }}</td>
                                <td>{{ $shelter->surname }}</td>
                                <td>{{ $shelter->note }}</td>
                                <td>{{ $shelter->note_master }}</td>
                                <td>{{ $shelter->extra_user }}</td>
                                <td>
                                    @if ($shelter->member)
                                        {{ $shelter->member->name }} {{ $shelter->member->surname }}
                                        ({{ $shelter->member_id }})
                                    @else
                                        {{ $shelter->member_id }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('shelters.edit', $shelter->id) }}"
                                            class="btn btn-secondary btn-sm">แก้ไข</a>
                                        <form action="{{ route('shelters.destroy', $shelter->id) }}" method="POST"
                                            onsubmit="return confirm('ยืนยันการลบ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                                        </form>
                                    </div>
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

    <script>
        $(document).ready(function() {
            $('#shelter_table').DataTable({
                language: {
                    lengthMenu: "แสดง _MENU_ รายการ",
                    search: "ค้นหา:",
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
