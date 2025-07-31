<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">



{{--    <script src="{{url("plugins/datepicker/js/bootstrap-datepicker.js")}}" ></script>--}}
{{--    <script src="{{url("plugins/datepicker/js/bootstrap-datepicker-thai.js")}}" ></script>--}}
{{--    <script src="{{url("plugins/datepicker/js/locales/bootstrap-datepicker.th.js")}}" ></script>--}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <title>มูลนิธิโรงเรียนแห่งชีวิต: Course Manager</title>

    @include('partials.styles')
</head>
<body>

@include('partials.header')

<main class="container-fluid">
    @yield('content')
</main>

@include('partials.footer')

@include('partials.scripts')

</body>
</html>
