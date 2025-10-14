@extends('layouts.app')        

@section('body-classes', 'hold-transition sidebar-mini layout-fixed theme-dark dashboard-with-image')

@section('content')
<!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard Admin</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Halaman Utama</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Dashboard Admin
                    </li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <row class="">
        <div class="col-lg-10 mx-auto">
            <div class="dashboard-overlay">
                <div class="dashboard-overlay__content">
                    <span class="myabsen-chip">MyAbsen Administrator</span>
                    <h1 class="dashboard-overlay__title">Selamat Datang di MyAbsen</h1>
                    <p class="dashboard-overlay__subtitle">Kelola data karyawan, absensi, dan lembur dengan cepat dan teratur.</p>
              <div class="dashboard-overlay__stats">
                <div class="dashboard-overlay__stat-item">
                  <span>Departemen Aktif</span>
                  <strong>{{ $dashboardStats['departments'] ?? 0 }}</strong>
                </div>
                <div class="dashboard-overlay__stat-item">
                  <span>Karyawan</span>
                  <strong>{{ $dashboardStats['employees'] ?? 0 }}</strong>
                </div>
                <div class="dashboard-overlay__stat-item">
                  <span>Lembur Menunggu</span>
                  <strong>{{ $dashboardStats['pendingOvertimes'] ?? 0 }}</strong>
                </div>
              </div>
              <div class="dashboard-overlay__meta mt-3">
                <span class="hero-accent">Powered by MyAbsen</span>
                <span>© <?php echo date('Y') ?></span>
              </div>
            </div>
                <div class="dashboard-overlay__photo">
                    <img src="/img/need.jpg" alt="Porsche Illustration">
                </div>
            </div>
        </div>
        </row>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
<!-- /.content-wrapper -->

@endsection
