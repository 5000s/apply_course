@extends('layouts.app')

@section('content')
    @php
        $pattern = '/admin\/courses\/applylist\/\d+\/\d+\/form/';
       $isMatchingUrl = preg_match($pattern, $previous_url) ? true : false;

    @endphp

    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center">{{ __('messages.applicant_edit_form') }}</h1>

            @if($user->admin == 1)

                @if($isMatchingUrl )
                    <a href="javascript:history.back()"  class="btn btn-secondary">{{ __('messages.back') }}</a>
                    @else
                    <a href="{{ route('admin.members') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
                @endif


                    @else
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
           @endif

        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ __('messages.member_updated') }}
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
            <form method="POST" action="{{ route('member.update.admin') }}" class="border p-4 shadow rounded">
        @else
            <form method="POST" action="{{ route('member.update') }}" class="border p-4 shadow rounded">
        @endif

            @csrf
            @method('PUT')
            <h4>{{ __('messages.applicant') }}</h4>
            <div class="row">
                <input type="hidden" id="member_id" name="member_id" value="{{ $member->id }}">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">{{ __('messages.surname') }}</label>
                        <input type="text" class="form-control" id="surname" name="surname" value="{{ $member->surname }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">{{ __('messages.gender') }}</label>
                        <select class="form-select form-control" id="gender" name="gender">
                            <option value="หญิง" {{ $member->gender == 'หญิง' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                            <option value="ชาย" {{ $member->gender == 'ชาย' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                        </select>
                    </div>
                    <!-- More fields from the left column -->
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">{{ __('messages.birthdate') }}</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" value="{{ $member->birthdate->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">{{ __('messages.nationality') }}</label>
                        <select class="form-control" id="nationality" name="nationality">
                            @foreach($nations as $nation)
                                <option @if($member->nationality == $nation) selected @endif value="{{ $nation }}"> {{ $nation }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">{{ __('messages.province') }}</label>
                        <select class="form-control" id="province" name="province">
                            @foreach($provinces as $province)
                                <option @if($member->province == $province['name_th']) selected @endif value="{{ $province['name_th'] }}"> {{ $province['name_th'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
                <div class="col-md-6">
                    <!-- Right Column -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $member->phone }}" required>
                    </div>



                    <div class="mb-3">
                        <label for="phone2" class="form-label">{{ __('messages.phone2') }}</label>
                        <input type="text" class="form-control" id="phone2" name="phone2" value="{{ $member->phone2 }}">
                    </div>
                    <div class="mb-3">
                        <label for="line" class="form-label">{{ __('messages.line') }}</label>
                        <input type="text" class="form-control" id="line" name="line" value="{{ $member->line }}">
                    </div>
                    <!-- More fields from the right column -->
                    <div class="mb-3">
                        <label for="facebook" class="form-label">{{ __('messages.facebook') }}</label>
                        <input type="text" class="form-control" id="facebook" name="facebook" value="{{ $member->facebook }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
            </div>
            <h4>{{ __('messages.education') }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Left Column -->
                    <div class="mb-3">
                        <label for="degree" class="form-label">{{ __('messages.degree') }}</label>
                        <input type="text" class="form-control" id="degree" name="degree" value="{{ $member->degree }}">
                    </div>
                    <div class="mb-3">
                        <label for="career" class="form-label">{{ __('messages.career') }}</label>
                        <input type="text" class="form-control" id="career" name="career" value="{{ $member->career }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="organization" class="form-label">{{ __('messages.organization') }}</label>
                        <input type="text" class="form-control" id="organization" name="organization" value="{{ $member->organization }}">
                    </div>
                    <div class="mb-3">
                        <label for="expertise" class="form-label">{{ __('messages.expertise') }}</label>
                        <input type="text" class="form-control" id="expertise" name="expertise" value="{{ $member->expertise }}">
                    </div>
                    <!-- Continue with other fields similarly -->
                </div>
            </div>
            <!-- Fields that don't fit into the two-column layout -->
            <!-- Emergency Contact -->
            <h4>{{ __('messages.emergency_contact') }}</h4>
            <div class="mb-3">
                <label for="name_emergency" class="form-label">{{ __('messages.emergency_name') }}</label>
                <input type="text" class="form-control" id="name_emergency" name="name_emergency" value="{{ $member->name_emergency }}">
            </div>

            <div class="mb-3">
                <label for="surname_emergency" class="form-label">{{ __('messages.emergency_surname') }}</label>
                <input type="text" class="form-control" id="surname_emergency" name="surname_emergency" value="{{ $member->surname_emergency }}">
            </div>

            <div class="mb-3">
                <label for="phone_emergency" class="form-label">{{ __('messages.emergency_phone') }}</label>
                <input type="text" class="form-control" id="phone_emergency" name="phone_emergency" value="{{ $member->phone_emergency }}">
            </div>

            <div class="mb-3">
                <label for="relation_emergency" class="form-label">{{ __('messages.emergency_relation') }}</label>
                <input type="text" class="form-control" id="relation_emergency" name="relation_emergency" value="{{ $member->relation_emergency }}">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>

            <input type="hidden" id="rechecking_id" name="rechecking_id" value="{{ $member->id }}">


                @if( $user->admin == 1 )
                    <h4> สำหรับ Admin เท่านั้นที่แก้ไขได้ </h4>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger"></span></label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ $member->email }}">
                    </div>

                    <div class="mb-3">
                        <label for="buddhism" class="form-label">Buddhism <span class="text-danger"></span></label>
                        <select class="form-control" id="buddhism" name="buddhism">
                            @foreach(\App\Models\Member::getEnumValues('buddhism') as $value)
                                <option value="{{ $value }}" {{ $member->buddhism == $value ? 'selected' : '' }}>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger"></span></label>
                        <select class="form-control" id="status" name="status">
                            @foreach(\App\Models\Member::getEnumValues('status') as $value)
                                <option value="{{ $value }}" {{ $member->status == $value ? 'selected' : '' }}>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blacklist" class="form-label">Blacklist <span class="text-danger"></span></label>
                        <select class="form-control" id="blacklist" name="blacklist">
                            @foreach(\App\Models\Member::getEnumValues('blacklist') as $value)
                                <option value="{{ $value }}" {{ $member->blacklist == $value ? 'selected' : '' }}>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blacklist_release" class="form-label">Blacklist End <span class="text-danger"></span></label>
                        <input type="date" class="form-control" id="blacklist_release" name="blacklist_release" value="{{ $member->blacklist_release }}">
                    </div>

                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudo <span class="text-danger"></span></label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo" value="{{ $member->pseudo }}">
                    </div>
                @endif




            </form>
    </div>
@endsection
