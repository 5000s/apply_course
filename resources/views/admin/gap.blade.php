@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h2>Gap Before “{{ $course->name }}” (Y – M – D)</h2>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>uid</th>
                <th>Member</th>
                <th>Last 7-Day Course Date</th>
                <th>Gap (Y – M – D)</th>
            </tr>
            </thead>
            <tbody>
            @forelse($results as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['member']->id }}</td>
                    <td>{{ $row['member']->name }}</td>
                    <td>
                        {{ $row['last_date']
                            ? \Carbon\Carbon::parse($row['last_date'])->format('Y-m-d')
                            : '—' }}
                    </td>
                    <td>
                        @if(!is_null($row['years']))
                            {{ $row['years'] }} – {{ $row['months'] }} – {{ $row['days'] }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No applicants found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
