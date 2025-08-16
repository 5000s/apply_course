@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <div class="card" style="font-size: 18px;">
                    <div class="card-header">
                        <h2>{{ __('messages.search_result') }}</h2>
                    </div>

                    <div class="card-body" style="font-size: 18px;">

                        @if($most_matched_member)
                            <p>{{ __('messages.found_member') }}</p>
                            <form method="POST" action="{{ route('request-password-reset') }}">
                                @csrf
                                <input type="hidden" name="member_id" value="{{ $most_matched_member['id'] }}">
                                <p>{{ $most_matched_member['email'] }}</p>
                                <button type="submit" class="btn btn-primary mt-3">
                                    {{ __('messages.send_request') }}
                                </button>
                            </form>
                        @else
                            <p>{{ __('messages.not_found') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
