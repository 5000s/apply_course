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
        }

        .course-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .course-item {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #333;
            font-size: 1rem;
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
                    @if ($lang == 'th')
                        {{ $course->course_long_date_txt }}
                    @else
                        {{ $course->course_long_date_txt_en }}
                    @endif
                </li>
            @empty
                <li class="no-course">ไม่มีรอบคอร์สที่เปิดรับสมัครในขณะนี้</li>
            @endforelse
        </ul>
    </div>

</body>

</html>
