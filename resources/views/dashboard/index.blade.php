@extends('layouts.master')

@section('content')
    <!-- DataTables core -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <!-- Buttons extension -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons + deps -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>


    <style>
        /* แถวสรุปผลรวมของสถานที่ */
        #summary-table .total-row>* {
            font-weight: 700;
            background: #f8f9fa;
            /* โทนเทาอ่อน Bootstrap */
        }

        /* คอลัมน์สุดท้ายของแต่ละเดือน (เส้นกั้นหนา) */
        #summary-table th.month-end,
        #summary-table td.month-end {
            border-right: 2px solid #212529;
        }

        #summary-table th.month-group {
            border-right: 2px solid #212529;
        }
    </style>


    <div class="px-3 py-4">
        <h2 class="mb-4">Statistics Dashboard</h2>

        {{-- Choices.js CSS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

        <form id="filterForm" class="row g-3 align-items-end mb-5">
            <div class="col-md-2">
                <label for="date_start" class="form-label">Date Start</label>
                <input type="date" class="form-control" id="date_start" name="date_start" min="2010-01-01"
                    value="{{ request('date_start', '2010-01-01') }}">
            </div>
            <div class="col-md-2">
                <label for="date_end" class="form-label">Date End</label>
                <input type="date" class="form-control" id="date_end" name="date_end"
                    value="{{ request('date_end', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label for="course_location" class="form-label">Course Location</label>
                <select class="form-select" id="course_location" name="course_location[]" multiple>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}"
                            {{ in_array($loc->id, (array) request('course_location', [])) ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="course_type" class="form-label">Course Type</label>
                <select class="form-select" id="course_type" name="course_type[]" multiple disabled>
                    {{-- Populated via JS --}}
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>

        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="card p-3">
                    <h5>Nationality Distribution</h5>
                    <div class="d-flex align-items-center mb-2">
                        <strong class="me-2">Thailand:</strong>
                        <span id="thaiCount" class="badge bg-primary">0</span>
                    </div>
                    <div id="nationalityChart" style="height: 300px;"></div>

                    <div class="mt-4">
                        <table id="nationalityTable" class="table table-bordered table-sm table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nationality</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-3">
                    <h5>Gender Distribution</h5>
                    <div id="genderChart" style="height: 300px;"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-3">
                    <h5>Age Ranges (5‑year)</h5>
                    <div id="ageChart" style="height: 300px;"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card p-3">
                    <h5>Members per Month (Cumulative)</h5>
                    <div id="monthlyChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>


        <div class="d-flex justify-content-between align-items-center mb-2">
            <div id="summary-buttons"></div>
        </div>
        <div class="card mb-4">
            <div class="card-header fw-bold">สรุปจำนวนนักเรียนรายเดือน (แยกสถานที่/ประเภทคอร์ส)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="summary-table" class="table table-bordered table-sm table-striped align-middle">
                        <thead id="summary-thead"></thead>
                        <tbody id="summary-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Choices.js and Highcharts JS --}}
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices
            const locChoices = new Choices('#course_location', {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
                placeholderValue: 'Select locations'
            });
            const typeChoices = new Choices('#course_type', {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
                placeholderValue: 'Select types'
            });
            typeChoices.disable();

            // Populate Course Type based on selected locations
            function fillCourseTypes(selected = []) {
                const locs = locChoices.getValue(true);
                if (!locs.length) {
                    typeChoices.clearChoices();
                    typeChoices.disable();
                    return;
                }
                fetch(`{{ route('dashboard.course-types') }}?locations[]=${locs.join('&locations[]=')}`)
                    .then(res => res.json())
                    .then(categories => {
                        typeChoices.clearStore();
                        typeChoices.clearChoices();
                        typeChoices.removeActiveItems();
                        const choices = categories.map(cat => ({
                            value: cat.id,
                            label: cat.name,
                            selected: selected.includes(cat.id.toString())
                        }));
                        typeChoices.setChoices(choices, 'value', 'label', true);
                        typeChoices.enable();
                    });
            }

            // Initial load
            fillCourseTypes(@json((array) request('course_type', [])));
            document.querySelector('#course_location').addEventListener('change', function() {
                const prev = typeChoices.getValue(true).map(String);
                fillCourseTypes(prev);
            });

            // Chart rendering
            function renderCharts(data) {

                // Nationality
                const rawNat = data.nationality;
                const thaiKey = Object.keys(rawNat).find(k => k.toLowerCase().includes('thai'));
                document.getElementById('thaiCount').textContent = thaiKey ? rawNat[thaiKey] : 0;
                const nat = Object.entries(rawNat).filter(([k]) => !k.toLowerCase().includes('thai'))
                    .sort((a, b) => b[1] - a[1]);
                const totalNat = nat.reduce((sum, e) => sum + e[1], 0);
                Highcharts.chart('nationalityChart', {
                    chart: {
                        type: 'column',
                        marginBottom: 100
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: nat.map(e => e[0]),
                        labels: {
                            step: 1,
                            rotation: -45,
                            style: {
                                fontSize: '10px'
                            }
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Count (%)'
                        }
                    },
                    series: [{
                        name: 'Count',
                        data: nat.map(e => e[1]),
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return this.y + ' (' + (this.y / totalNat * 100).toFixed(1) +
                                    '%)';
                            }
                        }
                    }],
                    tooltip: {
                        enabled: true
                    }
                });

                // Gender
                const genderArr = Object.entries(data.gender).sort((a, b) => b[1] - a[1]);
                const totalGen = genderArr.reduce((sum, e) => sum + e[1], 0);
                Highcharts.chart('genderChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: genderArr.map(e => e[0]),
                        labels: {
                            step: 1
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Count (%)'
                        }
                    },
                    series: [{
                        name: 'Count',
                        data: genderArr.map(e => e[1]),
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return this.y + ' (' + (this.y / totalGen * 100).toFixed(1) +
                                    '%)';
                            }
                        }
                    }],
                    tooltip: {
                        enabled: true
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    }
                });

                // Age
                const ageArr = Object.entries(data.ageRanges)
                    .map(([label, count]) => ({
                        start: parseInt(label),
                        label,
                        count
                    }))
                    .filter(i => i.start >= 15 && i.start <= 90)
                    .sort((a, b) => a.start - b.start);
                const totalAge = ageArr.reduce((sum, i) => sum + i.count, 0);
                Highcharts.chart('ageChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: ageArr.map(i => i.label),
                        labels: {
                            step: 1
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Count (%)'
                        }
                    },
                    series: [{
                        name: 'Count',
                        data: ageArr.map(i => i.count),
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return this.y + ' (' + (this.y / totalAge * 100).toFixed(1) +
                                    '%)';
                            }
                        }
                    }],
                    tooltip: {
                        enabled: true
                    },
                    plotOptions: {
                        column: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    }
                });

                // Monthly Cumulative (Numbers Only)
                const monthArr = Object.entries(data.monthly)
                    // sort the keys chronologically
                    .sort(([a], [b]) => new Date(a + '-01') - new Date(b + '-01'));

                // extract the cumulative values (they’re already cumulative!)
                const cumData = monthArr.map(([m, c]) => c);

                Highcharts.chart('monthlyChart', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: monthArr.map(e => e[0]),
                        labels: {
                            step: 1
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Cumulative'
                        }
                    },
                    series: [{
                        name: 'Cumulative',
                        data: cumData,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }],
                    tooltip: {
                        enabled: true
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    }
                });
            }

            function renderSummaryTable(months, summary) {
                // 🔴 Reset table completely before building new header/body
                resetSummaryTableSkeleton();

                const thead = document.getElementById('summary-thead');
                const tbody = document.getElementById('summary-tbody');

                // ===== build THEAD (allowed to use colspan/rowspan here) =====
                const tr1 = document.createElement('tr');
                const th0 = document.createElement('th');
                th0.textContent = 'Course';
                th0.rowSpan = 2;
                tr1.appendChild(th0);

                months.forEach(m => {
                    const th = document.createElement('th');
                    th.textContent = m;
                    th.colSpan = 4;
                    th.className = 'text-center month-group';
                    tr1.appendChild(th);
                });

                const tr2 = document.createElement('tr');
                months.forEach(() => {
                    ['ชายไทย', 'หญิงไทย', 'ชายต่างชาติ', 'หญิงต่างชาติ'].forEach((lbl, idx) => {
                        const th = document.createElement('th');
                        th.textContent = lbl;
                        th.className = 'text-center';
                        if (idx === 3) th.classList.add('month-end');
                        tr2.appendChild(th);
                    });
                });

                thead.appendChild(tr1);
                thead.appendChild(tr2);

                // ===== build TBODY (NO colspan/rowspan here) =====
                const totalCols = 1 + months.length * 4;
                const padRow = (tr) => {
                    while (tr.children.length < totalCols) tr.appendChild(document.createElement('td'));
                };

                const buildLocationTotals = (locData) => {
                    const totals = {};
                    months.forEach(m => totals[m] = {
                        'ชายไทย': 0,
                        'หญิงไทย': 0,
                        'ชายต่างชาติ': 0,
                        'หญิงต่างชาติ': 0
                    });
                    Object.keys(locData).forEach(cat => {
                        months.forEach(m => {
                            const cell = locData[cat][m] || {
                                'ชายไทย': 0,
                                'หญิงไทย': 0,
                                'ชายต่างชาติ': 0,
                                'หญิงต่างชาติ': 0
                            };
                            totals[m]['ชายไทย'] += cell['ชายไทย'] || 0;
                            totals[m]['หญิงไทย'] += cell['หญิงไทย'] || 0;
                            totals[m]['ชายต่างชาติ'] += cell['ชายต่างชาติ'] || 0;
                            totals[m]['หญิงต่างชาติ'] += cell['หญิงต่างชาติ'] || 0;
                        });
                    });
                    return totals;
                };

                Object.keys(summary).forEach(loc => {
                    // location header (no colspan)
                    const trLoc = document.createElement('tr');
                    trLoc.className = 'fw-bold bg-light';
                    trLoc.appendChild(Object.assign(document.createElement('td'), {
                        textContent: loc
                    }));
                    padRow(trLoc);
                    tbody.appendChild(trLoc);

                    const cats = Object.keys(summary[loc] || {});
                    if (!cats.length) {
                        const tr = document.createElement('tr');
                        tr.appendChild(Object.assign(document.createElement('td'), {
                            textContent: '— ไม่มีข้อมูล —'
                        }));
                        padRow(tr);
                        tbody.appendChild(tr);
                        return;
                    }

                    // totals row
                    const totals = buildLocationTotals(summary[loc]);
                    const trTotal = document.createElement('tr');
                    trTotal.className = 'total-row';
                    trTotal.appendChild(Object.assign(document.createElement('td'), {
                        textContent: `รวม (${loc})`
                    }));
                    months.forEach(m => ['ชายไทย', 'หญิงไทย', 'ชายต่างชาติ', 'หญิงต่างชาติ'].forEach((k,
                        idx) => {
                        const td = document.createElement('td');
                        td.className = 'text-end';
                        td.textContent = (totals[m][k] || 0).toLocaleString();
                        if (idx === 3) td.classList.add('month-end');
                        trTotal.appendChild(td);
                    }));
                    padRow(trTotal);
                    tbody.appendChild(trTotal);

                    // category rows
                    cats.forEach(cat => {
                        const tr = document.createElement('tr');
                        tr.appendChild(Object.assign(document.createElement('td'), {
                            textContent: cat
                        }));
                        months.forEach(m => {
                            const cell = summary[loc][cat][m] || {
                                'ชายไทย': 0,
                                'หญิงไทย': 0,
                                'ชายต่างชาติ': 0,
                                'หญิงต่างชาติ': 0
                            };
                            ['ชายไทย', 'หญิงไทย', 'ชายต่างชาติ', 'หญิงต่างชาติ'].forEach((k,
                                idx) => {
                                const td = document.createElement('td');
                                td.className = 'text-end';
                                td.textContent = (cell[k] ?? 0).toLocaleString();
                                if (idx === 3) td.classList.add('month-end');
                                tr.appendChild(td);
                            });
                        });
                        padRow(tr);
                        tbody.appendChild(tr);
                    });
                });

                const ds = document.getElementById('date_start').value || '';
                const de = document.getElementById('date_end').value || '';

                // ตั้งชื่อไฟล์เป็น summary_2010-01-01_2025-01-01
                const fname = `summary_${ds}_${de}`;
                initSummaryDataTable(fname);
            }





            // Load data
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const params = new URLSearchParams(new FormData(this));
                fetch(`{{ route('dashboard.data') }}?${params}`)
                    .then(res => res.json())
                    .then(data => {
                        renderCharts(data); // ของเดิม

                        renderSummaryTable(data.months, data.summary); // ของใหม่
                        renderNationalityTable(data.nationality); // New table
                    })
                    .catch(err => {
                        console.error(err);
                    });
            });
            document.getElementById('filterForm').dispatchEvent(new Event('submit'));
        });
    </script>


    <script>
        let summaryDT = null;
        let nationalityDT = null;


        function resetSummaryTableSkeleton() {
            // 1) Destroy DT if active and remove wrapper
            if ($.fn.DataTable.isDataTable('#summary-table')) {
                $('#summary-table').DataTable().destroy();
            }
            $('#summary-buttons').empty();

            // DataTables wraps with .dataTables_wrapper – safest is to replace the table element entirely
            const wrapper = document.querySelector('#summary-table').closest('.dataTables_wrapper') ||
                document.querySelector('#summary-table').parentElement;

            // Recreate a fresh table skeleton
            wrapper.innerHTML = `
                        <table id="summary-table" class="table table-bordered table-sm table-striped align-middle" style="width:100%">
                          <thead id="summary-thead"></thead>
                          <tbody id="summary-tbody"></tbody>
                        </table>
                      `;
        }


        function initSummaryDataTable(customFilename) {
            // ดึงรายชื่อเดือนจาก thead แถวบน (มี class .month-group)
            const monthGroups = Array.from(
                document.querySelectorAll('#summary-thead tr:first-child th.month-group')
            ).map(th => th.textContent.trim());

            const subCols = ['ชายไทย', 'หญิงไทย', 'ชายต่างชาติ', 'หญิงต่างชาติ'];

            // ตั้งชื่อไฟล์: ใช้ เดือนแรก และ เดือนสุดท้าย เท่านั้น
            const firstM = monthGroups[0] || 'Start';
            const lastM = monthGroups[monthGroups.length - 1] || 'End';
            const filename = customFilename || `summary_${firstM}_${lastM}`;

            // re-init
            if (summaryDT && $.fn.DataTable.isDataTable('#summary-table')) {
                summaryDT.destroy();
                $('#summary-buttons').empty();
            }

            summaryDT = $('#summary-table').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                scrollX: true,
                deferRender: true,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: filename, // sheet title
                    filename: filename, // ชื่อไฟล์ .xlsx
                    bom: true,
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            // --> ทำ header เองให้เป็น "Mar-25 - ชายไทย" เป็นต้น
                            header: function(data, colIdx) {
                                if (colIdx === 0) return 'Course';
                                // ตัดคอลัมน์แรกออก แล้ว map ไปยังเดือนและ sub-col
                                const zeroBased = colIdx - 1;
                                const monthIdx = Math.floor(zeroBased / subCols.length);
                                const subIdx = zeroBased % subCols.length;
                                const monthLbl = monthGroups[monthIdx] || '';
                                const subLbl = subCols[subIdx] || '';
                                return `${monthLbl} - ${subLbl}`;
                            },
                            body: function(_data, _row, _col, node) {
                                return $(node).text().trim();
                            }
                        }
                    }
                }]
            });

            summaryDT.buttons().container().appendTo('#summary-buttons');
        }

        function renderNationalityTable(nationalityData) {
            if (nationalityDT && $.fn.DataTable.isDataTable('#nationalityTable')) {
                nationalityDT.destroy();
            }

            const tbody = document.querySelector('#nationalityTable tbody');
            tbody.innerHTML = '';

            // Convert object to array and sort by count descending
            const dataArr = Object.entries(nationalityData)
                .map(([nat, count]) => ({
                    nat,
                    count
                }))
                .sort((a, b) => b.count - a.count);

            const total = dataArr.reduce((sum, item) => sum + item.count, 0);

            dataArr.forEach(item => {
                const tr = document.createElement('tr');
                const pct = total > 0 ? ((item.count / total) * 100).toFixed(2) + '%' : '0%';

                tr.innerHTML = `
                    <td>${item.nat}</td>
                    <td class="text-end">${item.count}</td>
                    <td class="text-end">${pct}</td>
                `;
                tbody.appendChild(tr);
            });

            nationalityDT = $('#nationalityTable').DataTable({
                paging: true,
                searching: true,
                info: true,
                ordering: true,
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Nationality Distribution',
                    filename: 'nationality_distribution',
                    exportOptions: {
                        columns: ':visible'
                    }
                }]
            });
        }
    </script>
@endsection
