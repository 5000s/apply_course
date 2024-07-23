@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">{{ __('messages.applicant_form') }}</h1>
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

        <form method="POST" action="{{ route('member.store') }}" class="border p-4 shadow rounded">
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
        </form>
    </div>
@endsection
