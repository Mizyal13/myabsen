@extends('layouts.app')        

@section('body-classes', 'hold-transition sidebar-mini layout-fixed theme-dark dashboard-with-image')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Halaman Utama</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Dashboard
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
        @php
            $photoPath = $employee->photo
                ? asset('storage/employee_photos/'.$employee->photo)
                : asset('img/partner.png');
        @endphp
        <div class="col-lg-10 mx-auto">
          <div class="dashboard-overlay">
            <div class="dashboard-overlay__content">
              <span class="myabsen-chip">Hallo, {{ $employee->first_name }}</span>
              <h1 class="dashboard-overlay__title">Selamat Datang Kembali</h1>
              <p class="dashboard-overlay__subtitle">Pantau kehadiran, ajukan cuti, dan ikuti lembur dengan satu portal terpadu.</p>
              <div class="dashboard-overlay__stats">
                <div class="dashboard-overlay__stat-item">
                  <span>Absensi Hari Ini</span>
                  <strong>{{ $employeeStats['today_attendance'] ?? 0 }}</strong>
                </div>
                <div class="dashboard-overlay__stat-item">
                  <span>Pengajuan Cuti</span>
                  <strong>{{ $employeeStats['pending_leaves'] ?? 0 }}</strong>
                </div>
                <div class="dashboard-overlay__stat-item">
                  <span>Lembur Menunggu</span>
                  <strong>{{ $employeeStats['pending_expenses'] ?? 0 }}</strong>
                </div>
              </div>
              <div class="dashboard-overlay__meta mt-3">
                <span>{{ optional($employee->department)->name ?? 'Tidak ada departemen' }}</span>
                <span>Bergabung sejak {{ optional($employee->join_date)->format('d M Y') }}</span>
              </div>
            </div>
            <div class="dashboard-overlay__photo">
              <img src="{{ $photoPath }}" alt="Foto {{ $employee->first_name }}">
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
