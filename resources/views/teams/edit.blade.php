@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary fw-bold my-4">Edit Team</h1>
        <form action="{{ route('teams.update', $team->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Team Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $team->name }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ $team->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="leader_id" class="form-label">Leader</label>
                <select class="form-control" name="leader_id" id="leader_id">
                    <option value="">-- None --</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ $team->leader_id == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} {{ $member->surname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
