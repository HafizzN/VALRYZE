@extends('layouts.app')

@section('title', 'GPS Map Visualization')
@section('page-title', 'GPS Map Laporan')
@section('breadcrumb', 'Laporan / GPS Map')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.08);
        border: 1px solid var(--border-color);
        background: #ffffff;
    }
    .leaflet-popup-tip {
        background: #ffffff;
    }
    /* Set map container background to match clean light theme */
    #map {
        background: #f1f5f9;
        border: 1px solid var(--border-color);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 600; color: var(--text-main);">Visualisasi Koordinat Presensi</h2>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">Pantau lokasi check-in harian karyawan secara visual di peta interaktif</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-6">
        <form method="GET" action="{{ route('reports.gps') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="date">Pilih Tanggal Presensi</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date }}">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan Peta
                </button>
                @if(request()->filled('date'))
                    <a href="{{ route('reports.gps') }}" class="btn btn-secondary">Hari Ini</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Map & Sidebar Grid -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;" class="lg:grid-cols-3">
        
        <!-- Map Container (Left) -->
        <div class="lg:col-span-2 space-y-3">
            <div class="card p-2" style="height: 480px; position: relative;">
                <!-- Interactive Leaflet Map Canvas -->
                <div id="map" class="w-full h-full rounded-lg" style="z-index: 1;"></div>
            </div>
        </div>

        <!-- Sidebar: Checked-in Employees List (Right) -->
        <div class="space-y-4">
            <div class="card space-y-4" style="display: flex; flex-direction: column; height: 480px; overflow: hidden; padding: 1.25rem;">
                <h3 style="font-size: 0.88rem; font-weight: 600; color: var(--text-main); border-b: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-top: 0; display: flex; align-items: center; justify-content: space-between;">
                    <span>Titik Absensi Harian</span>
                    <span class="badge badge-success font-mono">{{ $attendances->count() }} Check-in</span>
                </h3>

                <div style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; padding-right: 0.25rem;" style="scrollbar-width: thin;">
                    @forelse($attendances as $attendance)
                        <div style="padding: 0.75rem; background: var(--body-bg); border: 1px solid var(--border-color); border-radius: 8px; display: flex; flex-direction: column; gap: 0.5rem; transition: all 0.2s; cursor: pointer;" 
                             onmouseover="this.style.background='var(--table-hover-bg)'; this.style.borderColor='var(--primary-light)';" 
                             onmouseout="this.style.background='var(--body-bg)'; this.style.borderColor='var(--border-color)';" 
                             onclick="focusMarker({{ $loop->index }})">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 0;">
                                    <div class="avatar" style="width: 28px; height: 28px; font-size: 0.62rem; flex-shrink: 0; overflow: hidden;">
                                        @if($attendance->user?->photo)
                                            <img src="{{ $attendance->user->photo_url }}" alt="{{ $attendance->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            {{ $attendance->user->initials ?? 'K' }}
                                        @endif
                                    </div>
                                    <div style="min-width: 0;">
                                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $attendance->user->name }}</div>
                                        <div style="font-size: 0.65rem; color: var(--text-muted); font-family: monospace;">{{ $attendance->user->nik }}</div>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--success); font-family: monospace;">{{ $attendance->check_in_time ? substr($attendance->check_in_time, 0, 5) : '--:--' }}</div>
                                    <span style="font-size: 0.6rem; color: var(--text-muted);">WIB</span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem; font-size: 0.68rem; font-family: monospace; color: var(--text-muted); padding-top: 0.4rem; border-top: 1px solid var(--border-color);">
                                <div>Lat: <span style="color: var(--text-main);">{{ number_format($attendance->check_in_latitude, 5) }}</span></div>
                                <div>Long: <span style="color: var(--text-main);">{{ number_format($attendance->check_in_longitude, 5) }}</span></div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-size: 0.78rem; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                            <svg style="width: 2rem; height: 2rem; color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Tidak ada titik GPS presensi pada tanggal ini.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const locations = [
            @foreach($attendances as $att)
            {
                lat: {{ $att->check_in_latitude }},
                lng: {{ $att->check_in_longitude }},
                name: "{{ $att->user->name }}",
                time: "{{ $att->check_in_time ? substr($att->check_in_time, 0, 5) : '--:--' }}",
                status: "{{ $att->status }}",
                nik: "{{ $att->user->nik }}"
            },
            @endforeach
        ];

        // Center map to first coordinate or default Jakarta
        const defaultCenter = locations.length > 0 ? [locations[0].lat, locations[0].lng] : [-6.2088, 106.8456];
        const defaultZoom = locations.length > 0 ? 14 : 11;

        // Initialize Leaflet Map
        const map = L.map('map').setView(defaultCenter, defaultZoom);

        // Add CartoDB Positron (clean, premium light map)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const markers = [];

        // Add Markers
        locations.forEach((loc, index) => {
            // Modern circle marker representing check-in
            const marker = L.circleMarker([loc.lat, loc.lng], {
                radius: 8,
                fillColor: '#16a34a', // emerald green
                color: '#ffffff',     // white border
                weight: 2,
                opacity: 1,
                fillOpacity: 0.85
            }).addTo(map);

            // Bind premium styled popup
            const content = `
                <div style="color: #1f2937; font-family: 'Plus Jakarta Sans', sans-serif; min-width: 150px; font-size: 11px;">
                    <strong style="font-size: 13px; color: #111827; display: block; margin-bottom: 2px;">${loc.name}</strong>
                    <span style="color: #64748b; display: block; margin-bottom: 2px;">NIK: ${loc.nik}</span>
                    <span style="color: #64748b; display: block; margin-bottom: 4px;">Check-in: <strong style="color: #16a34a;">${loc.time} WIB</strong></span>
                    <span style="display: inline-block; padding: 1px 6px; background: #dcfce7; color: #15803d; border-radius: 10px; font-weight: bold; text-transform: uppercase; font-size: 9px;">${loc.status}</span>
                </div>
            `;
            marker.bindPopup(content);
            markers.push(marker);
        });

        // Fit map bounds to show all markers if there are check-ins
        if (locations.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Global focus marker function
        window.focusMarker = function(index) {
            if (map && markers[index]) {
                map.setView(markers[index].getLatLng(), 16);
                markers[index].openPopup();
            }
        };
    });
</script>
@endpush
