@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Display Success Message --}}
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-center my-4">{{ isset($course) ? 'แก้ไข คอร์ส' : 'สร้างคอร์ส' }}</h1>

                        <form
                            action="{{ isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.save') }}"
                            method="POST">
                            @csrf
                            @if (isset($course))
                                @method('PUT')
                            @endif

                            <!-- Location -->
                            <div class="mb-3">
                                <label for="location_id" class="form-label">สถานที่</label>
                                <select name="location_id" id="location_id" class="form-control" required>
                                    <option value="" disabled selected>Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location_id', $course->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                            {{ $location->show_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category -->
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="" disabled selected>Select Category</option>
                                @foreach ($categories as $category)
                                    @if ($category->active == 1 || old('category_id', $course->category_id ?? '') == $category->id)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $course->category_id ?? '') == $category->id ? 'selected' : '' }}
                                            data-day="{{ $category->day ?? 0 }}">
                                            {{ $category->show_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                            <!-- Start Date -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="manual_date">
                                <label class="form-check-label" for="manual_date">
                                    แก้ไขวันเอง
                                </label>
                            </div>

                            <div class="mb-3">
                                <label for="date_start" class="form-label">วันที่เริ่ม</label>
                                <input type="date" name="date_start" id="date_start" class="form-control"
                                    value="{{ old('date_start') ? \Carbon\Carbon::parse(old('date_start'))->format('Y-m-d') : ($course->date_start ?? \Carbon\Carbon::now())->format('Y-m-d') }}"
                                    required>
                            </div>

                            <!-- End Date -->
                            <div class="mb-3">
                                <label for="date_end" class="form-label">วันที่จบ</label>
                                <input type="date" name="date_end" id="date_end" class="form-control"
                                    value="{{ old('date_end') ? \Carbon\Carbon::parse(old('date_end'))->format('Y-m-d') : ($course->date_end ?? \Carbon\Carbon::now())->format('Y-m-d') }}"
                                    required>
                            </div>



                            <script>
                                $(document).ready(function() {
                                    let descriptionEdited = false;

                                    function formatDate(date) {
                                        const yyyy = date.getFullYear();
                                        const mm = ('0' + (date.getMonth() + 1)).slice(-2);
                                        const dd = ('0' + date.getDate()).slice(-2);
                                        return `${yyyy}-${mm}-${dd}`;
                                    }

                                    function updateEndDate() {
                                        if ($('#manual_date').is(':checked')) return;

                                        const selectedOption = $('#category_id option:selected');
                                        const day = parseInt(selectedOption.data('day')) || 0;
                                        const startDateStr = $('#date_start').val();

                                        if (startDateStr) {
                                            const startDate = new Date(startDateStr);

                                            if (day > 0) {
                                                startDate.setDate(startDate.getDate() + day);
                                            }

                                            // ถ้า day = 0 ให้ end = start
                                            $('#date_end').val(formatDate(startDate));
                                        }
                                    }

                                    function updateStartDate() {
                                        if ($('#manual_date').is(':checked')) return;

                                        const selectedOption = $('#category_id option:selected');
                                        const day = parseInt(selectedOption.data('day')) || 0;
                                        const endDateStr = $('#date_end').val();

                                        if (endDateStr) {
                                            const endDate = new Date(endDateStr);

                                            if (day > 0) {
                                                endDate.setDate(endDate.getDate() - day);
                                            }

                                            // ถ้า day = 0 ให้ start = end
                                            $('#date_start').val(formatDate(endDate));
                                        }
                                    }


                                    function updateDescriptionIfNotEditedOrEmpty() {
                                        const selectedOption = $('#category_id option:selected');
                                        const showName = selectedOption.text().trim();
                                        const descriptionInput = $('#description');
                                        const currentVal = descriptionInput.val().trim();

                                        // อัปเดตถ้ายังไม่เคยแก้ หรือถ้าค่าว่าง
                                        if (!descriptionEdited || currentVal === '') {
                                            descriptionInput.val(showName).data('autofilled', true);
                                        }
                                    }

                                    // เมื่อผู้ใช้พิมพ์เอง → หยุด auto update
                                    $('#description').on('input', function() {
                                        const currentVal = $(this).val().trim();
                                        if (currentVal !== '') {
                                            descriptionEdited = true;
                                            $(this).data('autofilled', false);
                                        }
                                    });

                                    $('#category_id').on('change', function() {
                                        updateEndDate();
                                        updateDescriptionIfNotEditedOrEmpty();
                                    });

                                    $('#date_start').on('change', function() {
                                        updateEndDate();
                                    });

                                    $('#date_end').on('change', function() {
                                        updateStartDate();
                                    });

                                    // โหลดหน้า: ตรวจสอบว่าควร check manual หรือไม่ (ถ้าวันที่ไม่ตรงกับสูตร)
                                    // และคำนวณ end date ถ้ายังไม่ถูกแก้

                                    function checkManualMode() {
                                        const selectedOption = $('#category_id option:selected');
                                        const day = parseInt(selectedOption.data('day')) || 0;
                                        const startDateStr = $('#date_start').val();
                                        const endDateStr = $('#date_end').val();

                                        if (startDateStr && endDateStr) {
                                            const start = new Date(startDateStr);
                                            // คำนวณวันจบตามสูตร
                                            const expectedEnd = new Date(start);
                                            if (day > 0) {
                                                expectedEnd.setDate(expectedEnd.getDate() + day);
                                            }
                                            // ถ้า day=0, expectedEnd = start (logic ของ updateEndDate ในบรรทัด 111)

                                            // เปรียบเทียบ (formatStr)
                                            if (formatDate(new Date(endDateStr)) !== formatDate(expectedEnd)) {
                                                $('#manual_date').prop('checked', true);
                                            }
                                        }
                                    }

                                    checkManualMode();
                                    updateEndDate();
                                    updateDescriptionIfNotEditedOrEmpty();
                                });
                            </script>



                            <!-- State -->
                            <div class="mb-3">
                                <label for="state" class="form-label">สถานะ</label>
                                <select name="state" id="state" class="form-control" required>
                                    <option value="เปิดรับสมัคร"
                                        {{ old('state', $course->state ?? '') == 'เปิดรับสมัคร' ? 'selected' : '' }}>
                                        เปิดรับสมัคร</option>
                                    <option value="ปิดรับสมัคร"
                                        {{ old('state', $course->state ?? '') == 'ปิดรับสมัคร' ? 'selected' : '' }}>
                                        ปิดรับสมัคร</option>
                                    <option value="ยกเลิกคอร์ส"
                                        {{ old('state', $course->state ?? '') == 'ยกเลิกคอร์ส' ? 'selected' : '' }}>
                                        ยกเลิกคอร์ส</option>
                                </select>
                            </div>

                            <!-- description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">คำอธิบาย</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    value="{{ $course->description ?? '' }}">
                            </div>

                            <!-- listed -->
                            <div class="mb-3">
                                <label for="listed" class="form-label">Listed</label>
                                <select name="listed" id="listed" class="form-control" required>
                                    <option value="no"
                                        {{ old('no', $course->listed ?? '') == 'no' ? 'selected' : '' }}>no</option>
                                    <option value="yes"
                                        {{ old('yes', $course->listed ?? '') == 'yes' ? 'selected' : '' }}>yes</option>
                                </select>
                            </div>

                            <!-- Listed Date -->
                            <div class="mb-3">
                                <label for="listed_date" class="form-label">Listed Date</label>
                                <input type="date" name="listed_date" id="listed_date" class="form-control"
                                    value="{{ old('listed_date') ? \Carbon\Carbon::parse(old('listed_date'))->format('Y-m-d') : ($course->listed_date ?? \Carbon\Carbon::now())->format('Y-m-d') }}"
                                    required>
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-3 text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($course) ? 'Update Course' : 'Create Course' }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
