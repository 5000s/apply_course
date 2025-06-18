@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary fw-bold mb-4">Team Members - {{ $team->name }}</h1>
        <a href="{{ route('teammembers.create', $team->id) }}" class="btn btn-outline-success mb-3">Add Member</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Member</th>
                <th>Position</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($teamMembers as $member)
                <tr>
                    <td>{{ $member->member->name }} {{ $member->member->surname }}</td>
                    <td>{{ $member->position }}</td>
                    <td>{{ \Carbon\Carbon::parse($member->join_at)->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('teammembers.edit', [$team->id, $member->id]) }}"
                           class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('teammembers.destroy', [$team->id, $member->id]) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Remove member?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
