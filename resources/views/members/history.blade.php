@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center my-4">
            <div class="text-left my-4">{{ __('messages.course_history') }}: {{$user->name}} {{$user->surname}}  </div>


            @if($user->admin == 1)
                <a href="javascript:history.back()" id="back-button" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @else
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @endif

        </div>

        <script>
            // Hide the back button if there's no previous page
            document.addEventListener("DOMContentLoaded", function() {
                if (window.history.length <= 1) {
                    document.getElementById("back-button").style.display = "none";
                }
            });
        </script>


        <div class="row">
        </div>

        <table class="table">
            <thead>
            <tr>
                <td class="text-center w-[14%]">{{ __('messages.location') }}</td>
                <td class="text-center w-[14%]">{{ __('messages.course_name') }}</td>
                <td class="text-center w-[14%]">{{ __('messages.start_date') }}</td>
                <td class="text-center w-[14%]">{{ __('messages.end_date') }}</td>
                <td class="text-center w-[14%]">{{ __('messages.apply_date') }}</td>
                <td class="text-center w-[14%]">{{ __('messages.status') }}</td>
                <td class="text-center w-[16%]">{{ __('messages.application') }}</td>
            </tr>
            </thead>
            <tbody>
            @foreach($applies as $apply)
                <tr>
                    <td class="text-center">{{ $apply->location }}</td>
                    <td class="text-center">{{ $apply->category }}</td>
                    <td class="text-center">{{ $apply->date_start }}</td>
                    <td class="text-center">{{ $apply->date_end }}</td>
                    <td class="text-center">{{ $apply->apply_date }}</td>
                    <td class="text-center">{{ $apply->state }}</td>
                    <td>
                        @if($apply->state === 'เปิดรับสมัคร' && $apply->days_until_start > 0)
                            <a href="{{ route('courses.show', [$member_id, $apply->id]) }}" class="btn btn-secondary">{{ __('messages.edit') }}</a>
                        @else
                            {{ __('messages.closed') }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
