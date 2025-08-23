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
            <select name="import">
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

            {{-- ===================== ยังไม่มี Member ===================== --}}
            <h5>❎ ยังไม่มี Member ในระบบ </h5>
            <table class="table table-bordered table-sm" id="unsyncedTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ค้นหา</th>
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
                    <tr
                        data-app="{{ $app->id }}"
                        data-first="{{ $app->first_name }}"
                        data-last="{{ $app->last_name }}"
                        data-phone="{{ $app->phone }}"
                        data-email="{{ $app->email }}"
                    >
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm btn-find-member"
                                    data-bs-toggle="modal"
                                    data-bs-target="#similarMemberModal">
                                ค้นหา Member
                            </button>
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

            {{-- ===================== มี Member แล้วแต่ยังไม่มีสมัคร ===================== --}}
            <h5 class="mt-5">✅ มีในระบบสมาชิกแล้ว ❎ แต่ยังไม่มีข้อมูล สมัคร</h5>
            <table class="table table-bordered table-sm" id="syncedNoApplyTable">
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
                        <td class="text-center">
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

            {{-- ===================== มี Member และมีสมัครแล้ว ===================== --}}
            <h5 class="mt-5">✅ มีในระบบสมาชิกแล้ว และ ✅ ลงข้อมูลสมัคร แล้ว</h5>
            <table class="table table-bordered table-sm" id="syncedWithApplyTable">
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
                        <td class="text-center">
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

    {{-- ===================== Modal: Similar Member ===================== --}}

    <style>
        /* ให้โมดัลนี้กว้างสุด 100% ของหน้าจอ และยังมีขอบเว้น 0.5rem */
        #similarMemberModal .modal-dialog{
            max-width: 100% !important;
            width: 100%;
            margin: .5rem;   /* เปลี่ยนเป็น 0 ถ้าอยากชิดขอบจริง ๆ */
        }
        /* ทางเลือก: ถ้าอยากจำกัดไม่ให้กว้างเกินไปบนจอใหญ่ */
        /* #similarMemberModal .modal-dialog{ max-width: min(100%,1400px); } */
    </style>

    
    <div class="modal fade" id="similarMemberModal" tabindex="-1" aria-labelledby="similarMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="similarMemberModalLabel">สมาชิกที่ใกล้เคียง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 small text-muted">
                        เกณฑ์ค้นหาเริ่มต้นจากแถวที่เลือก: <span id="criteriaText"></span>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-12 col-md-3">
                                    <label class="form-label mb-1">ชื่อ</label>
                                    <input type="text" class="form-control form-control-sm" id="simFirst" placeholder="เช่น กานต์">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label mb-1">นามสกุล</label>
                                    <input type="text" class="form-control form-control-sm" id="simLast" placeholder="เช่น ศรีสุข">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label mb-1">เบอร์โทร</label>
                                    <input type="text" class="form-control form-control-sm" id="simPhone" placeholder="เช่น 0812345678">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label mb-1">อีเมล</label>
                                    <input type="text" class="form-control form-control-sm" id="simEmail" placeholder="เช่น a@b.com">
                                </div>
                            </div>
                            <div class="form-text mt-1">
                                ระบบจะค้นหาอัตโนมัติเมื่อพิมพ์อย่างน้อย 3 ตัวอักษรในช่องใดช่องหนึ่ง (ครั้งแรกจะค้นหาให้อัตโนมัติ)
                            </div>
                        </div>
                    </div>

                    <div id="similarHint" class="alert alert-info d-none">
                        โปรดพิมพ์อย่างน้อย 3 ตัวอักษรในช่องค้นหา…
                    </div>

                    <div id="similarLoading" class="d-none">
                        <div class="text-center py-3">กำลังค้นหา...</div>
                    </div>

                    <div id="similarEmpty" class="alert alert-warning d-none">
                        ไม่พบสมาชิกที่ใกล้เคียง
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle d-none" id="similarTable">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Member ID</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>เพศ</th>
                                <th>อายุ</th>
                                <th>เบอร์โทร</th>
                                <th>อีเมล</th>
                                <th>จัดการ</th>
                            </tr>
                            </thead>
                            <tbody id="similarTbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <small class="text-muted me-auto">แสดงสูงสุด 30 รายการล่าสุด</small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== Scripts ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>

    <script>
        (function(){
            // ---------- Confirm Sync ----------
            (function(){
                const btn = document.getElementById('confirmSync');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'ยืนยันการดำเนินการ',
                        text: 'คุณต้องการสร้าง Member, คอร์ส และ Apply ที่ยังไม่มีในฐานข้อมูลใช่หรือไม่?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'ใช่, ดำเนินการเลย!',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({ title: 'กำลังดำเนินการ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                            document.getElementById('syncForm').submit();
                        }
                    });
                });
            })();

            // ---------- DataTables ----------
            $(function () {
                if ($('#unsyncedTable').length) $('#unsyncedTable').DataTable({ paging: false, info: false });
                if ($('#syncedNoApplyTable').length) $('#syncedNoApplyTable').DataTable({ paging: false, info: false });
                if ($('#syncedWithApplyTable').length) $('#syncedWithApplyTable').DataTable({ paging: false, info: false });
            });

            // ---------- Similar Member Modal ----------
            let currentAppId = null;
            let debounceTimer = null;
            const DEBOUNCE_MS = 300;

            const $first = $('#simFirst');
            const $last  = $('#simLast');
            const $phone = $('#simPhone');
            const $email = $('#simEmail');

            function setState({loading=false, empty=false, showTable=false, showHint=false}){
                $('#similarLoading').toggleClass('d-none', !loading);
                $('#similarEmpty').toggleClass('d-none', !empty);
                $('#similarTable').toggleClass('d-none', !showTable);
                $('#similarHint').toggleClass('d-none', !showHint);
            }

            function escapeHtml(s){
                return (s||'').toString()
                    .replaceAll('&','&amp;').replaceAll('<','&lt;')
                    .replaceAll('>','&gt;').replaceAll('"','&quot;')
                    .replaceAll("'",'&#039;');
            }

            function buildRow(m,i){
                const gender = m.gender || '-';
                const age    = (m.age ?? '-') + '';
                const phone  = m.phone || '-';
                const email  = m.email || '-';
                const name   = escapeHtml(m.name || '-');

                return `
        <tr>
          <td>${i+1}</td>
          <td>${m.id}</td>
          <td>${name}</td>
          <td>${escapeHtml(gender)}</td>
          <td>${escapeHtml(age)}</td>
          <td>${escapeHtml(phone)}</td>
          <td>${escapeHtml(email)}</td>
          <td class="text-nowrap">
              <a href="${m.profile||'#'}" target="_blank" class="btn btn-sm btn-outline-secondary">โปรไฟล์</a>
              <a href="${m.history||'#'}" target="_blank" class="btn btn-sm btn-outline-info">ประวัติ</a>
              <button type="button" class="btn btn-sm btn-primary btn-link-member ms-1" data-member="${m.id}">
                  เลือกคนนี้
              </button>
          </td>
        </tr>`;
            }

            function anyFieldLongEnough(){
                const a = ($first.val()||'').trim();
                const b = ($last.val()||'').trim();
                const c = ($phone.val()||'').trim();
                const d = ($email.val()||'').trim();
                return (a.length>=3) || (b.length>=3) || (c.length>=3) || (d.length>=3);
            }

            function performSearch(force = false){
                if (!force && !anyFieldLongEnough()){
                    setState({loading:false, empty:false, showTable:false, showHint:true});
                    $('#similarTbody').empty();
                    return;
                }
                setState({loading:true, empty:false, showTable:false, showHint:false});

                $.getJSON(`{{ route('admin.members.similar') }}`, {
                    first: $first.val(),
                    last:  $last.val(),
                    phone: $phone.val(),
                    email: $email.val()
                }).done(function(resp){
                    if (resp && resp.ok && resp.data && resp.data.length){
                        const rows = resp.data.map(buildRow).join('');
                        $('#similarTbody').html(rows);
                        setState({loading:false, empty:false, showTable:true, showHint:false});
                    } else {
                        $('#similarTbody').empty();
                        setState({loading:false, empty:true, showTable:false, showHint:false});
                    }
                }).fail(function(){
                    $('#similarTbody').empty();
                    setState({loading:false, empty:true, showTable:false, showHint:false});
                });
            }

            function triggerSearchDebounced(){
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSearch, DEBOUNCE_MS);
            }

            // เปิดโมดัลจากแถว → เติมค่าค้นหา + ค้นหาทันที
            $(document).on('click', '.btn-find-member', function(){
                const $tr   = $(this).closest('tr');
                currentAppId = $tr.data('app') || null;

                const first = $tr.data('first') || '';
                const last  = $tr.data('last') || '';
                const phone = $tr.data('phone') || '';
                const email = $tr.data('email') || '';

                $('#criteriaText').text(`ชื่อ: ${first || '-'} / นามสกุล: ${last || '-'} / โทร: ${phone || '-'} / อีเมล: ${email || '-'}`);

                $first.val(first);
                $last.val(last);
                $phone.val(phone);
                $email.val(email);

                $('#similarTbody').empty();
                setState({loading:false, empty:false, showTable:false, showHint:true});

                performSearch(true); // ค้นหาเลยรอบแรก
            });

            // ค้นหาอัตโนมัติเมื่อพิมพ์ (debounce + เงื่อนไข >=3)
            $first.on('input', triggerSearchDebounced);
            $last.on('input', triggerSearchDebounced);
            $phone.on('input', triggerSearchDebounced);
            $email.on('input', triggerSearchDebounced);

            // ป้องกัน Enter ส่งฟอร์ม
            $('#simFirst,#simLast,#simPhone,#simEmail').on('keydown', function(e){
                if (e.key === 'Enter') e.preventDefault();
            });

            // เผื่อเปิดโมดัลทางอื่น ให้ค้นหาให้อีกครั้งถ้ายังไม่แสดงผล
            document.getElementById('similarMemberModal')
                .addEventListener('shown.bs.modal', function () {
                    if (!$('#similarTable').is(':visible')) performSearch(true);
                });

            // เลือก Member → POST ผูก Application และอัปเดตปุ่มบนแถว
            $(document).on('click', '.btn-link-member', function(){
                const $btn = $(this);
                const memberId = $btn.data('member');
                if (!currentAppId || !memberId) return;

                const token = $('meta[name="csrf-token"]').attr('content');
                $btn.prop('disabled', true).text('กำลังบันทึก...');

                $.ajax({
                    url: `{{ url('/admin/applications') }}/${currentAppId}/link-member`,
                    method: 'POST',
                    data: { member_id: memberId, _token: token },
                })
                    .done(function(resp){
                        if (resp && resp.ok){
                            $btn.removeClass('btn-primary').addClass('btn-success').text('เชื่อมแล้ว');

                            // ปิดโมดัลอย่างปลอดภัย (Bootstrap 5.1)
                            const modalEl = document.getElementById('similarMemberModal');
                            if (modalEl) {
                                let instance = bootstrap.Modal.getInstance(modalEl);
                                if (!instance) instance = new bootstrap.Modal(modalEl);
                                instance.hide();
                            }

                            // ไฮไลต์แถว + เปลี่ยนปุ่มค้นหาเป็น "[ เปลี่ยน ID (xxxxx) ]"
                            const $row = $(`tr[data-app="${currentAppId}"]`).addClass('table-success');
                            const picked = resp.member_id ?? memberId;
                            const $findBtn = $row.find('.btn-find-member');
                            $findBtn
                                .removeClass('btn-outline-primary')
                                .addClass('btn-outline-warning')
                                .attr('data-selected-member', picked)
                                .attr('title', 'เปลี่ยน Member')
                                .text(`[ เปลี่ยน ID (${picked}) ]`);

                            // เผื่อใช้ต่อ
                            $row.attr('data-selected-member', picked);

                        } else {
                            $btn.prop('disabled', false).text('เลือกคนนี้');
                            alert('บันทึกไม่สำเร็จ');
                        }
                    })
                    .fail(function(){
                        $btn.prop('disabled', false).text('เลือกคนนี้');
                        alert('เกิดข้อผิดพลาดในการเชื่อมโยง');
                    });
            });
        })();
    </script>
@endsection
