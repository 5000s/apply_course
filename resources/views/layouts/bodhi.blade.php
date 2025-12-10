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
    <footer class="py-5 mt-5" style="background-color: #ffffff; border-top: 1px solid #e0e0e0;">
        <div class="container">
            <div class="row justify-content-center align-items-start g-4">
                {{-- Logos Column --}}
                <div class="col-md-auto d-flex flex-column align-items-center gap-3">
                    <a href="https://www.knowingbuddha.org/" target="_blank">
                        <img src="https://bodhidhammayan.org/wp-content/uploads/2025/01/Knowing-Buddha-LOGO.avif"
                            alt="Knowing Buddha" style="width: 100px; height: auto;">
                    </a>
                    <a href="https://www.schooloflifethailand.org/" target="_blank">
                        <img src="https://bodhidhammayan.org/wp-content/uploads/2025/02/Logo-SchoolOfLife-1024x1024.webp"
                            alt="School of Life" style="width: 120px; height: auto;">
                    </a>
                </div>

                {{-- Contact Info Column --}}
                <div class="col-md-auto">
                    <h5 class="fw-bold mb-3" style="color: var(--bodhi-brown);">ดำเนินงานโดยมูลนิธิ โนอิ้ง บุดด้าฯ
                        และมูลนิธิโรงเรียนแห่งชีวิต</h5>

                    <ul class="list-unstyled text-muted" style="font-size: 0.95rem;">
                        <li class="mb-2 d-flex">
                            <span class="me-2 text-center" style="width: 20px; color: #444444;">
                                <svg aria-hidden="true" style="height: 1em;" viewBox="0 0 288 512"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path
                                        d="M112 316.94v156.69l22.02 33.02c4.75 7.12 15.22 7.12 19.97 0L176 473.63V316.94c-10.39 1.92-21.06 3.06-32 3.06s-21.61-1.14-32-3.06zM144 0C64.47 0 0 64.47 0 144s64.47 144 144 144 144-64.47 144-144S223.53 0 144 0zm0 76c-37.5 0-68 30.5-68 68 0 6.62-5.38 12-12 12s-12-5.38-12-12c0-50.73 41.28-92 92-92 6.62 0 12 5.38 12 12s-5.38 12-12 12z">
                                    </path>
                                </svg>
                            </span>
                            <a href="https://maps.app.goo.gl/2Ej1RJkgboYDaD576" target="_blank"
                                class="text-decoration-none text-muted">
                                462/12 ซอยอ่อนนุช 8 ถนนสุขุมวิท 77 แขวงอ่อนนุช เขตสวนหลวง กรุงเทพมหานคร 10250
                            </a>
                        </li>
                        <li class="mb-2 d-flex">
                            <span class="me-2 text-center" style="width: 20px; color: #444444;">
                                <svg aria-hidden="true" style="height: 1em;" viewBox="0 0 512 512"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path
                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z">
                                    </path>
                                </svg>
                            </span>
                            <span>จันทร์ - ศุกร์ เวลา 9.00 - 17.00 น.</span>
                        </li>
                        <li class="mb-2 d-flex">
                            <span class="me-2 text-center" style="width: 20px; color: #444444;">
                                <svg aria-hidden="true" style="height: 1em;" viewBox="0 0 512 512"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path
                                        d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z">
                                    </path>
                                </svg>
                            </span>
                            <span>โทร 02 117 4063 , 02 117 4064</span>
                        </li>
                        <li class="mb-2 d-flex">
                            <span class="me-2 text-center" style="width: 20px; color: #444444;">
                                <svg aria-hidden="true" style="height: 1em;" viewBox="0 0 512 512"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path
                                        d="M480 160V77.25a32 32 0 0 0-9.38-22.63L425.37 9.37A32 32 0 0 0 402.75 0H160a32 32 0 0 0-32 32v448a32 32 0 0 0 32 32h320a32 32 0 0 0 32-32V192a32 32 0 0 0-32-32zM288 432a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm0-128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm128 128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm0-128a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16v-32a16 16 0 0 1 16-16h32a16 16 0 0 1 16 16zm0-112H192V64h160v48a16 16 0 0 0 16 16h48zM64 128H32a32 32 0 0 0-32 32v320a32 32 0 0 0 32 32h32a32 32 0 0 0 32-32V160a32 32 0 0 0-32-32z">
                                    </path>
                                </svg>
                            </span>
                            <span>แฟกซ์ 02 117 4065</span>
                        </li>
                        <li class="mb-2 d-flex">
                            <span class="me-2 text-center" style="width: 20px; color: #444444;">
                                <svg aria-hidden="true" style="height: 1em;" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path
                                        d="M160 448c-25.6 0-51.2-22.4-64-32-64-44.8-83.2-60.8-96-70.4V480c0 17.67 14.33 32 32 32h256c17.67 0 32-14.33 32-32V345.6c-12.8 9.6-32 25.6-96 70.4-12.8 9.6-38.4 32-64 32zm128-192H32c-17.67 0-32 14.33-32 32v16c25.6 19.2 22.4 19.2 115.2 86.4 9.6 6.4 28.8 25.6 44.8 25.6s35.2-19.2 44.8-22.4c92.8-67.2 89.6-67.2 115.2-86.4V288c0-17.67-14.33-32-32-32zm256-96H224c-17.67 0-32 14.33-32 32v32h96c33.21 0 60.59 25.42 63.71 57.82l.29-.22V416h192c17.67 0 32-14.33 32-32V192c0-17.67-14.33-32-32-32zm-32 128h-64v-64h64v64zm-352-96c0-35.29 28.71-64 64-64h224V32c0-17.67-14.33-32-32-32H96C78.33 0 64 14.33 64 32v192h96v-32z">
                                    </path>
                                </svg>
                            </span>
                            <span>admin@bodhidhammayan.org</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
