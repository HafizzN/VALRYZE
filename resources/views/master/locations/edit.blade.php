@extends('layouts.app')

@section('title', 'Edit Lokasi GPS')
@section('page-title', 'Edit Lokasi Kantor')
@section('breadcrumb', 'Master Data › Lokasi GPS › Edit')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        background: var(--bg-elevated) !important;
        border: 1px solid var(--border-soft) !important;
        color: var(--t1) !important;
        border-radius: 10px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important;
    }
    .leaflet-popup-tip { background: var(--bg-elevated) !important; }
    .leaflet-control-attribution { background: rgba(7,16,26,0.7) !important; color: var(--t4) !important; }
    .leaflet-control-zoom a { background: var(--bg-elevated) !important; border-color: var(--border-soft) !important; color: var(--t2) !important; }
    .leaflet-control-zoom a:hover { background: var(--bg-hover) !important; color: var(--em) !important; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Edit Lokasi Kantor</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Perbarui koordinat geofencing dan jangkauan absensi kantor</p>
        </div>
        <a href="{{ route('master.locations.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p style="font-weight:700;font-size:0.8rem;">Terjadi kesalahan input data:</p>
                <ul style="padding-left:1rem;margin-top:0.25rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.75rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('master.locations.update', $location->id) }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf
            @method('PUT')

            {{-- Nama Lokasi --}}
            <div class="form-group">
                <label class="form-label" for="name">Nama Lokasi / Kantor <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="Contoh: Kantor Pusat Jakarta"
                    value="{{ old('name', $location->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- GPS Auto Detect Button --}}
            <div>
                <button type="button" id="btn-detect-gps" class="btn btn-secondary w-full justify-center"
                    style="background:var(--em-ghost);color:var(--em-light);border:1px solid var(--em-border);gap:0.5rem;display:flex;align-items:center;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ambil Lokasi Saat Ini (Deteksi GPS Otomatis)</span>
                </button>
            </div>

            {{-- Interactive Map Picker --}}
            <div class="form-group">
                <label class="form-label" style="margin-bottom:0.5rem;">Pilih Lokasi pada Peta Interaktif</label>
                <div id="map" class="w-full" style="height:320px;margin-bottom:0.4rem;"></div>
                <p style="font-size:0.68rem;color:var(--t4);">Klik pada peta atau geser penanda (marker) untuk menentukan koordinat kantor secara presisi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Latitude --}}
                <div class="form-group">
                    <label class="form-label" for="latitude">📍 Latitude <span style="color:var(--danger);">*</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;"
                        placeholder="-6.2088"
                        value="{{ old('latitude', $location->latitude) }}" required>
                    @error('latitude')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Longitude --}}
                <div class="form-group">
                    <label class="form-label" for="longitude">📍 Longitude <span style="color:var(--danger);">*</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;"
                        placeholder="106.8456"
                        value="{{ old('longitude', $location->longitude) }}" required>
                    @error('longitude')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Radius --}}
                <div class="form-group">
                    <label class="form-label" for="radius_meters">🎯 Radius Geofencing <span style="color:var(--danger);">*</span></label>
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        <input type="number" name="radius_meters" id="radius_meters" class="form-control"
                            style="font-family:'JetBrains Mono',monospace;"
                            placeholder="100" min="10" max="5000"
                            value="{{ old('radius_meters', $location->radius_meters) }}" required>
                        <span style="font-size:0.72rem;color:var(--t4);white-space:nowrap;">meter</span>
                    </div>
                    <p style="font-size:0.67rem;color:var(--t4);margin-top:0.25rem;">Jangkauan absen dalam meter</p>
                    @error('radius_meters')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Status Lokasi --}}
            <div class="form-group">
                <label class="form-label" for="is_active">Status Lokasi</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $location->is_active) ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="0" {{ !old('is_active', $location->is_active) ? 'selected' : '' }}>⛔ Non-Aktif</option>
                </select>
                @error('is_active')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Alamat --}}
            <div class="form-group">
                <label class="form-label" for="address">Alamat Fisik Lengkap Kantor</label>
                <textarea name="address" id="address" rows="3" class="form-control"
                    placeholder="Tuliskan alamat fisik lengkap kantor...">{{ old('address', $location->address) }}</textarea>
                @error('address')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Info tip --}}
            <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.9rem 1rem;background:var(--em-ghost);border:1px solid var(--em-border);border-radius:12px;font-size:0.76rem;color:var(--t3);">
                <svg style="width:17px;height:17px;color:var(--em);flex-shrink:0;margin-top:0.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span style="font-weight:800;color:var(--em-light);">Petunjuk Koordinat GPS: </span>
                    Gunakan tombol deteksi otomatis untuk memposisikan peta pada lokasi Anda, atau klik di mana saja pada peta di atas untuk mengubah posisi marker secara manual.
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('master.locations.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnDetect = document.getElementById('btn-detect-gps');
    const inputLat = document.getElementById('latitude');
    const inputLng = document.getElementById('longitude');
    const textareaAddress = document.getElementById('address');

    const initialLat = {{ $location->latitude }};
    const initialLng = {{ $location->longitude }};

    const map = L.map('map').setView([initialLat, initialLng], 15);

    // Dark CartoDB Basemap
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        maxZoom: 20
    }).addTo(map);

    let marker;

    function updateLocation(lat, lng, zoom = false) {
        const formattedLat = parseFloat(lat).toFixed(7);
        const formattedLng = parseFloat(lng).toFixed(7);
        inputLat.value = formattedLat;
        inputLng.value = formattedLng;

        if (marker) {
            marker.setLatLng([formattedLat, formattedLng]);
        } else {
            marker = L.marker([formattedLat, formattedLng], { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateLocation(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });
        }
        if (zoom) { map.setView([formattedLat, formattedLng], 16); }
    }

    // Initialize marker at existing coordinates
    updateLocation(initialLat, initialLng);

    map.on('click', function(e) {
        updateLocation(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data && data.display_name) { textareaAddress.value = data.display_name; }
        } catch (e) { console.error('Gagal mengambil alamat:', e); }
    }

    if (btnDetect) {
        btnDetect.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi GPS.');
                return;
            }
            const originalHTML = btnDetect.innerHTML;
            btnDetect.disabled = true;
            btnDetect.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mendeteksi Lokasi GPS...</span>
            `;
            navigator.geolocation.getCurrentPosition(
                async function(pos) {
                    updateLocation(pos.coords.latitude, pos.coords.longitude, true);
                    await reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = originalHTML;
                },
                function(err) {
                    alert('Gagal mendapatkan lokasi GPS: ' + err.message);
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = originalHTML;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }
});
</script>
@endpush
