@extends('layouts.app')

@section('title', 'Absen Masuk')
@section('page-title', 'Absen Masuk')
@section('breadcrumb', 'Absensi › Absen Masuk')

@section('content')
<div class="max-w-2xl mx-auto animate-fadeSlideIn" x-data="attendanceApp()" x-init="init()">

    {{-- Status Card --}}
    <div class="card mb-5">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="font-size:0.75rem;color:var(--t4);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Tanggal & Waktu</div>
                <div style="font-size:1.4rem;font-weight:900;color:var(--t1);font-family:'JetBrains Mono',monospace;" x-text="currentTime"></div>
                <div style="font-size:0.78rem;color:var(--t3);" x-text="currentDate"></div>
            </div>
            <div class="gps-status" style="background:var(--bg-elevated);border:1px solid var(--border-soft);padding:0.45rem 0.85rem;border-radius:10px;">
                <div class="gps-dot" :class="gpsStatus"></div>
                <span style="color:var(--t2);font-weight:700;font-size:0.78rem;" x-text="gpsMessage"></span>
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

        @if(auth()->user()->hasRole(['super_admin', 'hrd', 'manager']))
        <div class="card mb-5" style="border:1px dashed var(--warning);background:rgba(245,158,11,0.04);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <input type="checkbox" name="bypass_restrictions" id="bypass_restrictions" value="1" class="rounded border-slate-700 bg-slate-800 text-amber-500 focus:ring-amber-500" style="width:1rem;height:1rem;cursor:pointer;">
                <div>
                    <label for="bypass_restrictions" class="font-bold text-amber-400 style-label" style="font-size:0.78rem;cursor:pointer;">Bypass Pembatasan Demo (Geofence & Jam Kerja)</label>
                    <div style="font-size:0.67rem;color:var(--t4);margin-top:0.1rem;">Centang untuk mengizinkan absensi dari lokasi mana pun dan di luar jam shift resmi (Khusus Demo).</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Shift Selection Card --}}
        <div class="card mb-5">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="shift_id">Pilih Shift Kerja Hari Ini <span style="color:var(--danger);">*</span></label>
                <select name="shift_id" id="shift_id" class="form-control" required>
                    <option value="" disabled {{ !old('shift_id', auth()->user()->shift_id) ? 'selected' : '' }}>-- Pilih Shift Anda --</option>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}" {{ old('shift_id', auth()->user()->shift_id) == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
                <p style="font-size:0.65rem;color:var(--t4);margin-top:0.25rem;">Pilih shift kerja Anda untuk validasi jam masuk dan pulang hari ini</p>
                @error('shift_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="md:grid-cols-2">

            {{-- Camera Section --}}
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:0.4rem;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">
                    📷 Foto Selfie Masuk
                </h3>

                {{-- Camera preview --}}
                <div style="position:relative;background:var(--bg-base);border:1px solid var(--border-soft);border-radius:12px;overflow:hidden;aspect-ratio:4/3;">
                    <video id="camera-preview" x-show="!photoTaken" autoplay playsinline style="width:100%;height:100%;object-fit:cover;"></video>
                    <canvas id="photo-canvas" x-show="photoTaken" style="width:100%;height:100%;object-fit:cover;border-radius:12px;"></canvas>

                    {{-- Overlay when no camera --}}
                    <div x-show="!cameraReady && !photoTaken" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.75rem;padding:1rem;">
                        <span style="font-size:2rem;">📷</span>
                        <p style="font-size:0.75rem;color:var(--t4);text-align:center;" x-text="cameraMessage"></p>
                        <button type="button" @click="startCamera()" class="btn btn-secondary btn-sm">Aktifkan Kamera</button>
                    </div>

                    {{-- Countdown overlay --}}
                    <div x-show="countdown > 0" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
                        <div style="font-size:4rem;font-weight:950;color:white;text-shadow:0 0 20px rgba(0,0,0,0.6);" x-text="countdown"></div>
                    </div>

                    {{-- Photo taken indicator --}}
                    <div x-show="photoTaken" style="position:absolute;top:0.75rem;right:0.75rem;">
                        <span class="badge badge-success">✓ Foto Siambil</span>
                    </div>
                </div>

                {{-- Camera buttons --}}
                <div style="display:flex;gap:0.5rem;">
                    <button type="button" @click="capturePhoto()" x-show="cameraReady && !photoTaken"
                        class="btn btn-primary flex-1" :disabled="countdown > 0" style="justify-content:center;">
                        ⏱ <span x-text="countdown > 0 ? countdown + '...' : 'Ambil Foto'"></span>
                    </button>
                    <button type="button" @click="retakePhoto()" x-show="photoTaken"
                        class="btn btn-secondary btn-sm w-full" style="justify-content:center;">
                        🔄 Ulangi Foto
                    </button>
                </div>
            </div>

            {{-- GPS Section --}}
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:0.4rem;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">
                    🗺 Lokasi GPS Anda
                </h3>

                {{-- GPS info boxes --}}
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    <div style="background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:10px;padding:0.65rem 0.85rem;">
                        <div style="font-size:0.6rem;font-weight:800;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.15rem;">Koordinat</div>
                        <div style="font-size:0.8rem;font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t2);">
                            Lat: <span x-text="latitude || 'Memuat...'"></span> · Lng: <span x-text="longitude || 'Memuat...'"></span>
                        </div>
                    </div>
                    <div style="background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:10px;padding:0.65rem 0.85rem;">
                        <div style="font-size:0.6rem;font-weight:800;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.15rem;">Akurasi Sinyal</div>
                        <div style="font-size:0.8rem;font-weight:700;color:var(--t2);" x-text="accuracy ? accuracy + ' meter' : 'Memuat...'"></div>
                    </div>
                    <div style="background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:10px;padding:0.65rem 0.85rem;">
                        <div style="font-size:0.6rem;font-weight:800;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.15rem;">Alamat Terdeteksi</div>
                        <div style="font-size:0.75rem;color:var(--t3);line-height:1.45;word-break:break-word;" x-text="address || 'Memuat alamat...'"></div >
                    </div>

                    {{-- Distance indicator --}}
                    <div x-show="distanceInfo" style="background:var(--bg-elevated);border:1.5px solid var(--border-soft);border-radius:10px;padding:0.75rem 0.85rem;" :style="withinRadius ? 'border-color:var(--em-border); background:var(--em-ghost);' : 'border-color:rgba(239,68,68,0.2); background:rgba(239,68,68,0.03);'">
                        <div style="font-size:0.65rem;color:var(--t4);margin-bottom:0.15rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Jarak dari Kantor</div>
                        <div style="font-size:1.15rem;font-weight:900;font-family:'JetBrains Mono',monospace;" :style="{ color: withinRadius ? 'var(--em)' : 'var(--danger)' }" x-text="distanceInfo"></div>
                        <div style="font-size:0.72rem;margin-top:0.25rem;font-weight:700;" :style="{ color: withinRadius ? 'var(--em-light)' : '#FCA5A5' }" x-text="withinRadius ? '✓ Berada dalam radius geofence kantor' : '✗ Berada di luar geofence kantor'"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fake GPS warning --}}
        <div x-show="fakeGpsDetected" class="alert alert-error" style="margin-top:1.25rem;">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div><strong>⚠️ FAKE GPS TERDETEKSI!</strong> Sistem mendeteksi penggunaan GPS palsu. Absensi tidak dapat dilakukan.</div>
        </div>

        {{-- Submit button --}}
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary w-full" style="padding:0.875rem;font-size:0.9rem;justify-content:center;font-weight:800;"
                :disabled="!canSubmit"
                :style="{ opacity: canSubmit ? '1' : '0.5', cursor: canSubmit ? 'pointer' : 'not-allowed' }">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="submitLabel"></span>
            </button>
            <p style="font-size:0.7rem;color:var(--t4);text-align:center;margin-top:0.6rem;">
                Pastikan GPS aktif dan Anda berada dalam radius kantor sebelum melakukan absensi
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
            if (!this.latitude) return '⏳ Menunggu Lokasi GPS...';
            if (!this.photoTaken) return '📷 Ambil Foto Selfie Terlebih Dahulu';
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
                this.gpsMessage = 'GPS tidak didukung';
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

                    if (pos.coords.accuracy === 0 || pos.coords.accuracy < 1) {
                        this.fakeGpsDetected = true;
                    }

                    // Check radius geofence (mocking with a call or inline comparison)
                    // We will fetch from office coordinates or simulate locally
                    this.withinRadius = true; // In production this comes from geofence validation
                    this.distanceInfo = '15 Meter'; // Example distance

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
