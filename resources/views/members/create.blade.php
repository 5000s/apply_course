@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center my-4">{{ __('messages.applicant_form') }}</h1>

            @if($user->admin == 1)
                <a href="{{ route('admin.members') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @else
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @endif

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

        @if($user->admin == 1)
            <form method="POST" action="{{ route('member.store.admin') }}" class="border p-4 shadow rounded">
        @else
            <form method="POST" action="{{ route('member.store') }}" class="border p-4 shadow rounded">
        @endif
            @csrf
            <h4>{{ __('messages.applicant') }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">{{ __('messages.surname') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">{{ __('messages.gender') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-control" id="gender" name="gender" required>
                            <option value="หญิง">{{ __('messages.female') }}</option>
                            <option value="ชาย">{{ __('messages.male') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">{{ __('messages.birthdate') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">{{ __('messages.nationality') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="nationality" name="nationality" required>
                            @foreach($nations as $nation)
                                <option value="{{$nation}}">{{$nation}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">{{ __('messages.province') }} <span class="text-danger">*</span></label>
                        <select class="form-control" id="province" name="province" required>
                            @foreach($provinces as $province)
                                <option value="{{$province['name_th']}}">{{$province['name_th']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Right Column -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">{{ __('messages.phone') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone2" class="form-label">{{ __('messages.phone2') }}</label>
                        <input type="text" class="form-control" id="phone2" name="phone2">
                    </div>
                    <div class="mb-3">
                        <label for="line" class="form-label">{{ __('messages.line') }}</label>
                        <input type="text" class="form-control" id="line" name="line">
                    </div>
                    <div class="mb-3">
                        <label for="facebook" class="form-label">{{ __('messages.facebook') }}</label>
                        <input type="text" class="form-control" id="facebook" name="facebook">
                    </div>
                </div>
            </div>
            <h4>{{ __('messages.medical') }}</h4>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="medical_condition"
                            name="medical_condition"
                            maxlength="255"
                            value="{{ old('medical_condition') }}"
                            required
                        >
                        @error('medical_condition')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            <h4>{{ __('messages.education') }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="degree" class="form-label">{{ __('messages.degree') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="degree" name="degree" required>
                    </div>
                    <div class="mb-3">
                        <label for="career" class="form-label">{{ __('messages.career') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="career" name="career" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Right Column -->
                    <div class="mb-3">
                        <label for="organization" class="form-label">{{ __('messages.organization') }}</label>
                        <input type="text" class="form-control" id="organization" name="organization">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">{{ __('messages.expertise') }}</label>
                        <input type="text" class="form-control" id="expertise" name="expertise">
                    </div>
                </div>
            </div>
            <h4>{{ __('messages.emergency_contact') }}</h4>
            <div class="mb-3">
                <label for="name_emergency" class="form-label">{{ __('messages.emergency_name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ old('name_emergency') }}" required>
            </div>
            <div class="mb-3">
                <label for="surname_emergency" class="form-label">{{ __('messages.emergency_surname') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ old('surname_emergency') }}" required>
            </div>
            <div class="mb-3">
                <label for="phone_emergency" class="form-label">{{ __('messages.emergency_phone') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ old('phone_emergency') }}" required>
            </div>
            <div class="mb-3">
                <label for="relation_emergency" class="form-label">{{ __('messages.emergency_relation') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ old('relation_emergency') }}" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>


                @if( $user->admin == 1 )
                    <h4> สำหรับ Admin เท่านั้นที่แก้ไขได้ </h4>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger"></span></label>
                        <input type="text" class="form-control" id="email" name="email"  value="{{ $user->email }}">
                    </div>

                    <div class="mb-3">
                        <label for="shelter" class="form-label">Shelter Number <span class="text-danger"></span></label>
                        <select class="form-control" id="shelter_number" name="shelter_number">
                            <option  value="0">ทั่วไป</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="buddhism" class="form-label">Buddhism <span class="text-danger"></span></label>
                        <select class="form-control" id="buddhism" name="buddhism">
                            @foreach(\App\Models\Member::getEnumValues('buddhism') as $value)
                                <option value="{{ $value }}">
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger"></span></label>
                        <select class="form-control" id="status" name="status">
                            @foreach(\App\Models\Member::getEnumValues('status') as $value)
                                <option value="{{ $value }}" >
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blacklist" class="form-label">Blacklist <span class="text-danger"></span></label>
                        <select class="form-control" id="blacklist" name="blacklist">
                            @foreach(\App\Models\Member::getEnumValues('blacklist') as $value)
                                <option value="{{ $value }}" >
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blacklist_release" class="form-label">Blacklist End <span class="text-danger"></span></label>
                        <input type="date" class="form-control" id="blacklist_release" name="blacklist_release" value="1970-01-01" >
                    </div>

                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudo <span class="text-danger"></span></label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo">
                    </div>
                @endif



            </form>
    </div>
@endsection
