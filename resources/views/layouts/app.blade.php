<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <!-- โลโก้ -->
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('images/Bhodhi-Dhammayan-LOGO-TH.png') }}" alt="Logo" height="70">
{{--                <span class="ms-2 fw-bold text-danger">โพธิธรรมญาณสถาน</span>--}}
            </a>

            <!-- ปุ่มเมนู (สำหรับมือถือ) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-primary" href="{{ route('login') }}">
                                {{ __('messages.Login') }}
                            </a>
                        </li>

                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile') }}">{{ __('messages.profile') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('messages.Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest

                    <!-- ปุ่มตารางคอร์สปฏิบัติ -->
                    <li class="nav-item ms-2">
                        <a class="btn btn-danger text-white fw-bold px-3 shadow" href="{{ route('showCourseForStudent') }}">
                            <i class="fas fa-calendar-alt"></i>   {{ __('messages.course_list') }}
                        </a>
                    </li>

                        @if(Auth::check() && Auth::user()->admin == 1)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.courses') }}">จัดการคอร์ส</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.members') }}">จัดการสมาชิก</a>
                                </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teams.index') }}">จัดการทีม</a>
                            </li>
                        @endif

                    <!-- สลับภาษา -->
                        <li class="nav-item dropdown ms-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset(app()->getLocale() == 'th' ? 'images/th.svg' : 'images/eng.svg') }}"
                                     alt="Language" width="24" class="rounded-circle">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('lang.switch', 'en') }}">
                                        <img src="{{ asset('images/eng.svg') }}" alt="English" width="24" class="me-2"> English
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('lang.switch', 'th') }}">
                                        <img src="{{ asset('images/th.svg') }}" alt="Thai" width="24" class="me-2"> ไทย
                                    </a>
                                </li>
                            </ul>
                        </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
