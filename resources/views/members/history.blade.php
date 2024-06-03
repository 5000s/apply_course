@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">ประวัติการเข้าคอร์ส</h1>
        <div class="row">
        </div>

        <table class="table">
            <thead>
            <tr>
                <td class="text-center w-[20%]">สถานที่</td>
                <td class="text-center w-[20%]">ชื่อคอร์ส</td>
                <td class="text-center w-[20%]">วันที่เริ่ม</td>
                <td class="text-center w-[20%]">วันที่จบ</td>
                <td class="text-center w-[20%]">วันที่สมัคร</td>
                <td class="text-center w-[20%]">สถานะ</td>
                <td class="text-center w-[20%]">การสมัคร</td>
            </tr>
            </thead>
            <tbody>
            @foreach($apples as $apply)
                <tr>
                    <td class="text-center">{{ $apply->location }}</td>
                    <td class="text-center">{{ $apply->category }}</td>
                    <td class="text-center">{{ $apply->date_start}}</td>
                    <td class="text-center">{{ $apply->date_end }}</td>
                    <td class="text-center">{{ $apply->apply_date }}</td>
                    <td class="text-center">{{ $apply->state }}</td>
                    <td>
                        @if($apply->state === 'เปิดรับสมัคร' && $apply->days_until_start > 0)
                            <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-secondary">แก้ไข</a>
                        @else
                            ปิดรับสมัคร
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
