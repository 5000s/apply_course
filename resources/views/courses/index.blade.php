@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/course_table.css') }}">

    <div class="container">
        {{-- Title + Back Button --}}
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center text-primary fw-bold">{{ __('messages.course_list') }}</h1>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        {{-- Location Selection --}}
        <div class="row justify-content-center">
            @foreach ($course_locations as $location)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('courses.index', ['member_id' => $member_id, 'location' => $location->id]) }}"
                       class="btn option_course w-100 text-center py-3 shadow-sm
                              {{ $location->id == $selected_location_id ? 'btn-success text-white' : 'btn-outline-success' }}">
                        <div class="fw-bold">{{ $location->show_name }}</div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Include Course Table --}}
        @include('partials.course_table_regis', [
              'location' => $course_location,
              'courses' => $courses,
              'allow_types' => $allow_types,
        ])
    </div>

@endsection

@push('styles')
    <style>
        /* Custom Styles */
        .text-primary {
            color: #0d6efd;
        }
        .fw-bold {
            font-weight: bold;
        }
        .option_course {
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }
        .option_course:hover {
            transform: translateY(-2px);
        }
        .btn-outline-success {
            border: 2px solid #198754;
            color: #198754;
        }
        .btn-outline-success:hover {
            background-color: #198754;
            color: white;
        }
        .btn-success {
            background-color: #198754 !important;
            border: none;
        }
        .shadow-sm {
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush
