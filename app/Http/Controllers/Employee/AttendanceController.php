<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Attendance;
use App\Holiday;
use App\Rules\DateRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    // -------- IP helper (lebih robust saat dev/prod)
    public function getIp()
    {
        // ambil IP dari Laravel (menghormati proxy jika dikonfigurasi)
        $ip = request()->ip();
        if (! $ip) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }
        return $ip;
    }

    // -------- Reverse geocoding Nominatim yang stabil
    public function location(Request $request)
    {
        // validasi input
        $request->validate([
            'lat' => ['required','numeric'],
            'lon' => ['required','numeric'],
        ]);

        $address = $this->reverseGeocode($request->lat, $request->lon);

        return $address ?? 'Geo Tag Expired';
    }

    // Opens view for attendance register form
    public function create() {
        $employee = Auth::user()->employee;
        $data = [
            'employee' => $employee,
            'attendance' => null,
            'registered_attendance' => null
        ];
        $last_attendance = $employee->attendance->last();
        if($last_attendance) {
            if($last_attendance->created_at->format('d') == Carbon::now()->format('d')){
                $data['attendance'] = $last_attendance;
                if($last_attendance->registered)
                    $data['registered_attendance'] = 'ya';
            }
        }
        return view('employee.attendance.create')->with($data);   
    }

    // Simpan data record absensi
    public function store(Request $request, $employee_id) {
        $request->validate([
            'entry_photo_data' => ['required', 'string'],
            'entry_lat'        => ['nullable', 'numeric'],
            'entry_lon'        => ['nullable', 'numeric'],
        ]);

        $entryPhotoPath = $this->storeAttendancePhoto(
            $request->input('entry_photo_data'),
            'entry_photo_data'
        );

        $entryLocation = $this->resolveLocation(
            $request->input('entry_lat'),
            $request->input('entry_lon'),
            $request->input('entry_location')
        );

        $attendance = new Attendance([
            'employee_id'    => $employee_id,
            'entry_ip'       => $request->ip(),
            'time'           => date('h'),
            'entry_location' => $entryLocation,
            'entry_photo'    => $entryPhotoPath,
        ]);
        $attendance->save();
        if(date('h')<=9) {
         $request->session()->flash('success', 'Absensi Anda berhasil direkam sistem');
         } else {
            $request->session()->flash('success', ' Absensi Anda berhasil direkam sistem dengan catatan keterlambatan');
         }
        return redirect()->route('employee.attendance.create')->with('employee', Auth::user()->employee);
    }

    // Hapus data record absensi
    public function update(Request $request, $attendance_id) {
        $request->validate([
            'exit_photo_data' => ['required', 'string'],
            'exit_lat'        => ['nullable', 'numeric'],
            'exit_lon'        => ['nullable', 'numeric'],
        ]);

        $attendance = Attendance::findOrFail($attendance_id);

        $exitPhotoPath = $this->storeAttendancePhoto(
            $request->input('exit_photo_data'),
            'exit_photo_data'
        );

        if ($attendance->exit_photo) {
            Storage::disk('public')->delete($attendance->exit_photo);
        }

        $attendance->exit_ip = $request->ip();
        $attendance->exit_location = $this->resolveLocation(
            $request->input('exit_lat'),
            $request->input('exit_lon'),
            $request->input('exit_location')
        );
        $attendance->exit_photo = $exitPhotoPath;
        $attendance->registered = 'ya';
        $attendance->save();
        $request->session()->flash('success', 'Absensi Anda berhasil diakhiri');
        return redirect()->route('employee.attendance.create')->with('employee', Auth::user()->employee);
    }

    public function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    protected function resolveLocation($lat, $lon, $fallback = null): string
    {
        $lat = is_null($lat) ? null : (float) $lat;
        $lon = is_null($lon) ? null : (float) $lon;

        if (!is_null($lat) && !is_null($lon)) {
            $address = $this->reverseGeocode($lat, $lon);
            if ($address) {
                return $address;
            }
        }

        if ($fallback && trim($fallback) !== '') {
            return $fallback;
        }

        if (!is_null($lat) && !is_null($lon)) {
            return 'Lat: '.round($lat, 6).', Lon: '.round($lon, 6);
        }

        return 'Geo Tag Expired';
    }

    protected function reverseGeocode($lat, $lon): ?string
    {
        try {
            $ua = config('app.name').' (contact: '.(config('mail.from.address') ?? 'admin@example.com').')';

            $resp = Http::withHeaders([
                        'User-Agent'      => $ua,
                        'Accept-Language' => 'id,en;q=0.8',
                    ])
                    ->timeout(10)
                    ->retry(2, 300)
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format'         => 'jsonv2',
                        'lat'            => $lat,
                        'lon'            => $lon,
                        'zoom'           => 16,
                        'addressdetails' => 1,
                    ]);

            if (! $resp->ok()) {
                return null;
            }

            $data = $resp->json();

            return $data['display_name'] ?? null;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function storeAttendancePhoto(string $photoData, string $fieldName): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $photoData, $matches)) {
            throw ValidationException::withMessages([
                $fieldName => 'Format foto tidak valid.',
            ]);
        }

        $extension = strtolower($matches[1]);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowed, true)) {
            $extension = 'png';
        }
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $photoData = substr($photoData, strpos($photoData, ',') + 1);
        $binary = base64_decode($photoData);

        if ($binary === false) {
            throw ValidationException::withMessages([
                $fieldName => 'Foto tidak dapat diproses.',
            ]);
        }

        $filename = 'attendance_photos/'.trim($fieldName, '_data').'_'.time().'_'.Str::random(18).'.'.$extension;
        Storage::disk('public')->put($filename, $binary);

        return $filename;
    }

    public function index() {
        $employee = Auth::user()->employee;
        $attendances = $employee->attendance;
        $filter = false;
        if(request()->all()) {
            $this->validate(request(), ['date_range' => new DateRange]);
            if($attendances) {
                [$start, $end] = explode(' - ', request()->input('date_range'));
                $start = Carbon::parse($start);
                $end = Carbon::parse($end)->addDay();
                $filtered_attendances = $this->attendanceOfRange($attendances, $start, $end);
                $leaves = $this->leavesOfRange($employee->leave, $start, $end);
                $holidays = $this->holidaysOfRange(Holiday::all(), $start, $end);
                $attendances = collect();
                $count = $filtered_attendances->count();
                if($count) {
                    $first_day = $filtered_attendances->first()->created_at->dayOfYear;
                    $attendances = $this->get_filtered_attendances($start, $end, $filtered_attendances, $first_day, $count, $leaves, $holidays);
                }
                else{
                    while($start->lessThan($end)) {
                        $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                        $start->addDay();
                    }
                }
                $filter = true;
            }   
        }
        if ($attendances)
            $attendances = $attendances->reverse()->values();
        $data = [
            'employee' => $employee,
            'attendances' => $attendances,
            'filter' => $filter
        ];
        return view('employee.attendance.index')->with($data);
    }

    public function get_filtered_attendances($start, $end, $filtered_attendances, $first_day, $count, $leaves, $holidays) {
        $found_start = false;
        $key = 1;
        $attendances = collect();
        while($start->lessThan($end)) {
            if (!$found_start) {
                if($first_day == $start->dayOfYear()) {
                    $found_start = true;
                    $attendances->add($filtered_attendances->first());
                } else {
                    $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                }
            } else {
                // iterating over the 2nd to .. n dates
                if ($key < $count) {
                    if($start->dayOfYear() != $filtered_attendances->get($key)->created_at->dayOfYear) {
                        $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                    }
                    else {
                        $attendances->add($filtered_attendances->get($key));
                        $key++;
                    }
                }
                else {
                    $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                }
            }
            $start->addDay();
        }

        return $attendances;
    }

    public function checkLeave($leaves, $date) {
        if ($leaves->count() != 0) {
            $leaves = $leaves->filter(function($leave, $key) use ($date) {
                // checks if the end date has a value
                if($leave->end_date) {
                    // if it does then checks if the $date falls between the leave range
                    $condition1 = intval($date->dayOfYear) >= intval($leave->start_date->dayOfYear);
                    $condition2 = intval($date->dayOfYear) <= intval($leave->end_date->dayOfYear);
                    return $condition1 && $condition2;
                }
                // else checks if this day is a leave
                return $date->dayOfYear == $leave->start_date->dayOfYear;
            });
        }
        return $leaves->count();
    }

    public function checkHoliday($holidays, $date) {
        if ($holidays->count() != 0) {
            $holidays = $holidays->filter(function($holiday, $key) use ($date) {
                // checks if the end date has a value
                if($holiday->end_date) {
                    // if it does then checks if the $date falls between the holiday range
                    $condition1 = intval($date->dayOfYear) >= intval($holiday->start_date->dayOfYear);
                    $condition2 = intval($date->dayOfYear) <= intval($holiday->end_date->dayOfYear);
                    return $condition1 && $condition2;
                }
                // else checks if this day is a holiday
                return $date->dayOfYear == $holiday->start_date->dayOfYear;
            });
        }
        return $holidays->count();
    }

    public function attendanceIfNotPresent($start, $leaves, $holidays) {
        $attendance = new Attendance();
        $attendance->created_at = $start;
        if($this->checkHoliday($holidays, $start)) {
            $attendance->registered = 'hari libur';
        } elseif($start->dayOfWeek == 0) {
            $attendance->registered = 'minggu';
        } elseif($this->checkLeave($leaves, $start)) {
            $attendance->registered = 'cuti';
        } else {
            $attendance->registered = 'absen';
        }

        return $attendance;
    }

    public function leavesOfRange($leaves, $start, $end) {
        return $leaves->filter(function($leave, $key) use ($start, $end) {
            // checks if the start date is between the range
            $condition1 = (intval($start->dayOfYear) <= intval($leave->start_date->dayOfYear)) && (intval($end->dayOfYear) >= intval($leave->start_date->dayOfYear));
            // checks if the end date is between the range
            $condition2 = false;
            if($leave->end_date)
                $condition2 = (intval($start->dayOfYear) <= intval($leave->end_date->dayOfYear)) && (intval($end->dayOfYear) >= intval($leave->end_date->dayOfYear));
            // checks if the leave status is approved
            $condition3 = $leave->status == 'diterima';
            // combining all the conditions
            return  ($condition1 || $condition2) && $condition3;
        });
    }

    public function attendanceOfRange($attendances, $start, $end) {
        return $attendances->filter(function($attendance, $key) use ($start, $end) {
                    $date = Carbon::parse($attendance->created_at);
                    if ((intval($date->dayOfYear) >= intval($start->dayOfYear)) && (intval($date->dayOfYear) <= intval($end->dayOfYear)))
                        return true;
                })->values();
    }

    public function holidaysOfRange($holidays, $start, $end) {
        return $holidays->filter(function($holiday, $key) use ($start, $end) {
            // checks if the start date is between the range
            $condition1 = (intval($start->dayOfYear) <= intval($holiday->start_date->dayOfYear)) && (intval($end->dayOfYear) >= intval($holiday->start_date->dayOfYear));
            // checks if the end date is between the range
            $condition2 = false;
            if($holiday->end_date)
                $condition2 = (intval($start->dayOfYear) <= intval($holiday->end_date->dayOfYear)) && (intval($end->dayOfYear) >= intval($holiday->end_date->dayOfYear));
            return  ($condition1 || $condition2);
        });
    }
}
