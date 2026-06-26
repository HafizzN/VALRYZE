<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\Shift;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['division', 'position', 'roles'])
            ->where('id', '!=', auth()->id());

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $employees  = $query->orderBy('name')->paginate(15);
        $divisions  = Division::where('is_active', true)->get();

        return view('employees.index', compact('employees', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::where('is_active', true)->with('positions')->get();
        $shifts    = Shift::where('is_active', true)->get();
        $roles     = Role::all();
        return view('employees.create', compact('divisions', 'shifts', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users',
            'nik'             => 'required|string|unique:users',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'gender'          => 'nullable|in:male,female',
            'birth_date'      => 'nullable|date',
            'birth_place'     => 'nullable|string',
            'religion'        => 'nullable|string',
            'marital_status'  => 'nullable|in:single,married,divorced,widowed',
            'division_id'     => 'nullable|exists:divisions,id',
            'position_id'     => 'nullable|exists:positions,id',
            'shift_id'        => 'nullable|exists:shifts,id',
            'join_date'       => 'nullable|date',
            'employment_type' => 'nullable|in:permanent,contract,internship,freelance',
            'annual_leave_quota' => 'nullable|integer|min:0',
            'role'            => 'required|exists:roles,name',
            'photo'           => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employee-photos', 'public');
        }

        $employee = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => Hash::make($request->nik), // default password = NIK
            'nik'              => $request->nik,
            'phone'            => $request->phone,
            'address'          => $request->address,
            'gender'           => $request->gender,
            'birth_date'       => $request->birth_date,
            'birth_place'      => $request->birth_place,
            'religion'         => $request->religion,
            'marital_status'   => $request->marital_status,
            'division_id'      => $request->division_id,
            'position_id'      => $request->position_id,
            'shift_id'         => $request->shift_id,
            'join_date'        => $request->join_date,
            'employment_type'  => $request->employment_type ?? 'permanent',
            'status'           => 'active',
            'annual_leave_quota' => $request->annual_leave_quota ?? 12,
            'photo'            => $photoPath,
        ]);

        $employee->assignRole($request->role);

        // Log creation in audit trail
        ActivityLog::log('create', 'User', $employee->id, [
            'name' => $employee->name,
            'email' => $employee->email,
            'nik' => $employee->nik,
            'division' => $employee->division->name ?? '-',
            'position' => $employee->position->name ?? '-',
            'status' => $employee->status,
        ]);

        // Handle document uploads
        $docTypes = ['ktp', 'npwp', 'cv', 'contract'];
        foreach ($docTypes as $docType) {
            if ($request->hasFile($docType)) {
                $path = $request->file($docType)->store("employee-docs/{$employee->id}", 'public');
                EmployeeDocument::create([
                    'user_id'   => $employee->id,
                    'type'      => $docType,
                    'file_path' => $path,
                    'file_name' => $request->file($docType)->getClientOriginalName(),
                    'mime_type' => $request->file($docType)->getMimeType(),
                    'file_size' => $request->file($docType)->getSize(),
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', "Karyawan {$employee->name} berhasil ditambahkan.");
    }

    public function show(User $employee)
    {
        $employee->load(['division', 'position', 'shift', 'roles', 'documents']);
        $recentAttendances = $employee->attendances()->orderBy('date', 'desc')->limit(10)->get();
        $leaveBalance = $employee->remaining_leave;
        return view('employees.show', compact('employee', 'recentAttendances', 'leaveBalance'));
    }

    public function edit(User $employee)
    {
        $divisions = Division::where('is_active', true)->with('positions')->get();
        $shifts    = Shift::where('is_active', true)->get();
        $roles     = Role::all();
        return view('employees.edit', compact('employee', 'divisions', 'shifts', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $employee->id,
            'nik'      => 'required|string|unique:users,nik,' . $employee->id,
            'role'     => 'required|exists:roles,name',
            'status'   => 'required|in:active,inactive,resign',
            'photo'    => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['photo', 'role', '_token', '_method', 'ktp', 'npwp', 'cv', 'contract']);

        if ($request->hasFile('photo')) {
            if ($employee->photo) Storage::disk('public')->delete($employee->photo);
            $data['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        $oldData = $employee->only([
            'name', 'email', 'nik', 'division_id', 'position_id', 'shift_id', 'status'
        ]);

        $employee->update($data);
        $employee->syncRoles([$request->role]);

        $newData = $employee->only([
            'name', 'email', 'nik', 'division_id', 'position_id', 'shift_id', 'status'
        ]);

        ActivityLog::log('update', 'User', $employee->id, [
            'old' => $oldData,
            'new' => $newData
        ]);

        return redirect()->route('employees.show', $employee)->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        $details = $employee->only(['name', 'email', 'nik']);
        $employeeId = $employee->id;

        $employee->update(['status' => 'inactive']);
        $employee->delete();

        ActivityLog::log('delete', 'User', $employeeId, $details);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dinonaktifkan.');
    }
}
