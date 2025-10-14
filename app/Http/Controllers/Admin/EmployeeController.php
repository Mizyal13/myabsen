<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\Department;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Carbon\Carbon; // [EXISTING] dipakai untuk parsing/konversi tanggal
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;

class EmployeeController extends Controller
{
    public function index() {
        $data = [
            'employees' => Employee::all()
        ];
        return view('admin.employees.index')->with($data);
    }

    public function create() {
        $data = [
            'departments' => Department::all(),
            'desgs' => ['Manajer', 'Asisten Manajer', 'Kepala Divisi', 'Staff']
        ];
        return view('admin.employees.create')->with($data);
    }

    public function store(Request $request) {
        // ===================== VALIDASI =====================
        // [FIX] tambahkan validasi format tanggal & email unik
        $this->validate($request, [
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'sex'            => 'required|in:Male,Female',
            'desg'           => 'required|string|max:100',
            'department_id'  => 'required|integer',
            'salary'         => 'required|numeric',
            'email'          => 'required|email|unique:users,email', // [FIX]
            'dob'            => 'required|date_format:d-m-Y',        // [FIX]
            'join_date'      => 'required|date_format:d-m-Y',        // [FIX]
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'       => 'required|confirmed|min:6',
        ]);

        // ===================== TRANSAKSI =====================
        // [NEW] supaya kalau ada error di tengah jalan, insert sebelumnya di-rollback
        return DB::transaction(function () use ($request) {

            // ---------- USERS + ROLE ----------
            $user = User::create([
                'name'     => $request->first_name.' '.$request->last_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $employeeRole = Role::where('name', 'employee')->first();
            $user->roles()->attach($employeeRole);

            // ---------- KONVERSI TANGGAL ----------
            // [FIX] dari 'DD-MM-YYYY' -> 'YYYY-MM-DD' untuk MySQL
            $dob       = Carbon::createFromFormat('d-m-Y', $request->dob)->toDateString();
            $joinDate  = Carbon::createFromFormat('d-m-Y', $request->join_date)->toDateString();

            // ---------- NORMALISASI NILAI ----------
            // [HARDEN] pastikan gaji hanya angka
            $salary = preg_replace('/[^\d]/', '', (string) $request->salary);

            // ---------- PAYLOAD EMPLOYEE ----------
            $employeeDetails = [
                'user_id'       => $user->id,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'sex'           => $request->sex,
                'dob'           => $dob,          // [FIX]
                'join_date'     => $joinDate,     // [FIX]
                'desg'          => $request->desg,
                'department_id' => (int) $request->department_id,
                'salary'        => $salary,
                'photo'         => 'user.png',
            ];

            // ---------- UPLOAD FOTO (opsional) ----------
            if ($request->hasFile('photo')) {
                // [HARDEN] sanitasi nama file agar aman/rapi
                $original = pathinfo($request->file('photo')->getClientOriginalName(), PATHINFO_FILENAME);
                $ext      = strtolower($request->file('photo')->getClientOriginalExtension());
                $base     = preg_replace('/[^a-z0-9\-]+/i', '-', strtolower($original));
                $base     = trim($base, '-');
                $filename = $base . '_' . time() . '.' . $ext;

                $img = Image::make($request->file('photo')->getRealPath());
                $img->resize(300, 300)->save(
                    public_path('storage/employee_photos/'.$filename)
                );

                $employeeDetails['photo'] = $filename;
            }

            // ---------- SIMPAN EMPLOYEE ----------
            Employee::create($employeeDetails);

            // ---------- SUKSES ----------
            $request->session()->flash('success', 'Karyawan berhasil ditambahkan!');
            return back();
        });
    }

    public function attendance(Request $request) {
        $data = ['date' => null];

        if($request->all()) {
            $date = Carbon::create($request->date);
            $employees = $this->attendanceByDate($date);
            $data['date'] = $date->format('d M, Y');
        } else {
            $employees = $this->attendanceByDate(Carbon::now());
        }

        $data['employees'] = $employees;
        return view('admin.employees.attendance')->with($data);
    }

    public function attendanceByDate($date) {
        $employees = DB::table('employees as e')
            ->leftJoin('users as u','e.user_id','u.id')
            ->leftJoin('role_user as ru','u.id','ru.user_id')
            ->select('e.id', 'e.first_name', 'e.last_name', 'e.desg', 'e.department_id')
            ->where('ru.role_id','2')
            ->get();

        $attendances = Attendance::all()->filter(function($attendance) use ($date){
            return $attendance->created_at->dayOfYear == $date->dayOfYear;
        });

        return $employees->map(function($employee) use($attendances) {
            $attendance = $attendances->where('employee_id', $employee->id)->first();
            $employee->attendanceToday = $attendance;
            $employee->department = Department::find($employee->department_id)->name;
            return $employee;
        });
    }

    public function destroy($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        $user = User::findOrFail($employee->user_id);

        DB::table('leaves')->where('employee_id', $employee_id)->delete();
        DB::table('attendances')->where('employee_id', $employee_id)->delete();
        DB::table('expenses')->where('employee_id', $employee_id)->delete();

        $employee->delete();
        $user->roles()->detach();
        $user->delete();

        request()->session()->flash('success', 'Karyawan berhasil dihapus!');
        return back();
    }

    public function attendanceDelete($attendance_id) {
        $attendance = Attendance::findOrFail($attendance_id);
        $attendance->delete();
        request()->session()->flash('success', 'Riwayat Absensi berhasil dihapus!');
        return back();
    }

    public function employeeProfile($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        return view('admin.employees.profile')->with('employee', $employee);
    }
}
