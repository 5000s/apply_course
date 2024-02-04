<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('pages.index') }}">มูลนิธิโรงเรียนแห่งชีวิต</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('pages.index') }}">คอร์สปฎิบัติ</a>
                    </li>

                    @if(\Illuminate\Support\Facades\Auth::check())

                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ url('admin/logout') }}">Logout</a>
                        </li>

                    @endif

{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" href="{{ route('pages.about') }}">About</a>--}}
{{--                    </li>--}}
                </ul>
            </div>
        </div>
    </nav>
</header>
