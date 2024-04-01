@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">ฟอร์มเพิ่มข้อมูลผู้สมัคร</h1>
        <form method="POST" action="{{ route('member.store') }}" class="border p-4 shadow rounded">
            @csrf
            <h4>ข้อมูลผู้สมัคร</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">เพศ</label>
                        <select class="form-select form-control" id="gender" name="gender">
                            <option value="หญิง">หญิง</option>
                            <option value="ชาย">ชาย</option>
                        </select>
                    </div>
                    <!-- More fields from the left column -->
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">วันเกิด</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate">
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">สัญชาติ</label>
                        <input type="text" class="form-control" id="nationality" name="nationality">
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">จังหวัด</label>
                        <input type="text" class="form-control" id="province" name="province">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
                <div class="col-md-6">
                    <!-- Right Column -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">โทรศัพท์</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="phone2" class="form-label">โทรศัพท์ที่ 2</label>
                        <input type="text" class="form-control" id="phone2" name="phone2">
                    </div>
                    <div class="mb-3">
                        <label for="line" class="form-label">ไลน์</label>
                        <input type="text" class="form-control" id="line" name="line">
                    </div>
                    <!-- More fields from the right column -->
                    <div class="mb-3">
                        <label for="facebook" class="form-label">เฟซบุ๊ก</label>
                        <input type="text" class="form-control" id="facebook" name="facebook">
                    </div>
                    <div class="mb-3">
                        <label for="organization" class="form-label">องค์กร</label>
                        <input type="text" class="form-control" id="organization" name="organization">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">ความเชี่ยวชาญ</label>
                        <input type="text" class="form-control" id="expertise" name="expertise">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
            </div>
            <!-- Fields that don't fit into the two-column layout -->
            <!-- Dharma Experience -->
            <h4>ประสบการณ์เข้าคอร์สปฏิบัติธรรมของผู้สมัคร</h4>
            <div class="mb-3">
                <label for="know_source" class="form-label">ที่มาของการรู้จักคอร์สปฏิบัติธรรมนี้</label>
                <input type="text" class="form-control" id="know_source" name="know_source" value="{{ old('know_source') }}">
            </div>

            <div class="mb-3">
                <label for="dharma_ex" class="form-label">เคยมีประสบการณ์เข้าคอร์สปฏิบัติธรรมหรือไม่</label>
                <select class="form-control" id="dharma_ex" name="dharma_ex">
                    <option value="ไม่เคย">ไม่เคย</option>
                    <option value="เคย">เคย</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="dharma_ex_desc" class="form-label">รายละเอียดประสบการณ์ปฏิบัติธรรม</label>
                <textarea class="form-control" id="dharma_ex_desc" name="dharma_ex_desc">{{ old('dharma_ex_desc') }}</textarea>
            </div>



            <!-- Emergency Contact -->
            <h4>ผู้ติดต่อฉุกเฉิน</h4>
            <div class="mb-3">
                <label for="name_emergency" class="form-label">ชื่อจริง ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ old('name_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="surname_emergency" class="form-label">นามสกุล ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ old('surname_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="phone_emergency" class="form-label">โทรศัพท์ ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ old('phone_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="relation_emergency" class="form-label">ความสัมพันธ์กับผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ old('relation_emergency') }}">
            </div>


            <div class="text-center">
                <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
@endsection
