@extends('layouts.master')

@section('content')
    <style>
        .course-limit-page {
            --limit-gold: #c9a750;
            --limit-gold-soft: #fdf9ee;
            --limit-changed: #fff3cd;
            max-width: 1100px;
        }

        .course-limit-page .nav-pills .nav-link {
            color: #5c4a14;
            border: 1px solid var(--limit-gold);
            margin-right: .5rem;
            margin-bottom: .5rem;
        }

        .course-limit-page .nav-pills .nav-link.active {
            background: var(--limit-gold);
            color: #fff;
            font-weight: 600;
        }

        .course-limit-page .limit-table th,
        .course-limit-page .limit-table td {
            vertical-align: middle;
        }

        .course-limit-page .location-row th {
            background: var(--limit-gold-soft);
            border-top: 3px solid var(--limit-gold);
        }

        .course-limit-page .seat-input {
            width: 5.5rem;
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .course-limit-page .is-changed {
            background: var(--limit-changed);
        }

        .course-limit-page .save-bar {
            position: sticky;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #dee2e6;
            padding: .75rem 0;
            z-index: 5;
        }
    </style>

    <div class="container course-limit-page">
        <h1 class="mb-1">{{ $title }}</h1>
        <p class="text-muted mb-3">
            เลือกกลุ่มประเภทคอร์ส แล้วกำหนดจำนวนรับ ชาย / หญิง / รวมสูงสุด ของแต่ละคอร์สในแต่ละสถานที่
        </p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- เลือก Course Group Type --}}
        <ul class="nav nav-pills mb-2">
            @foreach ($groups as $groupKey => $groupLabel)
                <li class="nav-item">
                    <a class="nav-link {{ $groupKey === $group ? 'active' : '' }}"
                        href="{{ route('admin.courseLimits', array_filter(['group' => $groupKey, 'show_all' => $showAll ? 1 : null])) }}">
                        {{ $groupLabel }}
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- เลือก Course (เฉพาะในกลุ่มที่เลือก) --}}
        <form method="GET" action="{{ route('admin.courseLimits') }}" class="row g-2 align-items-center mb-3"
            id="limitFilterForm">
            <input type="hidden" name="group" value="{{ $group }}">
            <div class="col-auto">
                <label class="col-form-label fw-semibold" for="category_id">คอร์ส</label>
            </div>
            <div class="col-md-6">
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">ทุกคอร์สในกลุ่มนี้</option>
                    @foreach ($groupCategories as $category)
                        <option value="{{ $category->id }}" {{ $categoryId === (int) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}{{ $category->active ? '' : ' (ไม่ใช้งาน)' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_all" value="1" id="show_all"
                        {{ $showAll ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_all">แสดงคอร์สที่ไม่ใช้งานแล้วด้วย</label>
                </div>
            </div>
        </form>

        <div class="alert alert-light border small">
            <strong>เว้นว่างทั้ง 3 ช่อง</strong> = ไม่ตั้งค่า (ระบบใช้ค่าเริ่มต้น 99 ที่นั่ง) &nbsp;|&nbsp;
            <strong>0</strong> = ไม่ยืนยันอัตโนมัติ ผู้สมัครทุกคนรอเจ้าหน้าที่ยืนยัน
        </div>

        @if ($sections->isEmpty())
            <div class="alert alert-warning">ไม่มีคอร์สในกลุ่มนี้</div>
        @else
            <form method="POST" action="{{ route('admin.courseLimits.save') }}" id="limitForm">
                @csrf
                <input type="hidden" name="group" value="{{ $group }}">
                <input type="hidden" name="category_id" value="{{ $categoryId ?: '' }}">
                <input type="hidden" name="show_all" value="{{ $showAll ? 1 : 0 }}">

                <div class="table-responsive">
                    <table class="table table-bordered limit-table">
                        <thead class="table-light">
                            <tr>
                                <th>คอร์ส</th>
                                <th>ชาย</th>
                                <th>หญิง</th>
                                <th>รวมสูงสุด</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sections as $section)
                                @php $location = $section['location']; @endphp
                                {{-- หัวสถานที่ + ช่องตั้งค่าทั้งกลุ่ม (เติมค่าให้ทุกคอร์สของสถานที่นี้) --}}
                                <tr class="location-row" data-fill-location="{{ $location->id }}">
                                    <th>
                                        <div class="fs-5">{{ $location->name }}</div>
                                        <div class="small fw-normal text-muted">{{ $location->show_name }}</div>
                                    </th>
                                    @foreach (['male_limit' => 'ชาย', 'female_limit' => 'หญิง', 'max_limit' => 'รวมสูงสุด'] as $field => $fieldLabel)
                                        <th>
                                            <input type="number" min="0" max="{{ $maxSeats }}" inputmode="numeric"
                                                class="form-control form-control-sm seat-input"
                                                data-fill-field="{{ $field }}"
                                                aria-label="ตั้งค่าทั้งกลุ่ม {{ $location->name }}: {{ $fieldLabel }}">
                                        </th>
                                    @endforeach
                                    <th>
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-fill-button="{{ $location->id }}">
                                            ใช้กับทุกคอร์สของ{{ $location->name }}
                                        </button>
                                    </th>
                                </tr>

                                @foreach ($section['rows'] as $row)
                                    @php
                                        $category = $row['category'];
                                        $limit = $row['limit'];
                                        $inputName = "limits[{$category->id}][{$location->id}]";
                                        $oldKey = "limits.{$category->id}.{$location->id}";
                                    @endphp
                                    <tr data-location="{{ $location->id }}">
                                        <td>
                                            {{ $category->name }}
                                            @unless ($category->active)
                                                <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                            @endunless
                                            @unless ($limit)
                                                <span class="badge bg-light text-dark border">ยังไม่ตั้งค่า</span>
                                            @endunless
                                        </td>
                                        @foreach (['male_limit' => 'ชาย', 'female_limit' => 'หญิง', 'max_limit' => 'รวมสูงสุด'] as $field => $fieldLabel)
                                            <td>
                                                <input type="number" min="0" max="{{ $maxSeats }}"
                                                    inputmode="numeric" class="form-control form-control-sm seat-input"
                                                    name="{{ $inputName }}[{{ $field }}]"
                                                    data-field="{{ $field }}"
                                                    value="{{ old("{$oldKey}.{$field}", optional($limit)->{$field}) }}"
                                                    aria-label="{{ $category->name }} {{ $location->name }}: {{ $fieldLabel }}">
                                            </td>
                                        @endforeach
                                        <td>
                                            <input type="text" maxlength="{{ $maxDescriptionLength }}"
                                                class="form-control form-control-sm"
                                                name="{{ $inputName }}[description]"
                                                value="{{ old("{$oldKey}.description", optional($limit)->description) }}"
                                                aria-label="{{ $category->name }} {{ $location->name }}: หมายเหตุ">
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="save-bar d-flex justify-content-between align-items-center">
                    <span class="text-muted" id="limitChangedNote" hidden>มีการแก้ไขที่ยังไม่ได้บันทึก</span>
                    <button type="submit" class="btn btn-primary btn-lg ms-auto">บันทึก</button>
                </div>
            </form>
        @endif
    </div>

    <script>
        (function() {
            // ตัวกรอง: เปลี่ยนคอร์ส / ติ๊กแสดงทั้งหมด แล้วโหลดใหม่ทันที
            const filterForm = document.getElementById('limitFilterForm');
            if (filterForm) {
                filterForm.querySelectorAll('select, input[type="checkbox"]').forEach(function(control) {
                    control.addEventListener('change', function() {
                        filterForm.submit();
                    });
                });
            }

            const limitForm = document.getElementById('limitForm');
            if (!limitForm) return;

            const changedNote = document.getElementById('limitChangedNote');

            function markChanged(input) {
                input.classList.toggle('is-changed', input.value !== input.defaultValue);
                changedNote.hidden = !limitForm.querySelector('.is-changed');
            }

            limitForm.querySelectorAll('input[name]').forEach(function(input) {
                if (input.type === 'hidden') return;
                input.addEventListener('input', function() {
                    markChanged(input);
                });
            });

            // ปุ่ม "ใช้กับทุกคอร์สของสถานที่นี้": เติมค่าที่กรอกในแถวหัวสถานที่ ลงทุกคอร์สของสถานที่นั้น
            limitForm.querySelectorAll('[data-fill-button]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const locationId = button.dataset.fillButton;
                    const fillRow = limitForm.querySelector(`[data-fill-location="${locationId}"]`);

                    fillRow.querySelectorAll('[data-fill-field]').forEach(function(source) {
                        if (source.value === '') return;
                        limitForm
                            .querySelectorAll(`tr[data-location="${locationId}"] [data-field="${source.dataset.fillField}"]`)
                            .forEach(function(target) {
                                target.value = source.value;
                                markChanged(target);
                            });
                    });
                });
            });
        })();
    </script>
@endsection
