<div class="card shadow-lg border-0" >
    <div class="card-header bg-primary text-white text-center fw-bold py-3">
        <h4 class="mb-0">{{ $location->show_name }}</h4>
    </div>
    <div class="card-body">
        @if ($courses->isEmpty())
            <p class="text-center text-muted">
                <i class="fas fa-info-circle"></i> ไม่มีคอร์สเปิดที่ {{ $location->show_name }} ในขณะนี้
            </p>
        @else
            <table class="table table-hover text-center">
                <thead class="table-light">
                <tr>
{{--                    <th style="width: 15%;">{{ __('messages.course_month') }}</th>--}}
                    <th style="width: 30%;">{{ __('messages.course_date') }}</th>
                    <th style="width: 40%;">{{ __('messages.course_name') }}</th>
                    <th style="width: 15%;">{{ __('messages.status') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($courses as $course)
                    <tr class="align-middle">
{{--                        <td class="fw-bold">{{ $course->month_year }}</td>--}}
                        <td>{{ $course->date_range }}</td>
                        <td class="text-primary">{{ $course->name }}</td>
                        <td>
                            @if ($course->state == 'เปิดรับสมัคร')
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> {{ $course->state }}</span>
                            @elseif ($course->state == 'เต็มแล้ว')
                                <span class="badge bg-danger"><i class="fas fa-times-circle"></i> {{ $course->state }}</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> {{ $course->state }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<style>

    .card {
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header {
        font-size: 1.2rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .table th {
        font-weight: bold;
        color: #444;
    }

    .badge {
        font-size: 0.9rem;
        padding: 5px 10px;
        border-radius: 12px;
    }

</style>
