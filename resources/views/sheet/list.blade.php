@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">เลือกแผ่นงานที่ต้องการนำเข้าจาก Google Sheet</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            @foreach ($sheetList as $index => $name)
                <div class="col-md-3 mb-4">
                    <a href="{{ route('admin.import.sheet.direct', ['index' => $index]) }}" class="card text-center shadow-sm text-decoration-none text-dark border-primary h-100">
                        <div class="card-body">
                            <h5 class="card-title">แผ่นที่ {{ $index }}</h5>
                            <p class="card-text">{{ $name }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>


    {{-- after your closing </div> but before @endsection: --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.card.text-center').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: 'กำลังดึงข้อมูลจาก Google Sheet',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // give SweetAlert a moment to render…
                setTimeout(() => {
                    window.location = url;
                }, 200);
            });
        });
    </script>
@endsection
