@extends('layouts.app')        

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Register Absensi</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.index') }}">Dashboard Karyawan</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Register Absensi
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
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Absensi Hari ini
                                <span id="attendanceClock">{{ now()->format('d-m-Y, H:i:s') }}</span>
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        @include('messages.alerts')
                        <div
                            id="attendance-config"
                            data-has-attendance="{{ $attendance ? '1' : '0' }}"
                            data-has-registered-attendance="{{ $registered_attendance ? '1' : '0' }}"
                            data-requires-exit-location="{{ ($attendance && !$registered_attendance) ? '1' : '0' }}"
                            hidden
                        ></div>
                        <!-- form start -->
                        @if (!$attendance)
                        <form role="form" method="post" action="{{ route('employee.attendance.store', $employee->id) }}" enctype="multipart/form-data">
                        @else
                        <form role="form" method="post" action="{{ route('employee.attendance.update', $attendance->id) }}" enctype="multipart/form-data">
                            @method('PUT')
                        @endif
                            @csrf
                            <div class="card-body">
                                <?php if(date('h')>=17) { echo "Absensi Ditutup"; } else { ?>
                                @if (!$attendance)
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_time">Waktu Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="entry_time"
                                            id="entry_time"
                                            placeholder="--:--:--"
                                            disabled
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_location">Lokasi Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            id="entry_loc"
                                            placeholder="Memuat Lokasi..."
                                            disabled
                                            />
                                            <input type="text" name="entry_location"
                                            id="entry_location" hidden>
                                            <input type="hidden" name="entry_lat" id="entry_lat">
                                            <input type="hidden" name="entry_lon" id="entry_lon">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Masuk</label>
                                            <div class="camera-box" data-target="entry">
                                                <video id="entry_video" class="camera-video" autoplay playsinline muted></video>
                                                <canvas id="entry_canvas" class="d-none"></canvas>
                                                <img id="entry_preview" class="img-fluid rounded photo-preview d-none" alt="Preview Foto Masuk">
                                                <div class="camera-alert alert alert-warning mt-2 d-none">
                                                    Kamera tidak tersedia. Izinkan akses kamera atau gunakan perangkat lain.
                                                </div>
                                                <div class="btn-group-vertical w-100 mt-2">
                                                    <button type="button" class="btn btn-info capture-btn" data-target="entry">Ambil Foto</button>
                                                    <button type="button" class="btn btn-secondary retake-btn d-none" data-target="entry">Ulangi Foto</button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="entry_photo_data" id="entry_photo_data">
                                            <small class="form-text text-muted">Pastikan wajah terlihat jelas.</small>
                                            @error('entry_photo_data')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="entry_time">Waktu Absensi</label>
                                            <input
                                            type="text"
                                            value="{{ Carbon\Carbon::parse($attendance->created_at)->format('d-m-Y,  H:i:s') }}"
                                            class="form-control text-center"
                                            name="entry_time"
                                            id="entry_time"
                                            placeholder="--:--:--"
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="entry_location">Lokasi Absensi</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="entry_location"
                                            value="{{ $attendance->entry_location }}"
                                            placeholder="..."
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Masuk</label>
                                            @if ($attendance->entry_photo)
                                                <div class="rounded overflow-hidden border" style="background: rgba(0,0,0,0.3);">
                                                    <img src="{{ asset('storage/'.$attendance->entry_photo) }}" alt="Foto Masuk" class="img-fluid">
                                                </div>
                                            @else
                                                <p class="text-muted">Foto tidak tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if (!$registered_attendance)
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exit_time">Waktu Selesai</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="exit_time"
                                            id="exit_time"
                                            placeholder="--:--:--"
                                            disabled
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exit_location">Lokasi Selesai</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            id="exit_loc"
                                            @if ($attendance)
                                            placeholder="Memuat Lokasi..."
                                                
                                            @else
                                            placeholder="..."
                                                
                                            @endif
                                            disabled
                                            />
                                            <input type="text" name="exit_location"
                                            id="exit_location" hidden>
                                            <input type="hidden" name="exit_lat" id="exit_lat">
                                            <input type="hidden" name="exit_lon" id="exit_lon">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Pulang</label>
                                            @if ($attendance)
                                                <div class="camera-box" data-target="exit">
                                                    <video id="exit_video" class="camera-video" autoplay playsinline muted></video>
                                                    <canvas id="exit_canvas" class="d-none"></canvas>
                                                    <img id="exit_preview" class="img-fluid rounded photo-preview d-none" alt="Preview Foto Pulang">
                                                    <div class="camera-alert alert alert-warning mt-2 d-none">
                                                        Kamera tidak tersedia. Izinkan akses kamera atau gunakan perangkat lain.
                                                    </div>
                                                    <div class="btn-group-vertical w-100 mt-2">
                                                        <button type="button" class="btn btn-info capture-btn" data-target="exit">Ambil Foto</button>
                                                        <button type="button" class="btn btn-secondary retake-btn d-none" data-target="exit">Ulangi Foto</button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="exit_photo_data" id="exit_photo_data">
                                                @error('exit_photo_data')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            @else
                                                <p class="text-muted mb-0">Akan tersedia setelah absen masuk.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exit_time">Waktu Selesai</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="exit_time"
                                            id="exit_time"
                                            value="{{ Carbon\Carbon::parse($attendance->updated_at)->format('d-m-Y,  H:i:s') }}"
                                            placeholder="--:--:--"
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exit_location">Lokasi Selesai</label>
                                            <input
                                            type="text"
                                            class="form-control text-center"
                                            name="exit_location"
                                            value="{{ $attendance->exit_location }}"
                                            placeholder="..."
                                            disabled
                                            style="background: #333; color:#f4f4f4"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Pulang</label>
                                            @if ($attendance->exit_photo)
                                                <div class="rounded overflow-hidden border" style="background: rgba(0,0,0,0.3);">
                                                    <img src="{{ asset('storage/'.$attendance->exit_photo) }}" alt="Foto Pulang" class="img-fluid">
                                                </div>
                                            @else
                                                <p class="text-muted">Foto tidak tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                
                            </div>
                            <!-- /.card-body -->
                            @if (!$registered_attendance)
                            <div class="card-footer" >
                                @if (!$attendance)
                                <button type="submit" class="btn btn-primary p-3" style="font-size:1.2rem">
                                    Absen Masuk
                                </button>    
                                @else
                                <button type="submit" class="btn btn-primary pull-right p-3" style="font-size:1.2rem">
                                    Absen Keluar/Selesai
                                </button>
                                @endif
                            </div>   
                            @endif
                        <?php } ?>
                            
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection

