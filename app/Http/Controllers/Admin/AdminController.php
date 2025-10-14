<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Employee;
use App\Department;
use App\User;
use App\Expense;

class AdminController extends Controller
{
    public function index() {
        $stats = [
            'departments' => Department::count(),
            'employees' => Employee::count(),
            'pendingOvertimes' => Expense::where('status', 'pending')->count(),
        ];

        return view('admin.index', [
            'dashboardStats' => $stats,
        ]);
    }

    public function reset_password() {
        return view('auth.reset-password');
    }

    public function update_password(Request $request) {
        $user = User::findOrFail(Auth::user()->id);
        if(Hash::check($request->old_password,$user->password)===true) {
            $user->password = Hash::make($request->password);
            toastr()->success('Password berhasil diperbarui.');
            $user->save();
            // return view('auth.reset-password');
            return back();
        } else {
            toastr()->error('Password salah.');
            return back();
        }
    }

    public function adminProfile($admin_id) {
        $admin = Employee::findOrFail($admin_id);
        return view('admin.profile')->with('admin', $admin);
    }

    public function profile_edit($admin_id) {
        $data = [
            'admin' => Employee::findOrFail($admin_id),
            'departments' => Department::all(),
            'desgs' => ['Manajer', 'Asistent Manajer', 'Projek Manajer', 'Staff']
        ];
        Gate::authorize('admin-profile-access', intval($admin_id));
        return view('admin.profile-edit')->with($data);
    }

    public function profile_update(Request $request, $admin_id) {
        Gate::authorize('admin-profile-access', intval($admin_id));
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'photo' => 'image|nullable'
        ]);
        $admin = Employee::findOrFail($admin_id);
        $user_admin = User::find($admin->user_id);
        $user_admin->name = ''.$request->first_name.' '.$request->last_name.'';
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->dob = $this->normalizeDate($request->dob);
        $admin->sex = $request->gender;
        $admin->join_date = $this->normalizeDate($request->join_date);
        $admin->desg = $request->desg;
        $admin->department_id = $request->department_id;
        if ($request->hasFile('photo')) {
            if ($admin->photo && $admin->photo !== 'user.png') {
                Storage::disk('public')->delete('employee_photos/'.$admin->photo);
            }

            $ext = $request->file('photo')->getClientOriginalExtension() ?: 'jpg';
            $filename_store = 'employee_'.$admin->id.'_'.time().'.'.$ext;

            $image = Image::make($request->file('photo')->getRealPath())
                ->fit(300, 300, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode($ext);

            Storage::disk('public')->put('employee_photos/'.$filename_store, (string) $image);
            $admin->photo = $filename_store;
        }
        $admin->save();
        $user_admin->save();
        toastr()->success('Profil Anda berhasil diperbarui.');
        return redirect()->route('admin.profile', $admin->id);
    }

    /**
     * Normalize date strings coming from the UI (d-m-Y, d/m/Y, Y-m-d) into Y-m-d.
     */
    private function normalizeDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $th) {
                // try next format
            }
        }

        return null;
    }
}
