@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

                {{-- ======================== ฟอร์ม 1 : สมัคร / แก้ไข  ======================== --}}
                <form id="application_form"
                      action="{{ $apply->id == null ? route('courses.save', $member_id)
                                    : route('courses.update', [$member_id, 'apply' => $apply->id]) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf


                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    @if($course->location_id == 1)

                        {{-- 📌 การเดินทาง (เลือกอย่างใดอย่างหนึ่ง) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">การเดินทาง</label>
                            @php $currentVan = old('van', $apply->van ?? 'no'); @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="travel_self" name="van" value="no" {{ $currentVan === 'no' ? 'checked' : '' }}>
                                <label class="form-check-label" for="travel_self">เดินทางด้วยตนเอง</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="travel_van" name="van" value="yes" {{ $currentVan === 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label" for="travel_van">เดินทางด้วยรถตู้</label>
                            </div>
                        </div>

                        {{-- ===== ที่พัก (เฉพาะสมาชิกที่มีสิทธิ์) ===== --}}
                        @if($member->shelter_number >= 1)
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">ที่พัก</label>
                                @php $curShelter = old('shelter', $apply->shelter ?? 'ทั่วไป'); @endphp
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="shelter_general" name="shelter" value="ทั่วไป" {{ $curShelter==='ทั่วไป'?'checked':'' }}>
                                    <label class="form-check-label" for="shelter_general">ทั่วไป</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="shelter_special" name="shelter" value="กุฏิพิเศษ" {{ $curShelter==='กุฏิพิเศษ'?'checked':'' }}>
                                    <label class="form-check-label" for="shelter_special">กุฏิพิเศษ</label>
                                </div>
                            </div>
                        @endif

                    @endif

                    {{-- 📌 อัปโหลดไฟล์ ใบสมัคร --}}
                    @if(empty($apply->application) && $apply->id == null )
                        <div class="mb-4">
                            <label for="registration_form" class="form-label">กรุณาอัปโหลดแบบฟอร์มการสมัคร (PNG, JPG, JPEG, PDF)</label>
                            <input type="file" class="form-control" name="registration_form" required onchange="previewFile()" accept="image/png, image/jpeg, application/pdf">
                        </div>
                    @endif

                    {{-- 📌 แสดงตัวอย่างไฟล์ที่อัปโหลดแล้ว --}}
                    <div id="preview" class="mt-3">
                        @if(!empty($apply->application))
                            @php $extension = strtolower(pathinfo($apply->application, PATHINFO_EXTENSION)); @endphp
                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ asset('storage/' . $apply->application) }}" alt="Uploaded Image" class="img-fluid">
                            @elseif($extension === 'pdf')
                                <object data="{{ asset('storage/' . $apply->application) }}" type="application/pdf" width="100%" height="600px"></object>
                            @endif
                        @endif
                    </div>

                    <div class="text-end mt-4">
                        <button id="apply_button" type="submit" class="btn btn-primary btn-lg" {{ empty($apply->application) &&  $apply->id == null ? 'disabled' : '' }}>
                            {{ $apply->id == null  ? 'สมัคร' : 'แก้ไขใบสมัคร' }}
                        </button>
                    </div>
                </form>

                {{-- ======================== ฟอร์ม 2 : ยกเลิกการสมัคร  ======================== --}}
                @if(!empty($apply->application) || $apply->id != null )
                    <form id="cancel_form" action="{{ route('courses.cancel', [$member_id, 'apply' => $apply->id]) }}" method="POST" class="text-end mt-2">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <input type="hidden" name="cancel" value="cancel">
                        <button id="cancel_button" type="submit" class="btn btn-danger btn-lg">ยกเลิกการสมัคร</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- =====================  Scripts  ===================== --}}
    <script>
        function previewFile() {
            const preview = document.getElementById('preview');
            const fileInput = document.querySelector('input[type=file]');
            const file = fileInput.files[0];
            const reader = new FileReader();
            const applyBtn = document.getElementById('apply_button');

            if (file) {
                const fileSizeMB = file.size / (1024 * 1024);
                if (fileSizeMB > 2) {
                    alert("ไฟล์ที่อัปโหลดต้องมีขนาดไม่เกิน 2MB");
                    fileInput.value = "";
                    preview.innerHTML = "";
                    applyBtn.disabled = true;
                    return;
                }

                const fileType = file.type;
                preview.innerHTML = "";

                if (fileType.startsWith("image/")) {
                    reader.onload = e => {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Image preview" class="img-fluid">`;
                        applyBtn.disabled = false;
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === "application/pdf") {
                    preview.innerHTML = `<object data="${URL.createObjectURL(file)}" type="application/pdf" width="100%" height="600px"></object>`;
                    applyBtn.disabled = false;
                } else {
                    alert("ไฟล์ที่รองรับ: PNG, JPG, JPEG, PDF");
                    fileInput.value = "";
                    preview.innerHTML = "";
                    applyBtn.disabled = true;
                }
            } else {
                preview.innerHTML = "";
                applyBtn.disabled = true;
            }
        }

        // ยืนยันฟอร์ม สมัคร / แก้ไข
        document.getElementById('application_form').addEventListener('submit', function (e) {
            e.preventDefault();
            const isEdit = {{ empty($apply->application) ? 'false' : 'true' }};
            Swal.fire({
                title: isEdit ? 'ยืนยันแก้ไขใบสมัคร?' : 'ยืนยันส่งใบสมัคร?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then(result => { if (result.isConfirmed) e.target.submit(); });
        });

        // ยืนยันฟอร์ม ยกเลิก
        const cancelForm = document.getElementById('cancel_form');
        if (cancelForm) {
            cancelForm.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'ยืนยันยกเลิกการสมัคร?',
                    text: 'ขั้นตอนนี้จะลบใบสมัครของคุณ',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'กลับ',
                    reverseButtons: true
                }).then(result => { if (result.isConfirmed) e.target.submit(); });
            });
        }
    </script>
@endsection
