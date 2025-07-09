@php
    use Carbon\Carbon;
    $now = Carbon::now();
@endphp

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
                        <th style="width: 30%;">{{ __('messages.course_date') }}</th>
                        <th style="width: 30%;">{{ __('messages.course_name') }}</th>
                        <th style="width: 15%;">{{ __('messages.status') }}</th>
                        <th style="width: 10%;">{{ __('messages.register') }}</th>
                        @if($user->admin == 1)
                            <th style="width: 10%;"> Admin สมัคร </th>
                            <th style="width: 10%;"> Admin รายการ</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($courses as $course)
                        @php
                            $start = Carbon::parse($course->date_start);
                            $daysToStart = $now->diffInDays($start, false);
                            $isEarlyClose = $start->gt($now) && $daysToStart <= 30;
                        @endphp
                        <tr class="align-middle text-center">
                            <td>{{ $course->date_range }}</td>
                            <td>{{ $course->name }}</td>
                            <td>
                                @if($isEarlyClose)
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-lock"></i>
                                        ปิดรับสมัครก่อนเปิดคอร์ส 30 วัน
                                    </span>
                                @elseif($course->state === 'เปิดรับสมัคร')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $course->state }}
                                    </span>
                                @elseif($course->state === 'ปิดรับสมัคร')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i>
                                        {{ $course->state }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock"></i>
                                        {{ $course->state }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{-- ปุ่มสมัครจะแสดงเฉพาะเมื่อเปิดรับสมัครและยังไม่อยู่ในช่วงปิดก่อนเปิด 30 วัน --}}
                                @if(!$isEarlyClose
                                    && $course->state === 'เปิดรับสมัคร'
                                    && in_array($course->category_id, $allow_types, true))

                                    @if(is_null($course->apply_id))
                                        <a href="{{ route('courses.show', [$member_id, $course->id]) }}"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-sign-in-alt"></i>
                                            {{ __('messages.register') }}
                                        </a>
                                    @else
                                        <a href="{{ route('courses.show', [$member_id, $course->id]) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                            {{ __('messages.edit') }}
                                        </a>
                                    @endif
                                @endif

                            </td>

                            @if($user->admin == 1)
                                <td>
                                    <a href="{{ route('courses.show', [$member_id, $course->id]) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-sign-in-alt"></i>
                                        {{ __('messages.register') }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.courseList', [$course->id]) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                            รายการ
                                    </a>
                                </td>
                            @endif


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
