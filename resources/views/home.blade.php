@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">ระบบสมัครคอร์สเตโชวิปัสสนา 2567</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    โปรดลงชื่อเข้าใช้งาน
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
