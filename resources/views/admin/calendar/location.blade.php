@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">จัดการปฏิทิน Location {{ $location_id }}</h1>
            <a href="{{ route('calendar.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> กลับ (Back)
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">อัปโหลดรูปปฏิทิน (Upload Image)</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('calendar.upload-image', ['location_id' => $location_id]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="year" class="form-label">ปี (Year)</label>
                                <select name="year" id="year" class="form-control" required>
                                    @php
                                        $currentYear = now()->year;
                                    @endphp
                                    @for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++)
                                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="lang" class="form-label">ภาษา (Language)</label>
                                <select name="lang" id="lang" class="form-control" required>
                                    <option value="th">ไทย (TH)</option>
                                    <option value="en">อังกฤษ (EN)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="index" class="form-label">Index (1-9)</label>
                                <select name="index" id="index" class="form-control" required>
                                    @for ($i = 1; $i <= 9; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">รูปภาพ (Image .jpg)</label>
                                <input type="file" name="image" id="image" class="form-control" accept=".jpg,.jpeg"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary">อัปโหลด (Upload)</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">รายการรูปภาพที่มีอยู่ (Existing Images)</h6>
                    </div>
                    <div class="card-body">
                        @if (isset($files) && count($files) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Lang</th>
                                            <th>Index</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($files as $f)
                                            <tr>
                                                <td>{{ $f['year'] }}</td>
                                                <td>{{ strtoupper($f['lang']) }}</td>
                                                <td>{{ $f['index'] }}</td>
                                                <td>
                                                    <a href="{{ $f['url'] }}" target="_blank"
                                                        class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <form
                                                        action="{{ route('calendar.delete-image', ['location_id' => $location_id]) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                        @csrf
                                                        <input type="hidden" name="filename" value="{{ $f['filename'] }}">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">ยังไม่มีรูปภาพ (No images found)</p>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
