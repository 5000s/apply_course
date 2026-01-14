<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .course-table-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course-header {
            background-color: #002060;
            /* Dark Blue */
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            line-height: 1;
        }

        .course-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border-spacing: 0
        }

        .course-item {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #333;
            font-size: .9em;
            border-spacing: 0;
            line-height: 1;
            position: relative;
            /* Added for absolute positioning of the button */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .course-item:nth-child(odd) {
            background-color: #f0f4f8;
            /* Light Blue-Gray */
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .no-course {
            padding: 20px;
            text-align: center;
            color: #666;
        }

        .btn-register {
            position: absolute;
            right: 15px;
            background-color: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .btn-register:hover {
            background-color: #218838;
        }

        .desktop-text {
            display: block;
        }

        .mobile-text {
            display: none;
        }

        @media (max-width: 768px) {
            .desktop-text {
                display: none;
            }

            .mobile-text {
                display: block;
            }
        }
    </style>
</head>

<body>

    <div class="course-table-container">
        <div class="course-header">
            <i class="far fa-calendar-alt"></i>
            @if ($lang == 'th')
                <span> <strong>ตารางคอร์สปฏิบัติ</strong> <br>
                    <small>({{ $category->show_header }})</small>
                </span>
            @else
                <span> <strong>Course Schedule</strong> <br>
                    <small>({{ $category->show_header_en }})</small>
                </span>
            @endif

        </div>

        <ul class="course-list">
            @forelse($courses as $course)
                <li class="course-item">
                    <span class="desktop-text">
                        @if ($lang == 'th')
                            {{ $course->getCourseLongDateTxtAttribute(false) }}
                        @else
                            {{ $course->getCourseLongDateTxtEnAttribute(false) }}
                        @endif
                    </span>
                    <span class="mobile-text">
                        @if ($lang == 'th')
                            {{ $course->getCourseLongDateTxtAttribute(true) }}
                        @else
                            {{ $course->getCourseLongDateTxtEnAttribute(true) }}
                        @endif
                    </span>

                    @if (isset($regis) &&
                            $regis == 1 &&
                            $course->date_start->copy()->addDays(1)->gte(now()->startOfDay()))
                        <a href="{{ route('apply.direct', ['course_id' => $course->id]) }}&lang={{ $lang }}"
                            target="_blank" class="btn-register">
                            @if ($lang == 'th')
                                สมัคร
                            @else
                                Register
                            @endif
                        </a>
                    @endif
                </li>
            @empty
                <li class="no-course">ไม่มีรอบคอร์สที่เปิดรับสมัครในขณะนี้</li>
            @endforelse
        </ul>
    </div>

    <script>
        function sendHeight() {
            var height = document.body.scrollHeight;
            console.log('[Iframe Debug] Sending height:', height, 'for elementId:', @json($id));
            // เปลี่ยน URL ตรงนี้เป็นโดเมนหน้า parent ของคุณ
            window.parent.postMessage({
                    type: 'setHeight',
                    height: height,
                    elementId: @json($id)
                },
                '*'
            );
        }

        window.addEventListener('load', sendHeight);
        window.addEventListener('resize', sendHeight);
    </script>

</body>

</html>
