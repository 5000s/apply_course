<div style="overflow: visible">

    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm" style="overflow: visible">
        <div class="container-fluid" style="overflow: visible">
            <a class="navbar-brand fw-bold" href="{{ route('pages.index') }}">
                มูลนิธิโรงเรียนแห่งชีวิต
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav align-items-center">
                    @if(Auth::check() && Auth::user()->admin == 1)
                        <li class="nav-item dropdown position-relative">
                            <a class="nav-link dropdown-toggle text-primary fw-bold d-flex align-items-center"
                               href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield me-1"></i> เมนูแอดมิน
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                                <li><a class="dropdown-item" href="{{ route('admin.courses') }}">จัดการคอร์ส</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.members') }}">จัดการสมาชิก</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.membersType') }}">รายการสมาชิก</a></li>
                                <li><a class="dropdown-item" href="{{ route('teams.index') }}">จัดการทีม</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard.index') }}">สถิติ</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.import.sheet.buttons') }}">นำเข้าจาก Google Sheet</a></li>
                            </ul>
                        </li>
                    @endif

                    @if(Auth::check())
                        <li class="nav-item ms-3">
                            <a class="btn btn-outline-danger" href="{{ url('admin/logout') }}">Logout</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
</div>

<style>/* Allow dropdowns to escape the collapse container */
    .navbar-collapse,
    .collapse.show {
        overflow: visible !important;
    }

    /* keep your navbar and dropdown stacking normal */
    .navbar {
        position: relative;
        z-index: 1050;
    }
    .dropdown-menu {
        position: absolute !important;
        z-index: 2000 !important;
    }

</style>
