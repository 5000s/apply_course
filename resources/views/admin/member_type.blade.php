@extends('layouts.master')

@section('content')

    {{-- DataTables + Buttons CSS (CDN) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">


    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Member Types</h4>
            {{-- ปุ่ม Export รวมทั้งหน้าปัจจุบัน (จากตารางที่กำลังแสดง) --}}
            <button id="exportActiveExcel" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel (Active Tab)
            </button>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs" id="memberTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ana-tab" data-bs-toggle="tab" data-bs-target="#ana" type="button" role="tab">
                    ศิษย์อานาปานสติ/สู่จิตใจ 1 วัน (ยังไม่วิปัสสนา)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ana3d-tab" data-bs-toggle="tab" data-bs-target="#ana3d" type="button" role="tab">
                    ศิษย์อานาปานสติ/สู่จิตใจ 3 วัน (ยังไม่วิปัสสนา)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="techo-tab" data-bs-toggle="tab" data-bs-target="#techo" type="button" role="tab">
                    ศิษย์วิปัสสนากรรมฐาน 
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white rounded-bottom shadow-sm" id="memberTabsContent">
            {{-- TAB 1: ANA --}}
            <div class="tab-pane fade show active" id="ana" role="tabpanel" aria-labelledby="ana-tab">
                <div class="table-responsive">
                    <table id="tbl_ana" class="table table-striped table-bordered align-middle w-100">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Member ID</th>
                            <th>Member Name</th>
                            <th>Email</th>
                            <th>Locations Attended</th>
                            <th>First Course Date</th>
                            <th>Last Course Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['ana'] as $i => $r)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $r->member_id }}</td>
                                <td>{{ $r->member_name }}</td>
                                <td>
                                    @if($r->email)
                                        <a href="mailto:{{ $r->email }}">{{ $r->email }}</a>
                                    @endif
                                </td>
                                <td>{{ $r->locations_attended }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->first_course_date)->format('Y-m-d') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->last_course_date)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: ANA 3D --}}
            <div class="tab-pane fade" id="ana3d" role="tabpanel" aria-labelledby="ana3d-tab">
                <div class="table-responsive">
                    <table id="tbl_ana3d" class="table table-striped table-bordered align-middle w-100">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Member ID</th>
                            <th>Member Name</th>
                            <th>Email</th>
                            <th>Locations Attended</th>
                            <th>First Course Date</th>
                            <th>Last Course Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['ana_3d'] as $i => $r)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $r->member_id }}</td>
                                <td>{{ $r->member_name }}</td>
                                <td>
                                    @if($r->email)
                                        <a href="mailto:{{ $r->email }}">{{ $r->email }}</a>
                                    @endif
                                </td>
                                <td>{{ $r->locations_attended }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->first_course_date)->format('Y-m-d') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->last_course_date)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: TECHO --}}
            <div class="tab-pane fade" id="techo" role="tabpanel" aria-labelledby="techo-tab">
                <div class="table-responsive">
                    <table id="tbl_techo" class="table table-striped table-bordered align-middle w-100">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Member ID</th>
                            <th>Member Name</th>
                            <th>Email</th>
                            <th>Locations Attended</th>
                            <th>First Course Date</th>
                            <th>Last Course Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['techo'] as $i => $r)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $r->member_id }}</td>
                                <td>{{ $r->member_name }}</td>
                                <td>
                                    @if($r->email)
                                        <a href="mailto:{{ $r->email }}">{{ $r->email }}</a>
                                    @endif
                                </td>
                                <td>{{ $r->locations_attended }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->first_course_date)->format('Y-m-d') }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($r->last_course_date)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- jQuery (ถ้า layout คุณยังไม่มี) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap JS (ถ้า layout คุณยังไม่มี) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- DataTables + Buttons + JSZip (สำหรับ Excel) --}}
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        // ตั้งค่า DataTables ให้แต่ละตาราง + ปุ่ม Excel
        const makeTable = (selector, title) => {
            return $(selector).DataTable({
                pageLength: 25,
                order: [], // ไม่ sort โดยอัตโนมัติ (คอลัมน์แรกเป็น running no.)
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        title: title,
                        exportOptions: { columns: ':visible' }
                    }
                ],
                language: {
                    search: 'ค้นหา:',
                    lengthMenu: 'แสดง _MENU_ แถว',
                    info: 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ',
                    infoEmpty: 'ไม่มีข้อมูล',
                    infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
                    zeroRecords: 'ไม่พบข้อมูลที่ค้นหา',
                    paginate: { first: 'แรก', last: 'สุดท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า' }
                }
            });
        };

        const dtAna   = makeTable('#tbl_ana',   'ANA_NotVipassana');
        const dtAna3d = makeTable('#tbl_ana3d', 'ANA3D_NotVipassana');
        const dtTecho = makeTable('#tbl_techo', 'Vipassana_Techo');

        // ปุ่ม Export ด้านบน (Export เฉพาะแท็บที่กำลังแสดง)
        const exportActive = () => {
            const activeId = document.querySelector('.tab-pane.active').getAttribute('id');
            if (activeId === 'ana')   dtAna.button('.buttons-excel').trigger();
            if (activeId === 'ana3d') dtAna3d.button('.buttons-excel').trigger();
            if (activeId === 'techo') dtTecho.button('.buttons-excel').trigger();
        };

        document.getElementById('exportActiveExcel').addEventListener('click', exportActive);

        // แก้ bug column width เมื่อสลับแท็บ
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', (e) => {
                dtAna.columns.adjust();
                dtAna3d.columns.adjust();
                dtTecho.columns.adjust();
            });
        });
    </script>
@endsection
