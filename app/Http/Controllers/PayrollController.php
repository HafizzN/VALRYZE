<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of salaries.
     */
    public function index()
    {
        $users = User::with(['division', 'position', 'roles'])
            ->orderBy('name')
            ->paginate(15);

        return view('payroll.index', compact('users'));
    }

    /**
     * Show the form for editing salary settings.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('payroll.edit', compact('user'));
    }

    /**
     * Update the salary settings.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'basic_salary'   => 'nullable|numeric|min:0',
            'allowance'      => 'nullable|numeric|min:0',
            'bpjs_deduction' => 'nullable|numeric|min:0',
            'tax_deduction'  => 'nullable|numeric|min:0',
        ]);

        $user->update([
            'basic_salary'   => $request->basic_salary,
            'allowance'      => $request->allowance,
            'bpjs_deduction' => $request->bpjs_deduction,
            'tax_deduction'  => $request->tax_deduction,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Pengaturan gaji untuk ' . $user->name . ' berhasil diperbarui.');
    }
}
