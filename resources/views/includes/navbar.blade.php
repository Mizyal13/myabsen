<!-- Navbar -->
@php
    use Illuminate\Support\Facades\Storage;
    $employee = Auth::user()->employee ?? null;
    $photoFile = $employee ? $employee->photo : null;
    $photoPath = $photoFile ? 'employee_photos/'.$photoFile : null;
    $photoUrl = ($photoPath && Storage::disk('public')->exists($photoPath))
        ? Storage::url($photoPath)
        : asset('img/partner.png');
@endphp
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"
                ><i class="fas fa-bars"></i
            ></a>
        </li>

    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-flex align-items-center mr-2">
            <button type="button" class="btn-theme-toggle" id="themeToggle" aria-label="Ganti tema">
                <i class="fas fa-moon" id="themeToggleIcon"></i>
                <span class="theme-state-label d-none d-md-inline" id="themeToggleLabel">Mode Gelap</span>
            </button>
        </li>

        <li class="nav-item dropdown user user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ $photoUrl }}" class="user-image img-circle elevation-2" alt="User Image">
                <span class="hidden-xs">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-primary">
                <img  src="{{ $photoUrl }}"
                class="img-circle elevation-2" alt="User Image">
        
                <p>
                    {{ Auth::user()->name }}
                    @if ( Auth::user()->employee )
                    - {{ Auth::user()->employee->desg }}, {{ Auth::user()->employee->department->name }}
                    @endif 
                </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body text-center">
                    @if ( Auth::user()->employee )
                    <small>Terdaftar Sejak {{ Carbon\Carbon::parse(Auth::user()->employee->join_date)->format('d M, Y') }}</small>
                    @endif 
                <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                <div class="pull-left">
                    @if ( Auth::user()->roles[0]['id'] == 2 )
                    <a href="{{ route('employee.profile') }}" class="btn btn-default btn-flat">Profil Karyawan</a>
                    @elseif(Auth::user()->roles[0]['id'] == 1)
                    <a href="{{ route('admin.profile', Auth::user()->employee->id) }}" class="btn btn-default btn-flat">Profil Admin</a>
                    @endif
                </div>
                <div class="pull-right">
                    <a href="{{ route('logout') }}" 
                    class="btn btn-default btn-flat"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                    >Sign out</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>
