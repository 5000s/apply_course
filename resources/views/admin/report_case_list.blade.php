@extends('layouts.master')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>{{ $title ?? 'ตารางเรื่องไม่พบสมาชิก (Report Cases)' }}</h1>
                </div>
            </div>
            <div class="col-12">
                <table class="table table-bordered table-striped" id="report_case_table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>วันที่แจ้ง</th>
                            <th>ชื่อจริง</th>
                            <th>นามสกุล</th>
                            <th>เพศ</th>
                            <th>วันเกิด</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>อีเมล</th>
                            <th>ปิดสถานะ</th>
                            {{-- <th>IP Address</th>
                            <th>ที่อยู่ (City/Prov/Country)</th>
                            <th>Map (Lat, Lon)</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cases as $case)
                            <tr>
                                <td>{{ $case->id }}</td>
                                <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $case->name ?? '-' }}</td>
                                <td>{{ $case->surname ?? '-' }}</td>
                                <td>{{ $case->gender ?? '-' }}</td>
                                <td>{{ $case->birthdate ?? '-' }}</td>
                                <td>{{ $case->phone ?? '-' }}</td>
                                <td>{{ $case->email ?? '-' }}</td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input toggle-solve" type="checkbox" role="switch"
                                            data-id="{{ $case->id }}" {{ $case->is_solve ? 'checked' : '' }}>
                                    </div>
                                </td>
                                {{-- <td>{{ $case->ipv4 ?? ($case->ipv6 ?? '-') }}</td>
                                <td>
                                    {{ collect([$case->city, $case->province, $case->country])->filter()->join(', ') ?:'-' }}
                                </td>
                                <td>
                                    @if ($case->latitude && $case->longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $case->latitude }},{{ $case->longitude }}"
                                            target="_blank">
                                            {{ $case->latitude }}, {{ $case->longitude }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td> --}}
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
            var table = $('#report_case_table').DataTable({
                searching: true, // Enable search for this plain table since we aren't using an ajax backend for it yet.
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
                order: [
                    [1, 'desc']
                ], // Order by 'วันที่แจ้ง' descending by default
                scrollX: true
            });

            // Ajax logic for toggling is_solve
            $('#report_case_table').on('change', '.toggle-solve', function() {
                var isChecked = $(this).is(':checked') ? 1 : 0;
                var caseId = $(this).data('id');
                var $checkbox = $(this);

                // Disable input during request to prevent spam clicking
                $checkbox.prop('disabled', true);

                $.ajax({
                    url: '{{ route('admin.members.report_cases.toggle') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: caseId,
                        is_solve: isChecked
                    },
                    success: function(response) {
                        if (response.success) {
                            // Optionally display a toast message indicating success
                            // alert(response.message);
                        } else {
                            // If not successful for some business logic reason, revert toggle
                            alert('ไม่สามารถอัพเดทสถานะได้');
                            $checkbox.prop('checked', !isChecked);
                        }
                    },
                    error: function(err) {
                        console.error('Error toggling solve status', err);
                        alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                        // Revert the toggle on UI because request failed
                        $checkbox.prop('checked', !isChecked);
                    },
                    complete: function() {
                        // Re-enable input
                        $checkbox.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
