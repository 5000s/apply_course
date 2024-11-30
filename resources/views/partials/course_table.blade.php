<div class="d-flex justify-content-between align-items-center my-4">
    <h3 class="text-center my-4">{{ $location->show_name }}</h3>
</div>
@if ($courses->isEmpty())
    <p class="text-center text-muted">ไม่มีคอร์สเปิดที่ {{ $location->show_name }} ในขณะนี้</p>
@else
    <table class="table">
        <thead>
        <tr>
            <th style="width: 15%;" class="text-center">{{ __('messages.course_month') }}</th>
            <th style="width: 30%;" class="text-center">{{ __('messages.course_date') }}</th>
            <th style="width: 40%;" class="text-center">{{ __('messages.course_name') }}</th>
            <th style="width: 15%;" class="text-center">{{ __('messages.status') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($courses as $course)
            <tr>
                <td>{{ $course->month_year }}</td>
                <td>{{ $course->date_range }}</td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->state }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
