@extends('layouts.app')

@section('content')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="text-left my-4">{{ __('messages.course_history') }}: {{$user->name}} {{$user->surname}}</h4>

            @if($user->admin == 1)
                <a href="javascript:history.back()" id="back-button" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @else
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @endif
        </div>
        

        <div class="card shadow-sm p-3">
            <div class="table-responsive">
                <table id="coursesTable" class="table table-striped table-hover">
                    <thead class="table-light border-bottom">
                    <tr class="text-secondary">
                        <th class="text-center">{{ __('messages.location') }}</th>
                        <th class="text-center">{{ __('messages.course_name') }}</th>
                        <th class="text-center">{{ __('messages.course_date') }}</th>
{{--                        <th class="text-center">{{ __('messages.apply_date') }}</th>--}}
                        <th class="text-center">{{ __('messages.application') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($applies as $apply)
                        <tr>
                            <td class="text-center">{{ $apply->location }}</td>
                            <td class="text-center">{{ $apply->category }}</td>
                            <td class="text-center">{{ $apply->date_range }}</td>
{{--                            <td class="text-center">{{ \Carbon\Carbon::parse($apply->apply_date)->format('d/m/Y') }}</td>--}}
                            <td class="text-center">
                                    <span class="badge
                                        @if($apply->state === 'ผ่านการอบรม') bg-success
                                        @elseif($apply->state === 'ยื่นใบสมัคร') bg-info
                                        @elseif($apply->state === 'ยุติกลางคัน') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $apply->state }}
                                    </span>
                            </td>
                            <td class="text-center">
                                @if($apply->state === 'เปิดรับสมัคร' && $apply->days_until_start > 0)
                                    <a href="{{ route('courses.show', [$member_id, $apply->id]) }}" class="btn btn-primary btn-sm">
                                        {{ __('messages.edit') }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('messages.closed') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            var table = $('#coursesTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ รายการ",
                    "zeroRecords": "ไม่พบข้อมูลที่ค้นหา",
                    "info": "แสดง _START_ - _END_ จาก _TOTAL_ รายการ",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองจากทั้งหมด _MAX_ รายการ)",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

            // 📌 กรองข้อมูลตามสถานะที่เลือก
            $('#statusFilter').on('change', function () {
                var selectedStatus = $(this).val();
                table.column(4).search(selectedStatus).draw();
            });
        });
    </script>


{{--    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">--}}


@endsection
