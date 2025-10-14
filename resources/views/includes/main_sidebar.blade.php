<!-- Main Sidebar Container -->
@php
    use Illuminate\Support\Facades\Storage;
    $user = Auth::user();
    $sidebarEmployee = $user->employee ?? null;
    $sidebarPhoto = $sidebarEmployee ? $sidebarEmployee->photo : null;
    $sidebarPhotoPath = $sidebarPhoto ? 'employee_photos/'.$sidebarPhoto : null;
    $sidebarPhotoUrl = ($sidebarPhotoPath && Storage::disk('public')->exists($sidebarPhotoPath))
        ? Storage::url($sidebarPhotoPath)
        : asset('img/partner.png');
@endphp
<aside class="main-sidebar elevation-4" style = "z-index: 1040 !important;">
    <!-- Brand Logo -->
    <a 
    @can('admin-access')
        href="{{ route('admin.index') }}"
    @endcan
    @can('employee-access')
        href="{{ route('employee.index') }}"
    @endcan
    class="brand-link text-center">
        {{-- <img
            src="/dist/img/AdminLTELogo.png"
            alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3"
            style="opacity: 0.8;"
        /> --}}
        <span class="brand-text font-weight-light ">MyAbsen</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img
                    src="{{ $sidebarPhotoUrl }}"
                    class="img-circle elevation-2"
                    alt="User Image"
                />
                
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul
                class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false"
            >
                @can('admin-access')

                <li class="nav-item">
                    <a href="{{ route('admin.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                                Dashboard Admin
                            
                        </p>
                    </a>
                </li>
                @include('includes.admin.sidebar_items')
                @endcan
                @can('employee-access')
                <li class="nav-item">
                    <a href="{{ route('employee.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                                Dashboard Karyawan
                            
                        </p>
                    </a>
                </li>
                @include('includes.employee.sidebar_items')
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
