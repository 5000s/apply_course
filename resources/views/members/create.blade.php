@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>ฟอร์มเพิ่มข้อมูลสมาชิก</h1>
        <form method="POST" action="{{ route('member.store') }}">
            @csrf
            <div class="mb-3">
                <label for="gender" class="form-label">เพศ</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="หญิง">หญิง</option>
                    <option value="ชาย">ชาย</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="surname" class="form-label">นามสกุล</label>
                <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname') }}" required>
            </div>

            <div class="mb-3">
                <label for="nickname" class="form-label">ชื่อเล่น</label>
                <input type="text" class="form-control" id="nickname" name="nickname" value="{{ old('nickname') }}">
            </div>

            <div class="mb-3">
                <label for="age" class="form-label">อายุ</label>
                <input type="number" class="form-control" id="age" name="age" value="{{ old('age') }}">
            </div>

            <div class="mb-3">
                <label for="birthdate" class="form-label">วันเกิด</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">โทรศัพท์</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label for="phone2" class="form-label">โทรศัพท์ที่ 2</label>
                <input type="text" class="form-control" id="phone2" name="phone2" value="{{ old('phone2') }}">
            </div>

            <div class="mb-3">
                <label for="line" class="form-label">ไลน์</label>
                <input type="text" class="form-control" id="line" name="line" value="{{ old('line') }}">
            </div>

            <!-- Repeat for other fields as needed -->

            <div class="mb-3">
                <label for="nationality" class="form-label">สัญชาติ</label>
                <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality') }}">
            </div>


            <div class="mb-3">
                <label for="province" class="form-label">จังหวัด</label>
                <input type="text" class="form-control" id="province" name="province" value="{{ old('province') }}">
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">ประเทศ</label>
                <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}">
            </div>

            <div class="mb-3">
                <label for="facebook" class="form-label">เฟซบุ๊ก</label>
                <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook') }}">
            </div>

            <div class="mb-3">
                <label for="organization" class="form-label">องค์กร</label>
                <input type="text" class="form-control" id="organization" name="organization" value="{{ old('organization') }}">
            </div>

            <div class="mb-3">
                <label for="expertise" class="form-label">ความเชี่ยวชาญ</label>
                <input type="text" class="form-control" id="expertise" name="expertise" value="{{ old('expertise') }}">
            </div>

            <div class="mb-3">
                <label for="degree" class="form-label">วุฒิการศึกษา</label>
                <input type="text" class="form-control" id="degree" name="degree" value="{{ old('degree') }}">
            </div>

            <div class="mb-3">
                <label for="career" class="form-label">อาชีพ</label>
                <input type="text" class="form-control" id="career" name="career" value="{{ old('career') }}">
            </div>

            <div class="mb-3">
                <label for="dharma_ex" class="form-label">เคยมีประสบการณ์เข้าคอสปฐิยัติธรรมหรือไม่</label>
                <select class="form-control" id="dharma_ex" name="dharma_ex">
                    <option value="ไม่เคย">ไม่เคย</option>
                    <option value="เคย">เคย</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="dharma_ex_desc" class="form-label">รายละเอียดประสบการณ์ปฐิยัติธรรม</label>
                <textarea class="form-control" id="dharma_ex_desc" name="dharma_ex_desc">{{ old('dharma_ex_desc') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="know_source" class="form-label">ที่มาของการรู้จัก</label>
                <input type="text" class="form-control" id="know_source" name="know_source" value="{{ old('know_source') }}">
            </div>

            <div class="mb-3">
                <label for="name_emergency" class="form-label">ชื่อผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ old('name_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="surname_emergency" class="form-label">นามสกุลผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ old('surname_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="phone_emergency" class="form-label">โทรศัพท์ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ old('phone_emergency') }}">
            </div>

            <div class="mb-3">
                <label for="relation_emergency" class="form-label">ความสัมพันธ์กับผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ old('relation_emergency') }}">
            </div>

            <!-- Add all other fields in a similar fashion -->

            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
        </form>
    </div>
@endsection


