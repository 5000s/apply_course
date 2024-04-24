@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">Edit Application</h1>
        <div class="card">
            <div class="card-body">
                <p>You have applied for: {{ $courseApplication->course->name }}</p>
                <p>Current Registration Form:</p>
                <a href="{{ Storage::url($courseApplication->registration_form) }}" target="_blank">View Form</a>

                {{-- Add any additional form fields needed for updating the application here --}}

                <form action="{{ route('courses.cancel', $courseApplication->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">Cancel Application</button>
                </form>
            </div>
        </div>
    </div>
@endsection
