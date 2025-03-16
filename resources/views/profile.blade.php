@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Navbar Right Alignment --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ __('messages.applicant_list') }}</h1>
            <a href="{{ route('member.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> {{ __('messages.add_applicant') }}
            </a>
        </div>

        {{-- Responsive Table --}}
        <div class="card shadow-sm p-3 border-0">
            <div class="table-responsive">
                <table id="membersTable" class="table table-hover">
                    <thead>
                    <tr class="text-center">
                        <th style="text-align: left">{{ __('messages.first_name') }}</th>
                        <th style="text-align: left">{{ __('messages.surname') }}</th>
                        <th style="text-align: left">{{ __('messages.nickname') }}</th>
                        <th width="15%">{{ __('messages.edit_info') }}</th>
                        <th width="15%">{{ __('messages.register_course') }}</th>
                        <th width="15%">{{ __('messages.history') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($members as $member)
                        <tr class="align-middle">
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->surname }}</td>
                            <td>{{ $member->nickname }}</td>

                            <td class="text-center">
                                <a href="{{ route('member.edit', $member->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('courses.index', $member->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-clipboard-list"></i> {{ __('messages.register') }}
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('courses.history', $member->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-history"></i> {{ __('messages.history') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DataTables & Script --}}
    @push('scripts')
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#membersTable').DataTable({
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
            });
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <style>
            body {
                background-color: #f9f9f9;
            }
            .container {
                max-width: 1000px;
            }
            .card {
                background-color: #ffffff;
                border-radius: 8px;
            }
            .table-hover tbody tr:hover {
                background-color: #f5f5f5;
            }
            th, td {
                vertical-align: middle !important;
            }
        </style>
    @endpush
@endsection
