@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center">แก้ไขข้อมูลสมาชิกอาวุโส</h1>
            <a href="{{ route('admin.members.senior') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.members.senior.update', $member->id) }}" class="border p-4 shadow rounded bg-white">
            @csrf
            @method('PUT')
            
            <h4 class="mb-3">ข้อมูลทั่วไป</h4>
            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" value="{{ $member->name }} {{ $member->surname }}" readonly disabled style="background-color: #f0f2f5;">
                </div>
                <div class="col-md-5">
                    <label class="form-label">ชื่อเล่น</label>
                    <input type="text" class="form-control" value="{{ $member->nickname }}" readonly disabled style="background-color: #f0f2f5;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">อายุ</label>
                    <input type="text" class="form-control" value="{{ $member->birthdate?->age ?? '-' }}" readonly disabled style="background-color: #f0f2f5;">
                </div>
            </div>

            <h4 class="mb-3">ข้อมูลอาวุโส</h4>
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">ขั้นปัจจุบัน</label>
                    <input type="text" class="form-control" value="{{ $member->current_level }}" readonly disabled style="background-color: #f0f2f5;">
                </div>
                <div class="col-md-3">
                     <label for="leave_date" class="form-label">วันที่ออกจากสายธรรม</label>
                     <input type="date" class="form-control" id="leave_date" name="leave_date" value="{{ $member->leave_date }}">
                </div>
                <div class="col-md-3">
                    <label for="promote_level" class="form-label">ปรับขั้นเป็น</label>
                    <select class="form-control form-select" id="promote_level" name="promote_level">
                        <option value="">เลือกขั้น (ไม่ปรับ)</option>
                        @for($i = 1; $i <= 4; $i++)
                             <option value="{{ $i }}" {{ $member->current_level == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="promote_date" class="form-label">วันที่เลื่อนขั้น</label>
                    <input type="date" class="form-control" id="promote_date" name="promote_date">
                    <small class="text-muted">* ระบุเมื่อมีการปรับขั้น</small>
                </div>
            </div>

            <h4 class="mb-3">สถานะบุคคล</h4>
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="death_date" class="form-label">วันที่เสียชีวิต</label>
                    <input type="date" class="form-control" id="death_date" name="death_date" value="{{ $member->death_date }}">
                </div>
            </div>

            <h4 class="mb-3">หมายเหตุ</h4>
            <div class="mb-4">
                <textarea class="form-control" id="leave_description" name="leave_description" rows="4" placeholder="ระบุข้อมูลเพิ่มเติม (ถ้ามี)">{{ $member->leave_description }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.members.senior') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary px-4">บันทึกการแก้ไข</button>
            </div>

        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const promoteLevelSelect = document.getElementById('promote_level');
            const promoteDateInput = document.getElementById('promote_date');

            function updatePromoteDateState() {
                if (promoteLevelSelect.value) {
                    promoteDateInput.setAttribute('required', 'required');
                    promoteDateInput.classList.add('border', 'border-danger', 'shadow-sm');
                    promoteDateInput.style.backgroundColor = '#fff5f5'; // Light red tint for emphasis
                } else {
                    promoteDateInput.removeAttribute('required');
                    promoteDateInput.classList.remove('border', 'border-danger', 'shadow-sm');
                    promoteDateInput.style.backgroundColor = '';
                }
            }

            // Run on load
            updatePromoteDateState();

            // Run on change
            promoteLevelSelect.addEventListener('change', updatePromoteDateState);
        });
    </script>
@endsection
