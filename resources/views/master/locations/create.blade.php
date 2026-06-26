@extends('layouts.app')

@section('title', 'Tambah Lokasi GPS Baru')
@section('page-title', 'Tambah Lokasi Kantor')
@section('breadcrumb', 'Master Data / Lokasi GPS / Tambah')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map {
        background: #f1f5f9;
        border: 1px solid var(--border-color);
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800" style="color: var(--text-main);">Tambah Lokasi Kantor</h2>
            <p class="text-xs text-slate-500">Definisikan area geofencing baru untuk validasi absensi GPS karyawan</p>
        </div>
        <a href="{{ route('master.locations.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi kesalahan input data:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('master.locations.store') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Lokasi / Kantor <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Kantor Pusat Jakarta, Cabang Bandung" value="{{ old('name') }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- GPS Auto Detect Button -->
            <div style="margin-bottom: 1.25rem;">
                <button type="button" id="btn-detect-gps" class="btn btn-secondary w-full justify-center" style="background: rgba(22,163,74,0.08); color: #16a34a; border: 1px solid rgba(22,163,74,0.2); gap: 0.5rem; display: flex; align-items: center;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ambil Lokasi Saat Ini (Deteksi GPS Otomatis)</span>
                </button>
            </div>

            <!-- Interactive Map Picker -->
            <div class="form-group">
                <label class="form-label">Pilih Lokasi pada Peta Interaktif</label>
                <div id="map" class="w-full rounded-lg" style="height: 320px; z-index: 1; margin-bottom: 0.5rem;"></div>
                <p style="font-size: 0.68rem; color: var(--text-muted);">Klik pada peta atau geser penanda (marker) untuk menentukan koordinat kantor secara presisi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Latitude -->
                <div class="form-group">
                    <label class="form-label" for="latitude">Latitude Koordinat <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="Contoh: -6.2088" value="{{ old('latitude') }}" required>
                    @error('latitude')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Longitude -->
                <div class="form-group">
                    <label class="form-label" for="longitude">Longitude Koordinat <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="Contoh: 106.8456" value="{{ old('longitude') }}" required>
                    @error('longitude')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Radius -->
                <div class="form-group">
                    <label class="form-label" for="radius_meters">Radius Geofencing (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_meters" id="radius_meters" class="form-control" placeholder="Contoh: 100" min="10" max="5000" value="{{ old('radius_meters', 100) }}" required>
                    <p class="text-[9px] text-slate-500 mt-1">Jangkauan absen dalam meter</p>
                    @error('radius_meters')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="address">Alamat Fisik Lengkap Kantor</label>
                <textarea name="address" id="address" rows="3" class="form-control" placeholder="Tuliskan alamat fisik lengkap kantor...">{{ old('address') }}</textarea>
                @error('address')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Map Explanation Info -->
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-750 text-xs flex gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span class="font-semibold" style="color: var(--text-main);">Petunjuk Koordinat GPS:</span>
                    <p class="mt-0.5 text-slate-500 leading-relaxed">Gunakan tombol deteksi otomatis untuk memposisikan peta langsung pada lokasi Anda, atau klik di mana saja pada peta di atas untuk memposisikan marker secara manual.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200" style="border-top-color: var(--border-color);">
                <a href="{{ route('master.locations.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnDetect = document.getElementById('btn-detect-gps');
    const inputLat = document.getElementById('latitude');
    const inputLng = document.getElementById('longitude');
    const textareaAddress = document.getElementById('address');

    // Default center to Jakarta
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;

    // Initialize Map
    const map = L.map('map').setView([defaultLat, defaultLng], 12);

    // Add CartoDB Positron style tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        maxZoom: 20
    }).addTo(map);

    let marker;

    // Update coordinates in inputs and move/create marker
    function updateLocation(lat, lng, zoom = false) {
        const formattedLat = parseFloat(lat).toFixed(7);
        const formattedLng = parseFloat(lng).toFixed(7);

        inputLat.value = formattedLat;
        inputLng.value = formattedLng;

        if (marker) {
            marker.setLatLng([formattedLat, formattedLng]);
        } else {
            marker = L.marker([formattedLat, formattedLng], { draggable: true }).addTo(map);
            
            // Listen to marker drag event
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateLocation(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });
        }

        if (zoom) {
            map.setView([formattedLat, formattedLng], 16);
        }
    }

    // Click on map to place marker
    map.on('click', function(e) {
        updateLocation(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // Inverse geocoding function
    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            if (data && data.display_name) {
                textareaAddress.value = data.display_name;
            }
        } catch (e) {
            console.error('Gagal mengambil alamat:', e);
        }
    }

    // Detect GPS button
    if (btnDetect) {
        btnDetect.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi GPS.');
                return;
            }

            const originalText = btnDetect.innerHTML;
            btnDetect.disabled = true;
            btnDetect.innerHTML = `
                <svg class="w-4 h-4 animate-spin" style="margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Mendeteksi Lokasi GPS...</span>
            `;

            navigator.geolocation.getCurrentPosition(
                async function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;

                    updateLocation(lat, lng, true); // update inputs & marker, and zoom map
                    await reverseGeocode(lat, lng); // update address input

                    btnDetect.disabled = false;
                    btnDetect.innerHTML = originalText;
                },
                function(err) {
                    alert('Gagal mendapatkan lokasi GPS: ' + err.message);
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = originalText;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }
});
</script>
@endpush
