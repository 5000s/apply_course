@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center my-4">{{ $course->category }}, {{ $course->location }}</h1>
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
                <p>{{ $course->date_start->format('d/m/Y') . "-".$course->date_end->format('d/m/Y') }}</p>

                <form action="{{ route('courses.save', $member_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">


                    @if($apply->application == "")
                        <div class="mb-3">
                            <label for="registration_form" class="form-label">Upload Registration Form (Image)</label>
                            <input type="file" class="form-control"
                                   name="registration_form" required onchange="previewFile()"
                                   accept="image/png,image/jpeg">
                        </div>
                    @else
                        <div class="mb-3">
                            <input type="hidden" name="cancel" value="cancel">
                        </div>
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
                                {{-- Alternatively, you can use an <iframe> or provide a link to the PDF file --}}
                            @endif
                        @endif
                    </div>
                    @if($apply->application != "")

                        <button id="submit_button" type="submit" class="btn btn-primary" >ยกเลิกการสมัคร</button>
                    @else
                        <button id="submit_button" type="submit" class="btn btn-primary" disabled>ส่งใบสมัคร</button>
                    @endif

                </form>
            </div>
        </div>
    </div>

    <script>
        function previewFile() {
            const preview = document.getElementById('preview');
            const file = document.querySelector('input[type=file]').files[0];
            const submitButton = document.getElementById('submit_button');
            const reader = new FileReader();

            reader.onloadend = function() {
                if (file.type.match('image.*')) {
                    // If the file is an image, display it directly
                    preview.innerHTML = `<img src="${reader.result}" alt="Image preview" style="max-width: 100%; height: auto;">`;
                    submitButton.disabled = false;

                } else if (file.type === 'application/pdf') {
                    // If the file is a PDF, display an embed or provide a link to open in a new tab
                    // Embed PDF. Note: The display might vary across browsers.
                    preview.innerHTML = `<embed src="${reader.result}" type="application/pdf" width="100%" height="600px">`;
                    // Alternatively, you can use an iframe or provide a link to open the PDF in a new tab
                    // preview.innerHTML = `<iframe src="${reader.result}" style="width:100%; height:600px;" frameborder="0"></iframe>`;
                    // preview.innerHTML = `<a href="${reader.result}" target="_blank">View PDF</a>`;

                    submitButton.disabled = false;
                } else{
                   submitButton.disabled = true;
                }
            };

            if (file) {
                reader.readAsDataURL(file); // Reads the file as a data URL
            } else {
                preview.innerHTML = "";
                submitButton.disabled = false;
            }
        }

    </script>
@endsection
