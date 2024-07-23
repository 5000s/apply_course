@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ __('messages.applicant_list') }}</h1>
            <a href="{{ route('member.create') }}" class="btn btn-primary">{{ __('messages.add_applicant') }}</a>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>{{ __('messages.first_name') }}</th>
                <th>{{ __('messages.surname') }}</th>
                <th width="15%">{{ __('messages.edit_info') }}</th>
                <th width="15%">{{ __('messages.register_course') }}</th>
                <th width="15%">{{ __('messages.history') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->surname }}</td>
                    <td>
                        <a href="{{ route('member.edit', $member->id) }}" class="btn btn-secondary">{{ __('messages.edit') }}</a>
                    </td>
                    <td>
                        <a href="{{ route('courses.index', $member->id) }}" class="btn btn-secondary">{{ __('messages.register') }}</a>
                    </td>
                    <td>
                        <a href="{{ route('courses.history', $member->id) }}" class="btn btn-secondary">{{ __('messages.history') }}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
