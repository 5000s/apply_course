@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">แก้ไขข้อมูลผู้สมัคร</h1>

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


        <form method="POST" action="{{ route('member.update') }}" class="border p-4 shadow rounded">
            @csrf
            @method('PUT')
            <h4>ข้อมูลผู้สมัคร</h4>
            <div class="row">
                <input type="hidden" id="member_id" name="member_id" value="{{$member->id}}">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อ</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}"  required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" id="surname" name="surname" value="{{ $member->surname }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">เพศ</label>
                        <select class="form-select form-control" id="gender" name="gender">
                            <option value="หญิง" {{ $member->gender == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                            <option value="ชาย" {{ $member->gender == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                        </select>
                    </div>
                    <!-- More fields from the left column -->
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">วันเกิด</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $member->birthdate->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">สัญชาติ</label>
                        <select class="form-control" id="nationality" name="nationality">
                            @foreach($nations as $nation)
                                <option  @if($member->nationality == $nation) selected @endif  value="{{$nation}}"> {{$nation}}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">จังหวัดที่อยู่ในไทย</label>
                        <select class="form-control" id="control" name="province">
                            @foreach($provinces as $province)
                                <option @if($member->province == $province['name_th']) selected @endif value="{{$province['name_th']}}"> {{$province['name_th']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
                <div class="col-md-6">
                    <!-- Right Column -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">โทรศัพท์</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $member->phone }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone2" class="form-label">โทรศัพท์ที่ 2</label>
                        <input type="text" class="form-control" id="phone2" name="phone2"  value="{{ $member->phone2 }}">
                    </div>
                    <div class="mb-3">
                        <label for="line" class="form-label">ไลน์</label>
                        <input type="text" class="form-control" id="line" name="line" value="{{ $member->line }}">
                    </div>
                    <!-- More fields from the right column -->
                    <div class="mb-3">
                        <label for="facebook" class="form-label">เฟซบุ๊ก</label>
                        <input type="text" class="form-control" id="facebook" name="facebook" value="{{ $member->facebook }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
            </div>
            <h4>การศึกษา และ อาชีพ</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="degree" class="form-label">ระดับการศึกษา</label>
                        <input type="text" class="form-control" id="degree" name="degree" value="{{ $member->degree }}">
                    </div>
                    <div class="mb-3">
                        <label for="career" class="form-label">อาชีพ</label>
                        <input type="text" class="form-control" id="career" name="career" value="{{ $member->career }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="organization" class="form-label">องค์กรที่ทำงาน</label>
                        <input type="text" class="form-control" id="organization" name="organization" value="{{ $member->organization }}">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">ความเชี่ยวชาญ</label>
                        <input type="text" class="form-control" id="expertise" name="expertise" value="{{ $member->expertise }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
            </div>
            <!-- Fields that don't fit into the two-column layout -->
            <!-- Dharma Experience -->
{{--            <h4>ประสบการณ์เข้าคอร์สปฏิบัติธรรมของผู้สมัคร</h4>--}}
{{--            <div class="mb-3">--}}
{{--                <label for="know_source" class="form-label">ที่มาของการรู้จักคอร์สปฏิบัติธรรมนี้</label>--}}
{{--                <input type="text" class="form-control" id="know_source" name="know_source" value="{{ $member->know_source }}">--}}
{{--            </div>--}}

{{--            <div class="mb-3">--}}
{{--                <label for="dharma_ex" class="form-label">เคยมีประสบการณ์เข้าคอร์สปฏิบัติธรรมหรือไม่</label>--}}
{{--                <select class="form-control" id="dharma_ex" name="dharma_ex" >--}}
{{--                    <option value="ไม่เคย">ไม่เคย</option>--}}
{{--                    <option value="เคย">เคย</option>--}}
{{--                </select>--}}
{{--            </div>--}}

{{--            <div class="mb-3">--}}
{{--                <label for="dharma_ex_desc" class="form-label">รายละเอียดประสบการณ์ปฏิบัติธรรม</label>--}}
{{--                <textarea class="form-control" id="dharma_ex_desc" name="dharma_ex_desc">{{ $member->dharma_ex_desc }}</textarea>--}}
{{--            </div>--}}



            <!-- Emergency Contact -->
            <h4>ผู้ติดต่อฉุกเฉิน</h4>
            <div class="mb-3">
                <label for="name_emergency" class="form-label">ชื่อจริง ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ $member->name_emergency }}">
            </div>

            <div class="mb-3">
                <label for="surname_emergency" class="form-label">นามสกุล ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ $member->surname_emergency }}">
            </div>

            <div class="mb-3">
                <label for="phone_emergency" class="form-label">โทรศัพท์ ผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ $member->phone_emergency }}">
            </div>

            <div class="mb-3">
                <label for="relation_emergency" class="form-label">ความสัมพันธ์กับผู้ติดต่อฉุกเฉิน</label>
                <input type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ $member->relation_emergency }}">
            </div>


            <div class="text-center">
                <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
            </div>

            <input type="hidden" id="rechecking_id" name="rechecking_id" value="{{$member->id}}">
        </form>
    </div>
@endsection
