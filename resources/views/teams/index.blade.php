@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center text-primary fw-bold">Team List</h1>
            <a href="{{ route('teams.create') }}" class="btn btn-outline-success">
                <i class="fas fa-plus"></i> Add New Team
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Leader</th>
                <th>Description</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($teams as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->leader?->name }} {{ $team->leader?->surname }}</td>
                    <td>{{ $team->description }}</td>
                    <td>
                        <a href="{{ route('teammembers.index', $team->id) }}" class="btn btn-outline-info btn-sm">Members</a>
                    </td>
                    <td>
                        <a href="{{ route('teams.edit', $team->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('teams.destroy', $team->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this team?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection


@push('styles')
    <style>
        .text-primary { color: #0d6efd; }
        .fw-bold { font-weight: bold; }
    </style>
@endpush