@section('extra-js')
<script>
// var x = document.getElementById("demo");
// if('geolocation' in navigator) {
//     console.log('gl available');
//     navigator.geolocation.getCurrentPosition(position => {
//         console.log('sda');
//     })
// }
// function getLocation() {
//     if (navigator.geolocation) {
//         console.log('getting location')
//         navigator.geolocation.getCurrentPosition(showPosition);
//     } else { 
//         x.innerHTML = "Geolocation is not supported by this browser.";
//     }
// }

// function showPosition(position) {
//     x.innerHTML = "Latitude: " + position.coords.latitude + 
//     "<br>Longitude: " + position.coords.longitude;
// }
// /attendance/get-location
$(document).ready(function() {
    const configEl = document.getElementById('attendance-config');
    const readFlag = (name) => {
        if (!configEl || !configEl.dataset) {
            return false;
        }
        return configEl.dataset[name] === '1';
    };
    const hasAttendance = readFlag('hasAttendance');
    const hasRegisteredAttendance = readFlag('hasRegisteredAttendance');
    const entryTimeField = $('#entry_time');
    const exitTimeField = $('#exit_time');
    const attendanceClock = $('#attendanceClock');
    const momentAvailable = typeof window.moment !== 'undefined';
    const entryLatInput = $('#entry_lat');
    const entryLonInput = $('#entry_lon');
    const exitLatInput = $('#exit_lat');
    const exitLonInput = $('#exit_lon');
    const requiresExitLocation = readFlag('requiresExitLocation');

    function formatNow() {
        if (momentAvailable) {
            return window.moment().format('DD-MM-YYYY, HH:mm:ss');
        }
        const now = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        return [
            pad(now.getDate()),
            pad(now.getMonth() + 1),
            now.getFullYear()
        ].join('-') + ', ' + [pad(now.getHours()), pad(now.getMinutes()), pad(now.getSeconds())].join(':');
    }

    function updateTimeDisplays() {
        const formatted = formatNow();
        if (attendanceClock.length) {
            attendanceClock.text(formatted);
        }
        if (entryTimeField.length && !hasAttendance) {
            entryTimeField.val(formatted);
        }
        if (exitTimeField.length && (!hasAttendance || (hasAttendance && !hasRegisteredAttendance))) {
            exitTimeField.val(formatted);
        }
    }

    updateTimeDisplays();
    setInterval(updateTimeDisplays, 1000);

    let entryLocationReady = false;
    let exitLocationReady = !hasAttendance || hasRegisteredAttendance;

    function applyLocation(value, ready) {
        if (typeof ready === 'undefined') {
            ready = true;
        }
        $('#entry_loc').val(value);
        $('#entry_location').val(value);
        entryLocationReady = ready;
    }

    function applyExitLocation(value, ready) {
        if (typeof ready === 'undefined') {
            ready = true;
        }
        $('#exit_loc').val(value);
        $('#exit_location').val(value);
        exitLocationReady = ready;
    }

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            entryLatInput.val(latitude);
            entryLonInput.val(longitude);
            if (!hasAttendance || hasRegisteredAttendance) {
                exitLatInput.val(latitude);
                exitLonInput.val(longitude);
            }
            $.post(
                "/employee/attendance/get-location",
                {
                    lat: position.coords.latitude,
                    lon: position.coords.longitude,
                    _token: $('meta[name=csrf-token]').attr('content'),
                }
            )
            .done(function(data) {
                applyLocation(data, true);
            })
            .fail(function() {
                applyLocation('Lokasi tidak dapat dimuat', true);
            });
        }, function(error) {
            console.warn('Geolocation error:', error);
            applyLocation('Izin lokasi ditolak', true);
            entryLatInput.val('');
            entryLonInput.val('');
            if (requiresExitLocation) {
                exitLatInput.val('');
                exitLonInput.val('');
            }
        });
    } else {
        applyLocation('Geolocation tidak tersedia', true);
        entryLatInput.val('');
        entryLonInput.val('');
        if (requiresExitLocation) {
            exitLatInput.val('');
            exitLonInput.val('');
        }
    }

    function fetchExitLocation(lat, lon) {
        if (typeof lat === 'undefined' || typeof lon === 'undefined') {
            return;
        }
        exitLatInput.val(lat);
        exitLonInput.val(lon);
        applyExitLocation('Memuat lokasi...', false);

        $.post(
            "/employee/attendance/get-location",
            {
                lat: lat,
                lon: lon,
                _token: $('meta[name=csrf-token]').attr('content'),
            }
        )
        .done(function(data) {
            applyExitLocation(data, true);
        })
        .fail(function() {
            applyExitLocation('Lokasi tidak dapat dimuat', true);
        });
    }

    const cameraBoxes = $('.camera-box');
    let mediaStream = null;

    const initCamera = async () => {
        if (mediaStream) {
            return mediaStream;
        }
        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            return mediaStream;
        } catch (error) {
            console.error('Tidak dapat mengakses kamera', error);
            throw error;
        }
    };

    const setupCameraBox = ($box) => {
        const target = $box.data('target');
        const video = $box.find('video')[0];
        const canvas = $box.find('canvas')[0];
        const preview = $box.find('.photo-preview')[0];
        const captureBtn = $box.find('.capture-btn');
        const retakeBtn = $box.find('.retake-btn');
        const alertBox = $box.find('.camera-alert');
        const hiddenInput = $('#' + target + '_photo_data');

        initCamera()
            .then((stream) => {
                video.srcObject = stream;
                video.play();
            })
            .catch(() => {
                alertBox.removeClass('d-none');
                captureBtn.prop('disabled', true);
            });

        captureBtn.on('click', function () {
            if (target === 'exit' && navigator.geolocation) {
                exitLocationReady = false;
                navigator.geolocation.getCurrentPosition(pos => {
                    fetchExitLocation(pos.coords.latitude, pos.coords.longitude);
                }, () => {
                    applyExitLocation('Izin lokasi ditolak', true);
                    exitLatInput.val('');
                    exitLonInput.val('');
                });
            }

            const width = video.videoWidth || 640;
            const height = video.videoHeight || 480;
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, width, height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
            hiddenInput.val(dataUrl);
            preview.src = dataUrl;
            preview.classList.remove('d-none');
            $(video).addClass('d-none');
            captureBtn.addClass('d-none');
            retakeBtn.removeClass('d-none');
        });

        retakeBtn.on('click', function () {
            hiddenInput.val('');
            preview.classList.add('d-none');
            $(video).removeClass('d-none');
            captureBtn.removeClass('d-none');
            retakeBtn.addClass('d-none');
        });
    };

    if (cameraBoxes.length) {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            cameraBoxes.each(function () {
                setupCameraBox($(this));
            });
        } else {
            cameraBoxes.each(function () {
                $(this).find('.camera-alert').removeClass('d-none');
                $(this).find('.capture-btn').prop('disabled', true);
            });
        }
    }

    $('form').on('submit', function (e) {
        const entryData = $('#entry_photo_data');
        if (entryData.length && !entryData.val()) {
            e.preventDefault();
            alert('Mohon ambil foto masuk terlebih dahulu.');
            return;
        }
        if (entryData.length && !entryLocationReady) {
            e.preventDefault();
            alert('Sedang mengambil lokasi masuk. Mohon tunggu beberapa detik dan coba lagi.');
            return;
        }
        const exitData = $('#exit_photo_data');
        if (exitData.length && !exitData.val()) {
            e.preventDefault();
            alert('Mohon ambil foto pulang terlebih dahulu.');
            return;
        }
        if (exitData.length && !exitLocationReady) {
            e.preventDefault();
            alert('Sedang mengambil lokasi pulang. Mohon tunggu beberapa detik dan coba lagi.');
        }
    });
});
</script>
@endsection
