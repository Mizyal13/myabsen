<?php

namespace App\Http\Controllers\Employee;

use App\Expense;
use App\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class ExpenseController extends Controller
{
    public function index () {
        $employee = Auth::user()->employee;
        $data = [
            'employee' => $employee,
            'expenses' => $employee->expense
        ];
        return view('employee.expenses.index')->with($data);
    }

    public function create () {
        $departemen = DB::table('departments')->where('id',Employee::find(Auth::user()->employee->id)->department_id)->get();
        $absen = DB::table('attendances')->where('employee_id',Auth::user()->employee->id)->get();
        $out = date('H',strtotime($absen[0]->updated_at));
        if($out > date('H',strtotime($departemen[0]->overtime_start)) && $out <= date('H',strtotime($departemen[0]->overtime_end))) {
            $jumlah_jam_lembur = $out - date('H',strtotime($departemen[0]->overtime_start));
            $upah_lembur = $departemen[0]->overtime_cost*$jumlah_jam_lembur;
        } else {
            $upah_lembur = "Jam Lembur adalah lewat dari ".$departemen[0]->overtime_start."";
        }
        $data = [
            'employee' => Auth::user()->employee,
            'jam_absen_out' => $out,
            'jumlah_jam_lembur' => $jumlah_jam_lembur ?? 0,
            'overtime_cost' => $departemen[0]->overtime_cost,
            'upah_lembur' => $upah_lembur
        ];
        return view('employee.expenses.create')->with($data);
    }

    public function store(Request $request, $employee_id) {
        $data = [
            'employee' => Auth::user()->employee
        ];
        $this->validate($request, [
            'reason' => 'required',
            'description' => 'required',
            'amount' => 'required | numeric',
            'receipt' => 'image | nullable'
        ]);

        $filename_store = null;
        // Photo upload
        if ($request->hasFile('receipt')) {
            $ext = $request->file('receipt')->getClientOriginalExtension();
            $filename_store = 'receipt_'.time().'.'.$ext;

            $image = Image::make($request->file('receipt')->getRealPath())
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($ext ?? 'jpg');

            Storage::disk('public')->put('receipts/'.$filename_store, (string) $image);
        }
        Expense::create([
            'employee_id' => $employee_id,
            'reason' => $request->input('reason'),
            'description' => $request->input('description'),
            'amount' => $request->input('amount'),
            'receipt' => $filename_store
        ]);
        $request->session()->flash('success', 'Pengajuan Lembur Berhasil Dikirim, silahkan tunggu status approval');
        return redirect()->route('employee.expenses.create')->with($data);
    }

    public function edit($expense_id) {
        $expense = Expense::findOrFail($expense_id);
        Gate::authorize('employee-expenses-access', $expense);
        return view('employee.expenses.edit')->with('expense', $expense);
    }

    public function update(Request $request, $expense_id) {
        $expense = Expense::findOrFail($expense_id);
        Gate::authorize('employee-expenses-access', $expense);
        $data = [
            'employee' => Auth::user()->employee
        ];
        $this->validate($request, [
            'reason' => 'required',
            'description' => 'required',
            'amount' => 'required | numeric',
            'receipt' => 'image | nullable'
        ]);
        $expense->reason = $request->reason;
        $expense->description = $request->description;
        $expense->amount = $request->amount;
        if ($request->hasFile('receipt')) {
            if ($expense->receipt) {
                Storage::disk('public')->delete('receipts/'.$expense->receipt);
            }

            $ext = $request->file('receipt')->getClientOriginalExtension();
            $filename_store = 'receipt_'.time().'.'.$ext;

            $image = Image::make($request->file('receipt')->getRealPath())
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($ext ?? 'jpg');

            Storage::disk('public')->put('receipts/'.$filename_store, (string) $image);
            $expense->receipt = $filename_store;
        }
        $expense->save();
        $request->session()->flash('success', 'Pengajuan Lembur Berhasil Di Update');
        return redirect()->route('employee.expenses.index');
    }

    public function destroy($expense_id) {
        $expense = Expense::findOrFail($expense_id);
        Gate::authorize('employee-expenses-access', $expense);
        if ($expense->receipt) {
            Storage::disk('public')->delete('receipts/'.$expense->receipt);
        }
        $expense->delete();
        request()->session()->flash('success', 'Data Pengajuan Lembur Berhasil Di Hapus');
        return redirect()->route('employee.expenses.index');
    }
}
