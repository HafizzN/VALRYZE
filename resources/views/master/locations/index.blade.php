@extends('layouts.app')

@section('title', 'Master Data Lokasi GPS')
@section('page-title', 'Lokasi Kantor')
@section('breadcrumb', 'Master Data / Lokasi GPS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Lokasi Geofencing</h2>
            <p class="text-xs text-slate-500">Kelola titik koordinat kantor dan radius presensi GPS (Geofencing) untuk absen masuk/pulang</p>
        </div>
        <div>
            <a href="{{ route('master.locations.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Lokasi
            </a>
        </div>
    </div>

    <!-- Locations Table Card -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Lokasi</th>
                        <th>Alamat</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Radius Geofencing</th>
                        <th>Status</th>
                        <th class="text-right" style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td class="font-bold text-slate-800">
                                {{ $location->name }}
                            </td>
                            <td class="text-xs text-slate-600 max-w-xs truncate">
                                {{ $location->address ?? '-' }}
                            </td>
                            <td class="font-mono text-xs text-slate-700">{{ number_format($location->latitude, 6) }}</td>
                            <td class="font-mono text-xs text-slate-700">{{ number_format($location->longitude, 6) }}</td>
                            <td class="font-mono text-xs font-semibold text-emerald-700">
                                {{ $location->radius_meters }} meter
                            </td>
                            <td>
                                @if($location->is_active ?? true)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('master.locations.edit', $location->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-500">
                                Belum ada lokasi kantor terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($locations) && $locations instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $locations->hasPages())
            <div class="mt-4">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
