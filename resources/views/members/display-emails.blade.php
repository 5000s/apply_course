@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col text-center">
                <h2>ผลการค้นหา</h2>
                @if($most_matched_member)
                    <p>เราพบข้อมูลที่ตรงกับการค้นหาของคุณ กรุณาส่งคำขอเพื่อสร้างรหัสผ่านใหม่:</p>
                    <form method="POST" action="{{ route('request-password-reset') }}">
                        @csrf
                        <input type="hidden" name="member_id" value="{{ $most_matched_member['id'] }}">
                        <p>{{ $most_matched_member['email'] }}</p>
                        <button type="submit" class="btn btn-primary mt-3">ส่งคำขอ</button>
                    </form>
                @else
                    <p>ไม่พบข้อมูลที่ตรงกับการค้นหาของคุณ</p>
                @endif
            </div>
        </div>
    </div>
@endsection
