@extends('layouts.master')

@section('content')

    <div class="container">

        <h1 class="text-center my-4">{{ $course->category }}, {{ $course->location }}</h1>
        <h3 class="text-center my-4">{{ $course->date_start->format('d/m/Y') . "-". $course->date_end->format('d/m/Y') }}</h3>
        <div class=" my-4" style="text-align: right">
            <a href="{{ route('member.edit', $member->id) }}" class="btn btn-secondary">แก้ไขข้อมูลผู้สมัคร</a>
            <a href="{{ route('courses.history', $member->id) }}" class="btn btn-secondary">ดูประวัติการสมัคร</a>
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
        <div class="card">
            <div class="card-body">

                <h4>ข้อมูลผู้สมัคร</h4>
                <div class="row">
                    <input disabled type="hidden" id="member_id" name="member_id" value="{{$member->id}}">
                    <div class="col-md-6">
                        <!-- Left Column -->
                        <div class="mb-3">
                            <label for="name" class="form-label">ชื่อ</label>
                            <input disabled type="text" class="form-control" id="name" name="name" value="{{ $member->name }}"  required>
                        </div>
                        <div class="mb-3">
                            <label for="surname" class="form-label">นามสกุล</label>
                            <input disabled type="text" class="form-control" id="surname" name="surname" value="{{ $member->surname }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">เพศ</label>
                            <select disabled class="form-select form-control" id="gender" name="gender">
                                <option value="หญิง" {{ $member->gender == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                <option value="ชาย" {{ $member->gender == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                            </select>
                        </div>
                        <!-- More fields from the left column -->
                        <div class="mb-3">
                            <label for="birthdate" class="form-label">วันเกิด</label>
                            <input disabled type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $member->birthdate->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="nationality" class="form-label">สัญชาติ</label>
                            <select disabled class="form-control" id="nationality" name="nationality">
                                @foreach($nations as $nation)
                                    <option  @if($member->nationality == $nation) selected @endif  value="{{$nation}}"> {{$nation}}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="province" class="form-label">จังหวัดที่อยู่ในไทย</label>
                            <select disabled class="form-control" id="control" name="province">
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
                            <input disabled type="text" class="form-control" id="phone" name="phone" value="{{ $member->phone }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone2" class="form-label">โทรศัพท์ที่ 2</label>
                            <input disabled type="text" class="form-control" id="phone2" name="phone2"  value="{{ $member->phone2 }}">
                        </div>
                        <div class="mb-3">
                            <label for="line" class="form-label">ไลน์</label>
                            <input disabled type="text" class="form-control" id="line" name="line" value="{{ $member->line }}">
                        </div>
                        <!-- More fields from the right column -->
                        <div class="mb-3">
                            <label for="facebook" class="form-label">เฟซบุ๊ก</label>
                            <input disabled type="text" class="form-control" id="facebook" name="facebook" value="{{ $member->facebook }}">
                        </div>
                        <!-- Continue with other fields similarly -->
                    </div>
                </div>
                <h4>{{ __('messages.medical') }}</h4>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input
                            disabled
                            type="text"
                            class="form-control"
                            id="medical_condition"
                            name="medical_condition"
                            maxlength="255"
                            value="{{ $member->medical_condition }}"
                            required
                        >
                    </div>
                </div>
                <h4>การศึกษา และ อาชีพ</h4>
                <div class="row">
                    <div class="col-md-6">
                        <!-- Left Column -->
                        <div class="mb-3">
                            <label for="degree" class="form-label">ระดับการศึกษา</label>
                            <input disabled type="text" class="form-control" id="degree" name="degree" value="{{ $member->degree }}">
                        </div>
                        <div class="mb-3">
                            <label for="career" class="form-label">อาชีพ</label>
                            <input disabled type="text" class="form-control" id="career" name="career" value="{{ $member->career }}">
                        </div>
                        <!-- Continue with other fields similarly -->
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="organization" class="form-label">องค์กรที่ทำงาน</label>
                            <input disabled type="text" class="form-control" id="organization" name="organization" value="{{ $member->organization }}">
                        </div>
                        <div class="mb-3">
                            <label for="expertise" class="form-label">ความเชี่ยวชาญ</label>
                            <input disabled type="text" class="form-control" id="expertise" name="expertise" value="{{ $member->expertise }}">
                        </div>
                        <!-- Continue with other fields similarly -->
                    </div>
                </div>
                <h4>ผู้ติดต่อฉุกเฉิน</h4>
                <div class="mb-3">
                    <label for="name_emergency" class="form-label">ชื่อจริง ผู้ติดต่อฉุกเฉิน</label>
                    <input disabled type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ $member->name_emergency }}">
                </div>

                <div class="mb-3">
                    <label for="surname_emergency" class="form-label">นามสกุล ผู้ติดต่อฉุกเฉิน</label>
                    <input disabled type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ $member->surname_emergency }}">
                </div>

                <div class="mb-3">
                    <label for="phone_emergency" class="form-label">โทรศัพท์ ผู้ติดต่อฉุกเฉิน</label>
                    <input disabled type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ $member->phone_emergency }}">
                </div>

                <div class="mb-3">
                    <label for="relation_emergency" class="form-label">ความสัมพันธ์กับผู้ติดต่อฉุกเฉิน</label>
                    <input disabled type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ $member->relation_emergency }}">
                </div>

                <h4>ใบสมัคร</h4>
                <img src="{{ asset('storage/' . $apply->application) }}" alt="Uploaded Image" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>

@endsection
