@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- Display Success Message --}}
        @if(session('success'))
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

                        <form action="{{ isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.create') }}" method="POST">
                            @csrf
                            @if (isset($course))
                                @method('PUT')
                            @endif

                            <!-- Start Date -->
                            <div class="mb-3">
                                <label for="date_start" class="form-label">วันที่เริ่ม</label>
                                <input type="date"
                                       name="date_start"
                                       id="date_start"
                                       class="form-control"
                                       value="{{ old('date_start') ? \Carbon\Carbon::parse(old('date_start'))->format('Y-m-d') : ($course->date_start ?? \Carbon\Carbon::now())->format('Y-m-d') }}"
                                       required>
                            </div>

                            <!-- End Date -->
                            <div class="mb-3">
                                <label for="date_end" class="form-label">วันที่จบ</label>
                                <input type="date"
                                       name="date_end"
                                       id="date_end"
                                       class="form-control"
                                       value="{{ old('date_end') ? \Carbon\Carbon::parse(old('date_end'))->format('Y-m-d') : ($course->date_end ?? \Carbon\Carbon::now())->format('Y-m-d') }}"
                                       required>
                            </div>

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
                            <div class="mb-3">
                                <label for="category_id" class="form-label">ประเภทคอร์ส</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="" disabled selected>Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $course->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->show_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- State -->
                            <div class="mb-3">
                                <label for="state" class="form-label">สถานะ</label>
                                <select name="state" id="state" class="form-control" required>
                                    <option value="เปิดรับสมัคร"
                                        {{ old('state', $course->state ?? '') == 'เปิดรับสมัคร' ? 'selected' : '' }}>เปิดรับสมัคร</option>
                                    <option value="ปิดรับสมัคร"
                                        {{ old('state', $course->state ?? '') == 'ปิดรับสมัคร' ? 'selected' : '' }}>ปิดรับสมัคร</option>
                                    <option value="ยกเลิกคอร์ส"
                                        {{ old('state', $course->state ?? '') == 'ยกเลิกคอร์ส' ? 'selected' : '' }}>ยกเลิกคอร์ส</option>
                                </select>
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
