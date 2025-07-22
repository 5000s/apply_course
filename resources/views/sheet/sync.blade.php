@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">รายการผู้สมัคร {{ $location->show_name }} </h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $countUnsynced = $applications->where('is_synced', false)->count();
            $countNoApply = $applications->where('is_synced', true)->where('has_apply', false)->count();
        @endphp


        <div class="d-flex justify-content-start mb-3">
            <form method="GET" id="dateFilterForm" class="form-inline">
                <label for="date" class="me-2">เลือกวันที่:</label>
                <select name="date" id="date" class="form-select me-2">
                    <option value="0" {{ request('date') == "0" || request('date') == "" ? 'selected' : '' }}>
                         ทั้งหมด
                    </option>
                    @foreach($courseListDates as $date)
                        <option value="{{ $date }}" {{ request('date') == $date ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($date)->addYear(543)->format('d-m-Y') }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success">เลือก</button>
            </form>
        </div>

        <hr>

        <form id="syncForm" method="POST" action="{{ route('admin.applications.sync.store', ['locationId' =>  $location->id]) }}">
            @csrf


            <h6> เลือกข้อมูลที่จะสร้างจาก Google Sheet</h6>

            <select name="import" >
                <option value="all">ทั้งหมด (ผู้สมัคร, คอร์ส, และ การสมัคร)</option>
                <option value="course">เฉพาะ คอร์ส</option>
            </select>

            <input type="hidden" name="location_id" value="{{ $location->id }}">
            <input type="hidden" name="date" id="hiddenDateInput" value="{{ request('date') }}">


        @if($countUnsynced > 0 || $countNoApply > 0)
                <button type="button" id="confirmSync" class="btn btn-primary mb-3">
                    สร้างข้อมูลจาก Google Sheet
                </button>
            @endif
            <h5>❎ ยังไม่มี Member ในระบบ </h5>
            <table class="table table-bordered table-sm" id="unsyncedTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>เพศ</th>
                    <th>อายุ</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>วันที่สมัคร</th>
                </tr>
                </thead>
                <tbody>
                @foreach($applications->where('is_synced', false) as $i => $app)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $app->first_name }} {{ $app->last_name }}</td>
                        <td>{{ str_contains($app->gender, 'ชาย') ? 'ชาย' : 'หญิง' }}</td>
                        <td>{{ $app->age }}</td>
                        <td>{{ $app->phone }}</td>
                        <td>{{ $app->email }}</td>
                        <td>{{ implode(', ', $app->courseDates) }}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <hr>

            <h5 class="mt-5">✅ มีในระบบสมาชิกแล้ว ❎ แต่ยังไม่มีข้อมูล สมัคร</h5>

            <table class="table table-bordered table-sm" id="syncedTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Member ID</th>
                    <th>ประวัติ</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>เพศ</th>
                    <th>อายุ</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>วันที่สมัคร</th>
                </tr>
                </thead>
                <tbody>
                @foreach($applications->where('is_synced', true)->where('has_apply', false) as $i => $app)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $app->member_id ?? '-' }}</td>
                        <td style="text-align: center">
                            <a target="_blank" href="{{ route('courses.history', $app->member_id) }}" class="btn btn-secondary">{{ __('messages.history') }}</a>
                        </td>
                        <td>{{ $app->first_name }} {{ $app->last_name }}</td>
                        <td>{{ str_contains($app->gender, 'ชาย') ? 'ชาย' : 'หญิง' }}</td>
                        <td>{{ $app->age }}</td>
                        <td>{{ $app->phone }}</td>
                        <td>{{ $app->email }}</td>
                        <td>{{ implode(', ', $app->courseDates) }}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <hr>

            <h5 class="mt-5">✅ มีในระบบสมาชิกแล้ว และ ✅ ลงข้อมูลสมัคร แล้ว</h5>
            <table class="table table-bordered table-sm" id="syncedTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Member ID</th>
                    <th>ประวัติ</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>เพศ</th>
                    <th>อายุ</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>วันที่สมัคร</th>
                </tr>
                </thead>
                <tbody>
                @foreach($applications->where('is_synced', true)->where('has_apply', true) as $i => $app)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $app->member_id ?? '-' }}</td>
                        <td style="text-align: center">
                            <a target="_blank" href="{{ route('courses.history', $app->member_id) }}" class="btn btn-secondary">{{ __('messages.history') }}</a>
                        </td>
                        <td>{{ $app->first_name }} {{ $app->last_name }}</td>
                        <td>{{ str_contains($app->gender, 'ชาย') ? 'ชาย' : 'หญิง' }}</td>
                        <td>{{ $app->age }}</td>
                        <td>{{ $app->phone }}</td>
                        <td>{{ $app->email }}</td>
                        <td>{{ implode(', ', $app->courseDates) }}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>

    <script>
        try{
            document.getElementById('confirmSync').addEventListener('click', function() {
                Swal.fire({
                    title: 'ยืนยันการดำเนินการ',
                    text: 'คุณต้องการสร้าง Member, คอร์ส และ Apply ที่ยังไม่มีในฐานข้อมูลใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ดำเนินการเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'กำลังดำเนินการ...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        document.getElementById('syncForm').submit();
                    }
                });
            });
        }catch (e){

        }


        // Initialize DataTables
        $(document).ready(function () {
            $('#unsyncedTable').DataTable({ paging: false, info: false });
            $('#noApplyTable').DataTable({ paging: false, info: false });
            $('#syncedTable').DataTable({ paging: false, info: false });
        });
    </script>
@endsection
