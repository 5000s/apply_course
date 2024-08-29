@extends('layouts.app')

@section('content')
    <script>
        function goBack() {
            // Get the current URL
            var currentUrl = window.location.href;

            // Split the URL by '/' to get parts
            var urlParts = currentUrl.split('/');

            // Remove the last part (519) to go back to the parent (courses)
            urlParts.pop();

            // Join the remaining parts to form the new URL
            var newUrl = urlParts.join('/');

            // Redirect to the new URL
            window.location.href = newUrl;
        }
    </script>
    <div class="container">
        <h1 class="text-center my-4">{{ $course->category }}, {{ $course->location }}</h1>
        <h4 class="text-center my-4">{{ $course->coursename }}</h4>

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
        <div class="card">
            <div class="card-body">
                @if($course->category_id == 1 || $course->category_id == 3 || $course->category_id == 6 || $course->category_id == 4 ||  $course->category_id == 2 ||  $course->category_id == 9 )
                    <div>
                        ท่านสามารถรับใบสมัครได้ที่นี้ <br>
                        <a href="{{asset("form/Application_Vipassana_TH.pdf")}}" target="_blank">ใบสมัคร ภาษาไทย</a>,
                        <a href="{{asset("form/Application_Vipassana_EN.pdf")}}" target="_blank">English Form</a>
                    </div>
                @elseif($course->category_id == 5 )
                    <div>
                        ท่านสามารถรับใบสมัครได้ที่นี้ <br>
                        <a href="{{asset("form/Application_Anapanasati_TH.pdf")}}" target="_blank">ใบสมัคร ภาษาไทย</a>,
                        <a href="{{asset("form/Application_Anapanasati_EN.pdf")}}" target="_blank">English Form</a>
                    </div>
                @endif

                <form action="{{ route('courses.save', $member_id) }}" method="POST" enctype="multipart/form-data">
                    <p style="text-align: right">
                        @if($apply->application != "" and $apply->application != null)
                            <button id="back_button" type="button" class="btn btn-info" onclick="goBack()" >ย้อนกลับ</button>
                        @else
                            <button id="submit_button" type="submit" class="btn btn-primary" disabled="disabled">สมัคร</button>
                        @endif
                    </p>
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    @if($course->category_id == 8)
                        @if($apply->application == null)
                            <div class="mb-3">
                                <label for="registration_form" class="form-label">โปรดกรอกชื่อจริงและนามสกุลในช่อง เพื่อยืนยันการสมัคร</label>
                                <input type="text" class="form-control" name="regstration_name" >
                            </div>
                        @else
                            <div class="mb-3">
                                <input type="hidden" name="cancel" value="cancel">
                            </div>
                        @endif
                    @else
                        @if($apply->application == "")
                            <div class="mb-3">
                                <label for="registration_form" class="form-label">กรุณาอัปโหลดแบบฟอร์มการสมัคร (ไฟล์รูปภาพ)</label>
                                <input type="file" class="form-control"
                                       name="registration_form" required onchange="previewFile()"
                                       accept="image/png,image/jpeg">
                            </div>
                        @else
                            <div class="mb-3">
                                <input type="hidden" name="cancel" value="cancel">
                            </div>
                        @endif
                    @endif

                    <div id="preview">
                        @if($apply->application != "")
                            @php
                                $extension = pathinfo($apply->application, PATHINFO_EXTENSION);
                                $extension = strtolower($extension);
                            @endphp

                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                {{-- Display an image --}}
                                <img src="{{ asset('storage/' . $apply->application) }}" alt="Uploaded Image" style="max-width: 100%; height: auto;">
                            @elseif(strtolower($extension) == 'pdf')
                                {{-- Display a PDF --}}
                                <object data="{{ asset('storage/' . $apply->application) }}" type="application/pdf" width="100%" height="600px">
                                    <p>Your browser does not support PDFs. <a href="{{ asset('storage/' . $apply->form_path) }}">Download the PDF</a>.</p>
                                </object>
                            @endif
                        @endif
                    </div>

                    @if($apply->application != "" && $apply->application != null )
                        <button id="submit_button" type="submit" class="btn btn-primary" >ยกเลิกการสมัคร</button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script>
        const submitButton = document.getElementById('submit_button');

        @if($course->category_id != 8)
        function previewFile() {
            const preview = document.getElementById('preview');
            const fileInput = document.querySelector('input[type=file]');
            const file = fileInput.files[0];
            const reader = new FileReader();
            const submitButton = document.getElementById('submit_button');

            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    let canvas = document.createElement('canvas');
                    let ctx = canvas.getContext('2d');

                    // Set maximum dimensions (e.g., 30 cm in pixels)
                    const maxWidthCm = 30;
                    const cmToPx = 37.795275591; // Conversion factor from cm to pixels (for 96 DPI)
                    const maxWidthPx = maxWidthCm * cmToPx;

                    // Calculate new dimensions
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidthPx) {
                        height *= maxWidthPx / width;
                        width = maxWidthPx;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Draw the resized image on the canvas
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convert the canvas to a Blob in JPEG format
                    canvas.toBlob(function(blob) {
                        const resizedFile = new File([blob], file.name.replace(/\..+$/, '.jpg'), { type: 'image/jpeg', lastModified: Date.now() });

                        // Replace the original file in the file input with the resized file
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(resizedFile);
                        fileInput.files = dataTransfer.files;

                        // Display the resized image in the preview
                        const resizedReader = new FileReader();
                        resizedReader.onloadend = function() {
                            preview.innerHTML = `<img src="${resizedReader.result}" alt="Image preview" style="max-width: 100%; height: auto;">`;
                            submitButton.disabled = false; // Enable the submit button
                        };
                        resizedReader.readAsDataURL(resizedFile);
                    }, 'image/jpeg', 0.7); // 70% quality JPEG
                };
                img.src = event.target.result;
            };

            if (file) {
                reader.readAsDataURL(file); // Reads the file as a data URL
            } else {
                preview.innerHTML = "";
                submitButton.disabled = true; // Keep the button disabled if no file is selected
            }
        }

        @else
            submitButton.disabled = false;
        @endif
    </script>
@endsection
