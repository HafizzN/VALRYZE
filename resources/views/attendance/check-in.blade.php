@extends('layouts.app')

@section('title', 'Absen Masuk')
@section('page-title', 'Absen Masuk')
@section('breadcrumb', 'Absensi › Absen Masuk')

@section('content')
<div class="max-w-2xl mx-auto" x-data="attendanceApp()" x-init="init()">

    {{-- Status Card --}}
    <div class="card mb-6">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-size: 0.78rem; color: #64748b;">Tanggal & Waktu</div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #e2e8f0; font-variant-numeric: tabular-nums;" x-text="currentTime"></div>
                <div style="font-size: 0.8rem; color: #94a3b8;" x-text="currentDate"></div>
            </div>
            <div class="gps-status">
                <div class="gps-dot" :class="gpsStatus"></div>
                <span style="color: #94a3b8;" x-text="gpsMessage"></span>
            </div>
        </div>
    </div>

    {{-- Main form --}}
    <form method="POST" action="{{ route('attendance.check-in.store') }}" id="check-in-form">
        @csrf
        <input type="hidden" name="latitude"  x-model="latitude">
        <input type="hidden" name="longitude" x-model="longitude">
        <input type="hidden" name="accuracy"  x-model="accuracy">
        <input type="hidden" name="address"   x-model="address">
        <input type="hidden" name="photo"     x-model="photoData">

        {{-- Shift Selection Card --}}
        <div class="card mb-6">
            <div class="form-group mb-0">
                <label class="form-label" for="shift_id" style="font-weight: 600; color: #cbd5e1;">Pilih Shift Kerja Hari Ini <span class="text-red-500">*</span></label>
                <select name="shift_id" id="shift_id" class="form-control" required style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08); color: #f1f5f9; padding: 0.75rem;">
                    <option value="" disabled {{ !old('shift_id', auth()->user()->shift_id) ? 'selected' : '' }}>-- Pilih Shift Anda --</option>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}" {{ old('shift_id', auth()->user()->shift_id) == $s->id ? 'selected' : '' }} style="background: var(--sidebar-bg); color: #f1f5f9;">
                            {{ $s->name }} ({{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[9px] text-slate-500 mt-1">Pilih shift kerja Anda untuk validasi jam masuk dan pulang hari ini</p>
                @error('shift_id')
                    <div class="form-error" style="color: #f87171; font-size: 0.72rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Camera Section --}}
            <div class="card">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Foto Selfie
                </h3>

                {{-- Camera preview --}}
                <div style="position: relative; background: #0d1117; border-radius: 12px; overflow: hidden; aspect-ratio: 4/3;">
                    <video id="camera-preview" x-show="!photoTaken" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    <canvas id="photo-canvas" x-show="photoTaken" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;"></canvas>

                    {{-- Overlay when no camera --}}
                    <div x-show="!cameraReady && !photoTaken" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem;">
                        <svg class="w-12 h-12" style="color: #475569;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                        <p style="font-size: 0.78rem; color: #64748b;" x-text="cameraMessage"></p>
                        <button type="button" @click="startCamera()" class="btn btn-secondary btn-sm">Aktifkan Kamera</button>
                    </div>

                    {{-- Countdown overlay --}}
                    <div x-show="countdown > 0" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4);">
                        <div style="font-size: 4rem; font-weight: 700; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.5);" x-text="countdown"></div>
                    </div>

                    {{-- Photo taken indicator --}}
                    <div x-show="photoTaken" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                        <span class="badge badge-success">✓ Foto Diambil</span>
                    </div>
                </div>

                {{-- Camera buttons --}}
                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                    <button type="button" @click="capturePhoto()" x-show="cameraReady && !photoTaken"
                        class="btn btn-primary flex-1" :disabled="countdown > 0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                        <span x-text="countdown > 0 ? countdown + '...' : 'Ambil Foto'"></span>
                    </button>
                    <button type="button" @click="retakePhoto()" x-show="photoTaken"
                        class="btn btn-secondary btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Ulangi
                    </button>
                </div>
            </div>

            {{-- GPS Section --}}
            <div class="card">
                <h3 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi GPS
                </h3>

                {{-- GPS info boxes --}}
                <div style="space-y: 0.75rem;">
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                        <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Latitude</div>
                        <div style="font-size: 0.85rem; font-family: monospace; color: #e2e8f0;" x-text="latitude || 'Memuat...'"></div>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                        <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Longitude</div>
                        <div style="font-size: 0.85rem; font-family: monospace; color: #e2e8f0;" x-text="longitude || 'Memuat...'"></div>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                        <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Akurasi</div>
                        <div style="font-size: 0.85rem; color: #e2e8f0;" x-text="accuracy ? accuracy + ' meter' : 'Memuat...'"></div>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                        <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Alamat</div>
                        <div style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4;" x-text="address || 'Memuat alamat...'"></div>
                    </div>

                    {{-- Distance indicator --}}
                    <div x-show="distanceInfo" style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); border-radius: 8px; padding: 0.75rem;">
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-bottom: 0.25rem;">Jarak dari Kantor</div>
                        <div style="font-size: 1.1rem; font-weight: 600;" :style="{ color: withinRadius ? '#34d399' : '#f87171' }" x-text="distanceInfo"></div>
                        <div style="font-size: 0.72rem; margin-top: 0.25rem;" :class="withinRadius ? 'text-green-400' : 'text-red-400'" x-text="withinRadius ? '✓ Dalam radius kantor' : '✗ Di luar radius kantor'"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fake GPS warning --}}
        <div x-show="fakeGpsDetected" class="alert alert-error" style="margin-top: 1rem;">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <strong>⚠️ FAKE GPS TERDETEKSI!</strong> Sistem mendeteksi penggunaan GPS palsu. Absensi tidak dapat dilakukan.
        </div>

        {{-- Submit button --}}
        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary w-full" style="padding: 0.875rem; font-size: 0.9rem; justify-content: center;"
                :disabled="!canSubmit"
                :style="{ opacity: canSubmit ? '1' : '0.5', cursor: canSubmit ? 'pointer' : 'not-allowed' }">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="submitLabel"></span>
            </button>
            <p style="font-size: 0.72rem; color: #64748b; text-align: center; margin-top: 0.5rem;">
                Pastikan GPS aktif dan Anda berada dalam radius kantor sebelum absen
            </p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function attendanceApp() {
    return {
        // GPS
        latitude: '',
        longitude: '',
        accuracy: '',
        address: '',
        gpsStatus: 'loading',
        gpsMessage: 'Memuat GPS...',
        distanceInfo: '',
        withinRadius: false,
        fakeGpsDetected: false,

        // Camera
        stream: null,
        cameraReady: false,
        cameraMessage: 'Klik untuk mengaktifkan kamera',
        photoTaken: false,
        photoData: '',
        countdown: 0,

        // Time
        currentTime: '',
        currentDate: '',

        get canSubmit() {
            return this.latitude && this.longitude && this.photoTaken && !this.fakeGpsDetected;
        },

        get submitLabel() {
            if (this.fakeGpsDetected) return '❌ GPS Palsu Terdeteksi';
            if (!this.latitude) return '⏳ Menunggu GPS...';
            if (!this.photoTaken) return '📷 Ambil Foto Terlebih Dahulu';
            return '✅ Absen Masuk Sekarang';
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.startGPS();
            this.startCamera();
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        startGPS() {
            if (!navigator.geolocation) {
                this.gpsStatus = 'error';
                this.gpsMessage = 'GPS tidak didukung browser ini';
                return;
            }

            this.gpsStatus = 'loading';
            this.gpsMessage = 'Mendapatkan lokasi...';

            navigator.geolocation.watchPosition(
                (pos) => {
                    this.latitude  = pos.coords.latitude.toFixed(7);
                    this.longitude = pos.coords.longitude.toFixed(7);
                    this.accuracy  = Math.round(pos.coords.accuracy);
                    this.gpsStatus = 'active';
                    this.gpsMessage = 'GPS Aktif';

                    // Basic fake GPS check (very high accuracy suspicious)
                    if (pos.coords.accuracy === 0 || pos.coords.accuracy < 1) {
                        this.fakeGpsDetected = true;
                    }

                    this.reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                },
                (err) => {
                    this.gpsStatus = 'error';
                    this.gpsMessage = 'GPS Error: ' + err.message;
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 5000 }
            );
        },

        async reverseGeocode(lat, lng) {
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                );
                const data = await response.json();
                this.address = data.display_name || `${lat}, ${lng}`;
            } catch (e) {
                this.address = `${lat}, ${lng}`;
            }
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                });
                const video = document.getElementById('camera-preview');
                video.srcObject = this.stream;
                this.cameraReady = true;
            } catch (e) {
                this.cameraMessage = 'Kamera tidak dapat diakses. Izinkan akses kamera.';
            }
        },

        capturePhoto() {
            if (this.countdown > 0) return;
            this.countdown = 3;
            const interval = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(interval);
                    this.takeSnapshot();
                }
            }, 1000);
        },

        takeSnapshot() {
            const video  = document.getElementById('camera-preview');
            const canvas = document.getElementById('photo-canvas');
            canvas.width  = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            this.photoData = canvas.toDataURL('image/jpeg', 0.85);
            this.photoTaken = true;
        },

        retakePhoto() {
            this.photoTaken = false;
            this.photoData = '';
        }
    }
}
</script>
@endpush
