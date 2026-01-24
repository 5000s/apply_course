@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 text-gray-800">จัดการปฏิทิน (Calendar Management)</h1>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">เลือกสถานที่ (Select Location)</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="{{ route('calendar.location', ['location_id' => 1]) }}"
                                class="list-group-item list-group-item-action">
                                Location 1 (กรุงเทพฯ - Bangkok)
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 2]) }}"
                                class="list-group-item list-group-item-action">
                                Location 2 (แก่งคอย - Kaeng Khoi)
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 3]) }}"
                                class="list-group-item list-group-item-action">
                                Location 3 (หาดใหญ่ - Hat Yai)
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 4]) }}"
                                class="list-group-item list-group-item-action">
                                Location 4 (พิษณุโลก - Phitsanulok)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
