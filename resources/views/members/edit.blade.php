@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Member</h1>
        <form method="POST" action="{{ route('member.update', $member->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $member->name }}" required>
            </div>

            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" value="{{ $member->surname }}" required>
            </div>

            <!-- Add other fields as necessary -->

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
