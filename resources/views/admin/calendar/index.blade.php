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
                            <a href="{{ route('calendar.location', ['location_id' => 4]) }}"
                                class="list-group-item list-group-item-action">
                                1 ห้องภาวนามูลนิธิฯ อ่อนนุช กรุงเทพฯ
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 2]) }}"
                                class="list-group-item list-group-item-action">
                                2 โพธิธรรมญาณสถาน อ. แก่งคอย จ. สระบุรี
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 3]) }}"
                                class="list-group-item list-group-item-action">
                                3 แสงธรรมโพธิญาณ จ. หาดใหญ่
                            </a>
                            <a href="{{ route('calendar.location', ['location_id' => 5]) }}"
                                class="list-group-item list-group-item-action">
                                4 โพธิธรรมญาณสถาน จ.ภูเก็ต
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
