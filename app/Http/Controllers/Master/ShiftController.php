<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('master.shifts.index', compact('shifts'));
    }
    public function create()
    {
        return view('master.shifts.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'nullable|integer|min:0|max:120',
            'early_out_tolerance_minutes' => 'nullable|integer|min:0|max:120'
        ]);

        $data = $request->only([
            'name', 'start_time', 'end_time', 'late_tolerance_minutes', 'early_out_tolerance_minutes', 'is_overnight', 'color'
        ]);

        // Auto-detect overnight shift if end_time is earlier than start_time
        if (isset($data['start_time']) && isset($data['end_time'])) {
            if ($data['end_time'] < $data['start_time']) {
                $data['is_overnight'] = 1;
            }
        }

        $shift = Shift::create($data);

        \App\Models\ActivityLog::log('create', 'Shift', $shift->id, [
            'name' => $shift->name,
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'late_tolerance_minutes' => $shift->late_tolerance_minutes,
            'early_out_tolerance_minutes' => $shift->early_out_tolerance_minutes
        ]);

        return redirect()->route('master.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }
    public function edit(Shift $shift)
    {
        return view('master.shifts.edit', compact('shift'));
    }
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'nullable|integer|min:0|max:120',
            'early_out_tolerance_minutes' => 'nullable|integer|min:0|max:120'
        ]);

        $oldData = $shift->only([
            'name', 'start_time', 'end_time', 'late_tolerance_minutes', 'early_out_tolerance_minutes', 'is_overnight', 'color', 'is_active'
        ]);

        $data = $request->only([
            'name', 'start_time', 'end_time', 'late_tolerance_minutes', 'early_out_tolerance_minutes', 'is_overnight', 'color', 'is_active'
        ]);

        // Auto-detect overnight shift if end_time is earlier than start_time
        if (isset($data['start_time']) && isset($data['end_time'])) {
            if ($data['end_time'] < $data['start_time']) {
                $data['is_overnight'] = 1;
            }
        }

        $shift->update($data);

        $newData = $shift->only([
            'name', 'start_time', 'end_time', 'late_tolerance_minutes', 'early_out_tolerance_minutes', 'is_overnight', 'color', 'is_active'
        ]);

        \App\Models\ActivityLog::log('update', 'Shift', $shift->id, [
            'old' => $oldData,
            'new' => $newData
        ]);

        return redirect()->route('master.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }
    public function destroy(Shift $shift)
    {
        try {
            if ($shift->users()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus shift yang masih digunakan oleh karyawan.');
            }

            $details = $shift->only(['name', 'start_time', 'end_time']);
            $shiftId = $shift->id;

            $shift->delete();

            \App\Models\ActivityLog::log('delete', 'Shift', $shiftId, $details);

            return redirect()->route('master.shifts.index')->with('success', 'Shift berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus shift. Shift ini mungkin masih terhubung dengan data aktif lainnya.');
        }
    }
}
