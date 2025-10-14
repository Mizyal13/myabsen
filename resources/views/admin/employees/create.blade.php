@extends('layouts.app')        

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Tambah Karyawan</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Halaman Utama</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Tambah Karyawan
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
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center mt-2">Tambah Karyawan Baru</h5>
                    </div>
                    @include('messages.alerts')
                    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                    <div class="card-body">
                        
                            <fieldset>
                                <div class="form-group">
                                    <label for="">Nama Awal</label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control">
                                    @error('first_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Nama Akhir</label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control">
                                    @error('last_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                                    @error('email')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="dob">Tanggal Lahir</label>
                                    <input type="text" name="dob" id="dob" value="{{ old('dob') }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Jenis Kelamin</label>
                                    <select name="sex" class="form-control">
                                        <option hidden disabled {{ old('sex') ? '' : 'selected' }} value> -- Pilih Opsi -- </option>
                                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('sex')
                                        <div class="text-danger">
                                            Silakan pilih opsi yang valid
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="join_date">Tanggal Bergabung</label>
                                    <input type="text" name="join_date" id="join_date" value="{{ old('join_date') }}" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Jabatan</label>
                                        <select name="desg" class="form-control">
                                            <option hidden disabled {{ old('desg') ? '' : 'selected' }} value> -- Pilih Opsi -- </option>
                                            @foreach ($desgs as $desg)
                                                <option value="{{ $desg }}"
                                                @if (old('desg') == $desg)
                                                    selected
                                                @endif
                                                >
                                                    {{ $desg }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('desg')
                                            <div class="text-danger">
                                                Silahkan Pilih Opsi Valid
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Department</label>
                                        <select name="department_id" class="form-control">
                                            <option hidden disabled {{ old('department_id') ? '' : 'selected' }} value> -- Pilih Opsi -- </option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    @if (old('department_id') == $department->id)
                                                        selected
                                                    @endif    
                                                >
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="text-danger">
                                                Silahkan Pilih Opsi Valid
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Gaji</label>
                                    <input type="text" name="salary" value="{{ old('salary') }}" class="form-control">
                                    @error('salary')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Foto</label>
                                    <input type="file" name="photo" class="form-control-file">
                                    @error('photo')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" name="password" value="{{ old('password') }}" class="form-control">
                                    @error('password')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control">
                                    @error('password_confirmation')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </fieldset>
                            
                        
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-flat btn-primary" style="width: 40%; font-size:1.3rem">Tambah</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->

@endsection

@section('extra-js')
<script>
    (function ($, moment) {
        if (!$ || !moment) {
            return;
        }

        const baseOptions = {
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            }
        };

        initPicker('#dob');
        initPicker('#join_date');

        function initPicker(selector) {
            const $element = $(selector);

            if (!$element.length) {
                return;
            }

            const options = $.extend(true, {}, baseOptions);
            const value = $element.val();

            if (value) {
                options.startDate = moment(value, 'DD-MM-YYYY');
            }

            $element.daterangepicker(options);
        }
    })(window.jQuery, window.moment);
</script>
@endsection
