<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    public function index()
    {
        $locations = OfficeLocation::all();
        $googleMapsKey = config('services.google_maps.key');
        return view('master.locations.index', compact('locations', 'googleMapsKey'));
    }

    public function create()
    {
        $googleMapsKey = config('services.google_maps.key');
        return view('master.locations.create', compact('googleMapsKey'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'nullable|string',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
        ]);
        $location = OfficeLocation::create($request->only(['name', 'address', 'latitude', 'longitude', 'radius_meters']));

        \App\Models\ActivityLog::log('create', 'OfficeLocation', $location->id, [
            'name' => $location->name,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ]);

        return redirect()->route('master.locations.index')->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(OfficeLocation $location)
    {
        $googleMapsKey = config('services.google_maps.key');
        return view('master.locations.edit', compact('location', 'googleMapsKey'));
    }

    public function update(Request $request, OfficeLocation $location)
    {
        $request->validate([
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'radius_meters' => 'required|integer|min:10',
        ]);

        $oldData = $location->only(['name', 'address', 'latitude', 'longitude', 'radius_meters', 'is_active']);
        $location->update($request->only(['name', 'address', 'latitude', 'longitude', 'radius_meters', 'is_active']));
        $newData = $location->only(['name', 'address', 'latitude', 'longitude', 'radius_meters', 'is_active']);

        \App\Models\ActivityLog::log('update', 'OfficeLocation', $location->id, [
            'old' => $oldData,
            'new' => $newData
        ]);

        return redirect()->route('master.locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(OfficeLocation $location)
    {
        try {
            $details = $location->only(['name', 'latitude', 'longitude']);
            $locationId = $location->id;

            $location->delete();

            \App\Models\ActivityLog::log('delete', 'OfficeLocation', $locationId, $details);

            return redirect()->route('master.locations.index')->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('master.locations.index')->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan oleh data lain (seperti data riwayat absensi karyawan).');
        }
    }
}
