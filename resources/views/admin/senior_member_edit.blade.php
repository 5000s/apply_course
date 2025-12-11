@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center">แก้ไขข้อมูลสมาชิกอาวุโส</h1>
            <a href="{{ route('admin.members.senior') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        </div>

        @if (session('success'))
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

        <form method="POST" action="{{ route('admin.members.senior.update', $member->id) }}"
            class="border p-4 shadow rounded bg-white">
            @csrf
            @method('PUT')

            <h4 class="mb-3">ข้อมูลทั่วไป</h4>
            <div class="row mb-4">
                <div class="col-md-5">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" value="{{ $member->name }} {{ $member->surname }}" readonly
                        disabled style="background-color: #f0f2f5;">
                </div>
                <div class="col-md-5">
                    <label class="form-label">ชื่อเล่น</label>
                    <input type="text" class="form-control" value="{{ $member->nickname }}" readonly disabled
                        style="background-color: #f0f2f5;">
                </div>
                <div class="col-md-2">
                    <label class="form-label">อายุ</label>
                    <input type="text" class="form-control" value="{{ $member->birthdate?->age ?? '-' }}" readonly
                        disabled style="background-color: #f0f2f5;">
                </div>
            </div>

            <h4 class="mb-3">ข้อมูลอาวุโส</h4>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label for="current_level" class="form-label">ขั้นปัจจุบัน</label>
                    <select class="form-control form-select" id="current_level" name="current_level">
                        @for ($i = 0; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ $member->current_level == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <label for="level_1_date" class="form-label">วันที่ได้ขั้น 1</label>
                    <input type="date" class="form-control" id="level_1_date" name="level_1_date"
                        value="{{ $member->level_1_date }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="level_2_date" class="form-label">วันที่ได้ขั้น 2</label>
                    <input type="date" class="form-control" id="level_2_date" name="level_2_date"
                        value="{{ $member->level_2_date }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="level_3_date" class="form-label">วันที่ได้ขั้น 3</label>
                    <input type="date" class="form-control" id="level_3_date" name="level_3_date"
                        value="{{ $member->level_3_date }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="level_4_date" class="form-label">วันที่ได้ขั้น 4</label>
                    <input type="date" class="form-control" id="level_4_date" name="level_4_date"
                        value="{{ $member->level_4_date }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.members.senior') }}" class="btn btn-outline-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary px-4">บันทึกการแก้ไข</button>
            </div>

        </form>
    </div>
@endsection
