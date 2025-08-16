@extends('layouts.app')

@section('content')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="text-left my-4">
                {{ __('messages.course_history') }}: {{$user->name}} {{$user->surname}}
            </h4>

            @if($user->admin == 1)
                <a href="javascript:history.back()" id="back-button" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @else
                <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
            @endif
        </div>

        @php $isEn = app()->getLocale() === 'en'; @endphp

        <div class="card shadow-sm p-3">
            <div class="table-responsive">
                <table id="coursesTable" class="table table-striped table-hover">
                    <thead class="table-light border-bottom">
                    <tr class="text-secondary">
                        <th class="text-center">{{ __('messages.location') }}</th>
                        <th class="text-center">{{ __('messages.course_name') }}</th>
                        <th class="text-center">{{ __('messages.course_date') }}</th>
                        <th class="text-center">{{ __('messages.application') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($applies as $apply)
                        @php
                            // Map Thai states from DB → normalized keys (for translation + color)
                            $stateMap = [
                                'ผ่านการอบรม' => 'passed',
                                'ยื่นใบสมัคร' => 'submitted',
                                'ยุติกลางคัน' => 'abandoned',
                                'ยกเลิกการสมัคร' => 'cancelled', // sometimes you use this text explicitly
                            ];
                            // If cancelled flag set in DB, override
                            $stateKey = ($apply->cancel == 1)
                                ? 'cancelled'
                                : ($stateMap[$apply->state] ?? 'other');

                            // badge styles per normalized key
                            $badge = [
                                'passed'    => ['class' => 'bg-success',    'icon' => 'fas fa-check-circle'],
                                'submitted' => ['class' => 'bg-info',       'icon' => 'fas fa-file-upload'],
                                'abandoned' => ['class' => 'bg-danger',     'icon' => 'fas fa-times-circle'],
                                'cancelled' => ['class' => 'bg-secondary',  'icon' => 'fas fa-ban'],
                                'other'     => ['class' => 'bg-warning text-dark', 'icon' => 'fas fa-clock'],
                            ][$stateKey];
                        @endphp
                        <tr>
                            {{-- If you have location_en/category_en/date_range_en in $apply, use them; otherwise fallback --}}
                            <td class="text-center">
                                {{ $isEn ? $apply->location_name_en : $apply->location_name }}
                            </td>
                            <td class="text-center">
                                {{ $isEn ? $apply->course_name_en : $apply->course_name }}
                            </td>
                            <td class="text-center">
                                {{ $isEn ? ($apply->date_range_en ?? $apply->date_range) : $apply->date_range }}
                            </td>

                            <td class="text-center">
                                <span class="badge {{ $badge['class'] }}">
                                    <i class="{{ $badge['icon'] }}"></i>
                                    {{ __('apply_history.state.' . $stateKey) }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($apply->state === 'เปิดรับสมัคร' && $apply->days_until_start > 0)
                                    <a href="{{ route('courses.show', [$member_id, $apply->id]) }}" class="btn btn-primary btn-sm">
                                        {{ __('messages.edit') }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ __('messages.closed') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DataTables language: use Laravel translations --}}
    <script>
        $(document).ready(function() {
            const dtLang = {
                search: @json(__('datatable.search')),
                lengthMenu: @json(__('datatable.lengthMenu')),
                zeroRecords: @json(__('datatable.zeroRecords')),
                info: @json(__('datatable.info')),
                infoEmpty: @json(__('datatable.infoEmpty')),
                infoFiltered: @json(__('datatable.infoFiltered')),
                paginate: {
                    first: @json(__('datatable.paginate.first')),
                    last: @json(__('datatable.paginate.last')),
                    next: @json(__('datatable.paginate.next')),
                    previous: @json(__('datatable.paginate.previous')),
                }
            };

            var table = $('#coursesTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                language: dtLang,
            });

            $('#statusFilter').on('change', function () {
                var selectedStatus = $(this).val();
                table.column(4).search(selectedStatus).draw();
            });
        });
    </script>
@endsection
