@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary fw-bold mb-4">Edit Member</h1>

        <form action="{{ route('teammembers.update', [$team->id, $member->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Member</label>
                <input class="form-control" value="{{ $member->member->name }} {{ $member->member->surname }}" disabled>
            </div>

            <div class="mb-3">
                <label for="position" class="form-label">Position</label>
                <input type="text" name="position" id="position" class="form-control" value="{{ $member->position }}">
            </div>


            <div class="mb-3">
                <label for="join_at" class="form-label">Join At</label>
                <input type="date" name="join_at" id="join_at" class="form-control" value="{{ optional($member->join_at)->format('Y-m-d') }}">
            </div>

            <div class="mb-3">
                <label for="leave_at" class="form-label">Leave At</label>
                <input type="date" name="leave_at" id="leave_at" class="form-control" value="{{ optional($member->leave_at)->format('Y-m-d') }}">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('teammembers.index', [$team->id, $member->id]) }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
