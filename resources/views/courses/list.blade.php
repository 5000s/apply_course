@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/course_table.css') }}">

    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="fw-bold text-center text-primary">{{ __('messages.course_list') }}</h1>
            <a href="{{ route('profile') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="row">
            @foreach([
                ['location' => $location_bangkok, 'courses' => $courses_bangkok],
                ['location' => $location_saraburi, 'courses' => $courses_saraburi],
                ['location' => $location_hadyai, 'courses' => $courses_hadyai],
                ['location' => $location_phuket, 'courses' => $courses_phuket],
                ['location' => $location_surin, 'courses' => $courses_surin]
            ] as $courseData)
                <div class="col-lg-12 mb-12 pb-5">
                    @include('partials.course_table', $courseData)
                </div>
            @endforeach
        </div>

    </div>

@endsection
