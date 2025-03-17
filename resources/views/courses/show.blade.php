@extends('layouts.app')

@section('content')

    <div class="container">
{{--        <h1 class="text-center mt-4 mb-3">{{ $course->category }}, {{ $course->location }}</h1>--}}
        <h4 class="text-center mb-4">{{ $course->coursename }}, {{ $course->location }}</h4>

        {{-- Alert Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body py-4 px-4">

                {{-- 📌 ข้อมูลผู้สมัคร --}}
                <h4 class="text-primary fw-bold mb-3">การสมัครสำหรับคุณ: {{ $member->name }} {{ $member->surname }}</h4>

                {{-- 📌 ใบสมัครแต่ละประเภท --}}
                @if(in_array($course->category_id, [1, 2, 3, 4, 6, 8, 9, 10, 12]))
                    <div class="d-inline-flex flex-wrap align-items-center gap-3 mb-4">
                        <strong class="me-2">ท่านสามารถรับใบสมัครได้ที่นี่:</strong>
                        @php
                            $isEnglish = preg_match('/[a-zA-Z]/', $member->name);
                        @endphp
                        @if($member->buddism == "ภิกษุ")
                            <a href="{{ asset('form/Application-Vipassana_monk.pdf') }}" target="_blank" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-pdf"></i> ใบสมัคร สำหรับพระสงฆ์
                            </a>
                        @elseif($isEnglish)
                            <a href="{{ asset('form/Application-Vipassana_en.pdf') }}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-pdf"></i> English Form
                            </a>
                        @else
                            <a href="{{ asset('form/Application-Vipassana.pdf') }}" target="_blank" class="btn btn-success btn-sm">
                                <i class="fas fa-file-pdf"></i> ใบสมัคร ภาษาไทย
                            </a>
                        @endif
                    </div>
                @elseif(in_array($course->category_id, [5, 9, 11, 13, 14]))
                    <div class="d-inline-flex flex-wrap align-items-center gap-3 mb-4">
                        <strong class="me-2">ท่านสามารถรับใบสมัครได้ที่นี่:</strong>
                        @php
                            $isEnglish = preg_match('/[a-zA-Z]/', $member->name);
                        @endphp
                        @if($member->buddism == "ภิกษุ")
                            <a href="{{ asset('form/Application-Anapanasati_monk.pdf') }}" target="_blank" class="btn btn-warning btn-sm">
                                <i class="fas fa-file-pdf"></i> ใบสมัคร สำหรับพระสงฆ์
                            </a>
                        @elseif($isEnglish)
                            <a href="{{ asset('form/Application-Anapanasati_en.pdf') }}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-pdf"></i> English Form
                            </a>
                        @else
                            <a href="{{ asset('form/Application-Anapanasati.pdf') }}" target="_blank" class="btn btn-success btn-sm">
                                <i class="fas fa-file-pdf"></i> ใบสมัคร ภาษาไทย
                            </a>
                        @endif
                    </div>
                @endif

                {{-- 📌 ฟอร์มอัปโหลดใบสมัคร --}}
                <form action="{{ route('courses.save', $member_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    @if($apply->application == "")
                        <div class="mb-4">
                            <label for="registration_form" class="form-label">
                                กรุณาอัปโหลดแบบฟอร์มการสมัคร (ไฟล์รูปภาพ หรือ PDF)
                            </label>
                            <input type="file" class="form-control"
                                   name="registration_form" required onchange="previewFile()"
                                   accept="image/png, image/jpeg, application/pdf">
                        </div>
                    @else
                        <input type="hidden" name="cancel" value="cancel">
                    @endif

                    {{-- 📌 แสดงตัวอย่างไฟล์ที่อัปโหลด --}}
                    <div id="preview" class="mt-3">
                        @if($apply->application != "")
                            @php
                                $extension = strtolower(pathinfo($apply->application, PATHINFO_EXTENSION));
                            @endphp

                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset('storage/' . $apply->application) }}" alt="Uploaded Image" class="img-fluid">
                            @elseif($extension == 'pdf')
                                <object data="{{ asset('storage/' . $apply->application) }}" type="application/pdf" width="100%" height="600px">
                                    <p>เบราว์เซอร์ของคุณไม่รองรับการแสดง PDF <a href="{{ asset('storage/' . $apply->application) }}">ดาวน์โหลดที่นี่</a>.</p>
                                </object>
                            @endif
                        @endif
                    </div>

                    {{-- 📌 ปุ่มสมัคร / ยกเลิกการสมัคร --}}
                    <div class="text-end mt-4">
                        @if($apply->application != "" && $apply->application != null )
                            <button id="submit_button" type="submit" class="btn btn-danger btn-lg">ยกเลิกการสมัคร</button>
                        @else
                            <button id="submit_button" type="submit" class="btn btn-primary btn-lg" disabled>สมัคร</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewFile() {
            const preview = document.getElementById('preview');
            const fileInput = document.querySelector('input[type=file]');
            const file = fileInput.files[0];
            const reader = new FileReader();
            const submitButton = document.getElementById('submit_button');

            if (file) {
                const fileSizeMB = file.size / (1024 * 1024); // แปลงขนาดเป็น MB

                if (fileSizeMB > 2) {
                    alert("ไฟล์ที่อัปโหลดต้องมีขนาดไม่เกิน 2MB");
                    fileInput.value = ""; // รีเซ็ตค่าไฟล์
                    preview.innerHTML = "";
                    submitButton.disabled = true;
                    return;
                }

                const fileType = file.type;
                preview.innerHTML = "";

                if (fileType.startsWith("image/")) {
                    reader.onload = function(event) {
                        preview.innerHTML = `<img src="${event.target.result}" alt="Image preview" class="img-fluid">`;
                        submitButton.disabled = false;
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === "application/pdf") {
                    preview.innerHTML = `<object data="${URL.createObjectURL(file)}" type="application/pdf" width="100%" height="600px"></object>`;
                    submitButton.disabled = false;
                } else {
                    alert("ไฟล์ที่รองรับ: PNG, JPG, JPEG, PDF");
                    fileInput.value = "";
                    preview.innerHTML = "";
                    submitButton.disabled = true;
                }
            } else {
                preview.innerHTML = "";
                submitButton.disabled = true;
            }
        }
    </script>


@endsection
