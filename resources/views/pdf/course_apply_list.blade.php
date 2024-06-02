<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Application List</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        .application {
            margin-bottom: 50px;
        }
        .image {
            max-width: 100%;
        }
    </style>
</head>
<body>
@foreach($applications as $application)
    <div class="application">
        <h2>Application ID: {{ $application->apply_id }}</h2>
        <p><strong>Date Applied:</strong> {{ $application->apply_date }}</p>
        <p><strong>Course Name:</strong> {{ $application->coursename }}</p>
        <p><strong>Name:</strong> {{ $application->name }}</p>
        <p><strong>Surname:</strong> {{ $application->surname }}</p>
        <p><strong>Phone:</strong> {{ $application->phone }}</p>
        <p><strong>Email:</strong> {{ $application->email }}</p>
        <p><strong>Age:</strong> {{ $application->age }}</p>
        <p><strong>Gender:</strong> {{ $application->gender }}</p>
        <p><strong>Buddhism:</strong> {{ $application->buddhism }}</p>
        <p><strong>State:</strong> {{ $application->state }}</p>
        <p><strong>Updated By:</strong> {{ $application->updated_by }}</p>
        <div>
            <strong>Application Image:</strong>
            <img src="{{ asset('storage/' . $application->application) }}" class="image" alt="Application Image">
        </div>
    </div>
    <div class="page-break"></div>
@endforeach
</body>
</html>
