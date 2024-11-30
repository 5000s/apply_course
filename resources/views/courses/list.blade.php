@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/course_table.css') }}">

    <div class="container">


        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center my-4">{{ __('messages.course_list') }}</h1>
            <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        </div>

        @include('partials.course_table', [
                       'location' => $location_saraburi,
                       'courses' => $courses_saraburi,
                   ])

        @include('partials.course_table', [
                      'location' => $location_hadyai,
                      'courses' => $courses_hadyai,
                  ])

        @include('partials.course_table', [
                      'location' => $location_bangkok,
                      'courses' => $courses_bangkok,
                  ])

        @include('partials.course_table', [
                            'location' => $location_surin,
                            'courses' => $courses_surin,
                        ])

        @include('partials.course_table', [
                            'location' => $location_phuket,
                            'courses' => $courses_phuket,
                        ])



    </div>
@endsection
