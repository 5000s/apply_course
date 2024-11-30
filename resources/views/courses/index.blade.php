@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/course_table.css') }}">
    <div class="container">

            <div class="d-flex justify-content-between align-items-center my-4">
                <h1 class="text-center my-4">{{ __('messages.course_list') }}</h1>
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            </div>


        <div class="row">
            @foreach ($course_locations as $location)
                <div class="col-lg-3 col-md-4 col-sm-6 text-center my-2">
                    <a href="{{ route('courses.index', ['member_id' => $member_id, 'location' => $location->id]) }}"
                       class="btn {{ $location->id == $selected_location_id ? 'btn-success' : 'btn-light' }} option_course">
                       <div style="max-width: 150px;"> {{ $location->show_name }} </div>
                    </a>
                </div>
            @endforeach
        </div>



        @include('partials.course_table_regis', [
                  'location' => $course_location,
                  'courses' => $courses,
              ])


        {{--        <table class="table">--}}
        {{--            <thead>--}}
        {{--            <tr>--}}
        {{--                <td class="text-center w-[20%]">{{ __('messages.course_name') }}</td>--}}
        {{--                <td class="text-center w-[20%]">{{ __('messages.start_date') }}</td>--}}
        {{--                <td class="text-center w-[20%]">{{ __('messages.end_date') }}</td>--}}
        {{--                <td class="text-center w-[20%]">{{ __('messages.status') }}</td>--}}
        {{--                <td class="text-center w-[20%]">{{ __('messages.application') }}</td>--}}
        {{--            </tr>--}}
        {{--            </thead>--}}
        {{--            <tbody>--}}
        {{--            @foreach($courses as $course)--}}
        {{--                <tr>--}}
        {{--                    <td class="text-center">{{ $course->category }}</td>--}}
        {{--                    <td class="text-center">{{ $course->date_start }}</td>--}}
        {{--                    <td class="text-center">{{ $course->date_end }}</td>--}}
        {{--                    <td class="text-center">{{ $course->state }}</td>--}}
        {{--                    <td>--}}
        {{--                        @if($course->state === 'เปิดรับสมัคร')--}}
        {{--                            @if(is_null($course->apply_id))--}}
        {{--                                <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-primary">{{ __('messages.register') }}</a>--}}
        {{--                            @else--}}
        {{--                                <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-secondary">{{ __('messages.edit') }}</a>--}}
        {{--                            @endif--}}
        {{--                        @else--}}
        {{--                            {{ __('messages.closed') }}--}}
        {{--                        @endif--}}
        {{--                    </td>--}}
        {{--                </tr>--}}
        {{--            @endforeach--}}
        {{--            </tbody>--}}
        {{--        </table>--}}
    </div>
@endsection
