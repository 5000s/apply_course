<div class="container">
    <div class="text-center my-4">
        <h3 class="fw-bold text-primary">{{ $location->show_name }}</h3>
    </div>

    @if ($courses->isEmpty())
        <div class="alert alert-warning text-center" role="alert">
            <i class="fas fa-exclamation-circle"></i> ไม่มีคอร์สเปิดที่ {{ $location->show_name }} ในขณะนี้
        </div>
    @else
        <div class="card shadow-sm p-3 border-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                    <tr class="text-center">
                        <th style="width: 15%;">{{ __('messages.course_month') }}</th>
                        <th style="width: 30%;">{{ __('messages.course_date') }}</th>
                        <th style="width: 30%;">{{ __('messages.course_name') }}</th>
                        <th style="width: 15%;">{{ __('messages.status') }}</th>
                        <th style="width: 10%;">{{ __('messages.register') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($courses as $course)
                        <tr class="align-middle">
                            <td class="text-center">{{ $course->month_year }}</td>
                            <td class="text-center">{{ $course->date_range }}</td>
                            <td class="text-center">{{ $course->name }}</td>
                            <td class="text-center">
                                    <span class="badge
                                        @if($course->state === 'เปิดรับสมัคร') bg-success
                                        @elseif($course->state === 'ปิดรับสมัคร') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $course->state }}
                                    </span>
                            </td>
                            <td class="text-center">
                                @if($course->state === 'เปิดรับสมัคร')
                                    @if(is_null($course->apply_id))
                                        <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-sign-in-alt"></i> {{ __('messages.register') }}
                                        </a>
                                    @else
                                        <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                    @endif
                                @else
{{--                                    <span class="text-muted" style="font-size: 14px;">{{ __('messages.closed') }}</span>--}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-primary {
            color: #0d6efd;
        }
    </style>
@endpush
