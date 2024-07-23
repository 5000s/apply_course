@extends('layouts.app')

@section('content')
    <div class="container">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3 text-center">
                <h2>{{ __('messages.enter_details') }}</h2>
                <p>{{ __('messages.enter_name_surname') }}</p>
                <form id="request-access-form" method="POST" action="{{ route('check-email') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="{{ __('messages.first_name') }}" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="{{ __('messages.last_name') }}" required>
                    </div>
                    <div class="form-group">
                        <select name="birth_year" id="birth_year" class="form-control" required>
                            <option value="">{{ __('messages.select_birth_year') }}</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = $currentYear - 90;
                            @endphp
                            @for ($year = ($currentYear-6); $year >= $startYear ; $year--)
                                <option value="{{ $year }}">{{ $year+543 }} ({{$year}})</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="">{{ __('messages.gender') }}</option>
                            <option value="ชาย">{{ __('messages.male') }}</option>
                            <option value="หญิง">{{ __('messages.female') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                        <i class="fas fa-envelope"></i> {{ __('messages.check_email') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#first_name, #last_name').on('input', function() {
                var sanitizedValue = sanitizeInput($(this).val());
                $(this).val(sanitizedValue);
            });

            $('#request-access-form').on('submit', function(event) {
                var firstName = $('#first_name').val().trim();
                var lastName = $('#last_name').val().trim();
                var birthYear = $('#birth_year').val().trim();
                var gender = $('#gender').val().trim();

                if (firstName.length <= 2 || lastName.length <= 2) {
                    event.preventDefault();
                    alert('{{ __('messages.name_length_error') }}');
                }

                if (birthYear === '') {
                    event.preventDefault();
                    alert('{{ __('messages.select_birth_year_error') }}');
                }

                if (gender === '') {
                    event.preventDefault();
                    alert('{{ __('messages.select_gender_error') }}');
                }
            });

            function sanitizeInput(input) {
                return input.replace(/[^A-Za-zก-๙]+/g, '').trim();
            }
        });
    </script>
@endsection
