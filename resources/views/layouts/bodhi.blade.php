<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Bodhidhammayan')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ฟอนต์ไทยอ่านง่าย (Sarabun) --}}
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* โทนใกล้เคียงเว็บต้นฉบับ: สงบ เรียบ อบอุ่น */
            --bodhi-bg: #ffffff;
            --bodhi-text: #1d1f21;
            --bodhi-mute: #6c757d;
            --bodhi-gold: #c9a750;
            /* ไฮไลท์ทอง */
            --bodhi-gold-2: #b89545;
            --bodhi-brown: #5a4632;
            /* น้ำตาลอ่อน */
            --bodhi-green: #244b3c;
            /* เขียวเข้มสุภาพ */
            --bodhi-soft: #f7f5f0;
            /* พื้นหลังอ่อน */
        }

        html,
        body {
            background: var(--bodhi-bg);
            color: var(--bodhi-text);
            font-family: "Sarabun", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans Thai", sans-serif;
        }

        /* Navbar */
        .bodhi-navbar {
            border-bottom: 1px solid #eee;
            background: #fff8f0 linear-gradient(180deg, #fffdf8 0%, #ffffff 100%);
        }

        .bodhi-brand {
            font-weight: 700;
            letter-spacing: .5px;
            color: var(--bodhi-brown) !important;
        }

        .bodhi-nav a {
            color: var(--bodhi-brown);
            font-weight: 600;
        }

        .bodhi-nav a:hover {
            color: var(--bodhi-gold);
        }

        /* Card */
        .card {
            border-radius: 1rem;
            border-color: #eee;
        }

        .card-header {
            border-bottom-color: #eee;
        }

        /* ปุ่มธีม */
        .btn-bodhi {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--bodhi-gold);
            --bs-btn-border-color: var(--bodhi-gold);
            --bs-btn-hover-bg: var(--bodhi-gold-2);
            --bs-btn-hover-border-color: var(--bodhi-gold-2);
            --bs-btn-focus-shadow-rgb: 201, 167, 80;
        }

        .btn-outline-bodhi {
            --bs-btn-color: var(--bodhi-brown);
            --bs-btn-border-color: var(--bodhi-gold);
            --bs-btn-hover-bg: var(--bodhi-gold);
            --bs-btn-hover-border-color: var(--bodhi-gold);
            --bs-btn-hover-color: #fff;
        }

        h1.bodhi-hline,
        h2.bodhi-hline,
        h3.bodhi-hline,
        h4.bodhi-hline,
        h5.bodhi-hline {
            position: relative;
            padding-left: 1rem;
        }

        /* ฟอร์ม */
        .form-label {
            font-weight: 600;
            color: var(--bodhi-brown);
        }

        .form-text {
            color: var(--bodhi-mute);
        }

        /* Footer */
        .bodhi-footer {
            color: #7a7168;
            font-size: .95rem;
        }

        /* รูปทั่วไปในหน้าเรียน */
        .course-cover {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
            border-radius: .75rem;
        }

        /* Hero รูปสถานที่ด้านบน (ใช้ในหน้าสมัคร) */
        .hero-image-wrap {
            width: 100%;
            height: 240px;
            overflow: hidden;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        @media (min-width:992px) {
            .hero-image-wrap {
                height: 300px;
            }
        }
    </style>

    @stack('head')
</head>

<body>
    {{-- Navbar --}}
    {{-- <nav class="navbar navbar-expand-lg bodhi-navbar py-2"> --}}
    {{-- <div class="container">
            <a class="navbar-brand bodhi-brand" href="{{ url('/') }}">โพธิธรรมญาณสถาน</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button> --}}
    {{-- <div id="navMain" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto bodhi-nav">
                <li class="nav-item"><a class="nav-link" href="{{ url('/courses') }}">ตารางคอร์ส</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/stories') }}">ประสบการณ์</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/teachings') }}">ธรรมคำสอน</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-sm btn-bodhi" href="{{ route('apply.direct',['course_id'=>request('course_id')]) }}">สมัครคอร์ส</a>
                </li>
            </ul>
        </div> --}}
    {{-- </div> --}}
    {{-- </nav> --}}

    {{-- Content --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    {{-- <footer class="py-4 mt-5">
        <div class="container text-center bodhi-footer">
            © {{ date('Y') }} โพธิธรรมญาณสถาน
        </div>
    </footer> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
