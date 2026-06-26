<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Division;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('division')->orderBy('name')->get();
        return view('master.positions.index', compact('positions'));
    }
    public function create()
    {
        $divisions = Division::where('is_active', true)->get();
        return view('master.positions.create', compact('divisions'));
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'division_id' => 'required|exists:divisions,id', 'level' => 'required|in:staff,supervisor,manager,director']);
        $position = Position::create($request->only(['name', 'code', 'division_id', 'level', 'description']));

        \App\Models\ActivityLog::log('create', 'Position', $position->id, [
            'name' => $position->name,
            'level' => $position->level
        ]);

        return redirect()->route('master.positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }
    public function edit(Position $position)
    {
        $divisions = Division::where('is_active', true)->get();
        return view('master.positions.edit', compact('position', 'divisions'));
    }
    public function update(Request $request, Position $position)
    {
        $oldData = $position->only(['name', 'code', 'division_id', 'level', 'description', 'is_active']);
        $position->update($request->only(['name', 'code', 'division_id', 'level', 'description', 'is_active']));
        $newData = $position->only(['name', 'code', 'division_id', 'level', 'description', 'is_active']);

        \App\Models\ActivityLog::log('update', 'Position', $position->id, [
            'old' => $oldData,
            'new' => $newData
        ]);

        return redirect()->route('master.positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }
    public function destroy(Position $position)
    {
        $details = $position->only(['name', 'level']);
        $positionId = $position->id;

        $position->delete();

        \App\Models\ActivityLog::log('delete', 'Position', $positionId, $details);

        return redirect()->route('master.positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
