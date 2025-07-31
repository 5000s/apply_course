@extends('layouts.master')

@section('content')
    <div class="px-3 py-4">
        <h2 class="mb-4">Statistics Dashboard</h2>

        {{-- Choices.js CSS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

        <form id="filterForm" class="row g-3 align-items-end mb-5">
            <div class="col-md-2">
                <label for="date_start" class="form-label">Date Start</label>
                <input type="date" class="form-control" id="date_start" name="date_start"
                       min="2010-01-01"
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
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}"
                            {{ in_array($loc->id, (array)request('course_location', [])) ? 'selected' : '' }}>
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
                fillCourseTypes(@json((array)request('course_type', [])));
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
                        .sort((a,b) => b[1] - a[1]);
                    const totalNat = nat.reduce((sum,e) => sum + e[1], 0);
                    Highcharts.chart('nationalityChart', {
                        chart: { type: 'column', marginBottom: 100 }, title: { text: null },
                        xAxis: { categories: nat.map(e => e[0]), labels: { step: 1, rotation: -45, style: { fontSize: '10px' } } },
                        yAxis: { title: { text: 'Count (%)' } },
                        series: [{ name: 'Count', data: nat.map(e => e[1]), dataLabels: { enabled: true,
                                formatter: function(){ return this.y + ' (' + (this.y/totalNat*100).toFixed(1) + '%)'; }
                            } }],
                        tooltip: { enabled: true }
                    });

                    // Gender
                    const genderArr = Object.entries(data.gender).sort((a,b) => b[1]-a[1]);
                    const totalGen = genderArr.reduce((sum,e) => sum + e[1], 0);
                    Highcharts.chart('genderChart',{chart:{type:'column'},title:{text:null},
                        xAxis:{categories:genderArr.map(e=>e[0]),labels:{step:1}},
                        yAxis:{title:{text:'Count (%)'}}, series:[{name:'Count',data:genderArr.map(e=>e[1]), dataLabels:{enabled:true,
                                formatter:function(){return this.y+' ('+(this.y/totalGen*100).toFixed(1)+'%)';}
                            }}],
                        tooltip: { enabled: true },
                        plotOptions:{column:{dataLabels:{enabled:true}}}
                    });

                    // Age
                    const ageArr = Object.entries(data.ageRanges)
                        .map(([label,count])=>({start:parseInt(label),label,count}))
                        .filter(i=>i.start>=15&&i.start<=90)
                        .sort((a,b)=>a.start-b.start);
                    const totalAge = ageArr.reduce((sum,i)=>sum+i.count,0);
                    Highcharts.chart('ageChart',{chart:{type:'column'},title:{text:null},
                        xAxis:{categories:ageArr.map(i=>i.label),labels:{step:1}},yAxis:{title:{text:'Count (%)'}},
                        series:[{name:'Count',data:ageArr.map(i=>i.count), dataLabels:{enabled:true,
                                formatter:function(){return this.y+' ('+(this.y/totalAge*100).toFixed(1)+'%)';}
                            }}],
                        tooltip: { enabled: true },
                        plotOptions:{column:{dataLabels:{enabled:true}}}
                    });

                    // Monthly Cumulative (Numbers Only)
                    const monthArr = Object.entries(data.monthly)
                        // sort the keys chronologically
                        .sort(([a], [b]) => new Date(a + '-01') - new Date(b + '-01'));

// extract the cumulative values (they’re already cumulative!)
                    const cumData = monthArr.map(([m, c]) => c);

                    Highcharts.chart('monthlyChart', {
                        chart: { type: 'line' },
                        title: { text: null },
                        xAxis: {
                            categories: monthArr.map(e => e[0]),
                            labels: { step: 1 }
                        },
                        yAxis: {
                            title: { text: 'Cumulative' }
                        },
                        series: [{
                            name: 'Cumulative',
                            data: cumData,
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}'
                            }
                        }],
                        tooltip: { enabled: true },
                        plotOptions: {
                            line: { dataLabels: { enabled: true } }
                        }
                    });
                }

                // Load data
                document.getElementById('filterForm').addEventListener('submit',function(e){
                    e.preventDefault(); const params=new URLSearchParams(new FormData(this));
                    fetch(`{{ route('dashboard.data') }}?${params}`)
                        .then(res=>res.json()).then(renderCharts);
                });
                document.getElementById('filterForm').dispatchEvent(new Event('submit'));
            });
        </script>
    </div>
@endsection
