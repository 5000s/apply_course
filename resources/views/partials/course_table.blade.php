<div class="card shadow-lg border-0" >
    <div class="card-header bg-primary text-white text-center fw-bold py-3">
        <h4 class="mb-0">
            {{ app()->getLocale() === 'en' ? $loc->show_name_en : $loc->show_name }}
        </h4>
    </div>
    <div class="card-body">
        @if ($courses->isEmpty())
            <p class="text-center text-muted">
                <i class="fas fa-info-circle"></i>
                {{ __('messages.no_courses', [
                     'location' => app()->getLocale() === 'en' ? $loc->show_name_en : $loc->show_name
                 ]) }}
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
                        <td>
                            {{ app()->getLocale() === 'en' ? $course->date_range_en : $course->date_range }}
                        </td>
                        <td class="text-primary">
                            {{ app()->getLocale() === 'en' ? $course->name_en : $course->name }}
                        </td>
                        <td>
                            @php
                                  $state =  app()->getLocale() === 'en' ? $course->state_en : $course->state;
                            @endphp
                            @if ($course->state == 'เปิดรับสมัคร')
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> {{ $state }}</span>
                            @elseif ($course->state == 'เต็มแล้ว')
                                <span class="badge bg-danger"><i class="fas fa-times-circle"></i> {{ $state }}</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> {{ $state }}</span>
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
