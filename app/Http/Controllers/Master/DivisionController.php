<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->orderBy('name')->get();
        return view('master.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('master.divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions',
            'code' => 'nullable|string|max:10|unique:divisions',
            'description' => 'nullable|string',
        ]);
        $division = Division::create($request->only(['name', 'code', 'description']));

        \App\Models\ActivityLog::log('create', 'Division', $division->id, [
            'name' => $division->name,
            'code' => $division->code
        ]);

        return redirect()->route('master.divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        return view('master.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name,' . $division->id,
            'code' => 'nullable|string|max:10|unique:divisions,code,' . $division->id,
        ]);

        $oldData = $division->only(['name', 'code', 'description', 'is_active']);
        $division->update($request->only(['name', 'code', 'description', 'is_active']));
        $newData = $division->only(['name', 'code', 'description', 'is_active']);

        \App\Models\ActivityLog::log('update', 'Division', $division->id, [
            'old' => $oldData,
            'new' => $newData
        ]);

        return redirect()->route('master.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        try {
            if ($division->users()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus divisi yang masih memiliki karyawan.');
            }

            $details = $division->only(['name', 'code']);
            $divisionId = $division->id;

            $division->delete();

            \App\Models\ActivityLog::log('delete', 'Division', $divisionId, $details);

            return redirect()->route('master.divisions.index')->with('success', 'Divisi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus divisi. Divisi ini mungkin masih terhubung dengan jabatan atau data aktif lainnya.');
        }
    }
}
