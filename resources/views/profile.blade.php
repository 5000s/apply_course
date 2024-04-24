@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Members List</h1>
        <a href="{{ route('member.create') }}" class="btn btn-primary">Add New Member</a>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Surname</th>
                <th>Actions</th>
                <th>Apply Course</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->surname }}</td>
                    <td>
                        <a href="{{ route('member.edit', $member->id) }}" class="btn btn-secondary">Edit</a>
                    </td>
                    <td>
                        <a href="{{ route('courses.index', $member->id) }}" class="btn btn-secondary">Apply</a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
