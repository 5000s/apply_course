@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>รายการผู้สมัคร</h1>
            <a href="{{ route('member.create') }}" class="btn btn-primary">เพิ่มผู้สมัคร</a>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>ชื่อจริง</th>
                <th>นามสกุล</th>
                <th width="15%">แก้ไขข้อมูล</th>
                <th width="15%">สมัครเข้าคอร์ส</th>
                <th width="15%">ประวัติ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->surname }}</td>
                    <td>
                        <a href="{{ route('member.edit', $member->id) }}" class="btn btn-secondary">แก้ไข</a>
                    </td>
                    <td>
                        <a href="{{ route('courses.index', $member->id) }}" class="btn btn-secondary">สมัคร</a>
                    </td>
                    <td>
                        <a href="{{ route('courses.history', $member->id) }}" class="btn btn-secondary">ประวัติ</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
