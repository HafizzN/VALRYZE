@extends('layouts.app')

@section('title', 'Absen Pulang')
@section('page-title', 'Absen Pulang')
@section('breadcrumb', 'Absensi › Absen Pulang')

@section('content')
<div class="max-w-2xl mx-auto animate-fadeSlideIn" x-data="checkOutApp()" x-init="init()">

    {{-- Already checked out --}}
    @if($attendance && $attendance->check_out_time)
    <div class="card text-center" style="padding:3rem 2rem;display:flex;flex-direction:column;align-items:center;gap:1rem;">
        <div style="width:70px;height:70px;background:var(--em-ghost);border:1.5px solid var(--em-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin-bottom:0.5rem;">
            ✓
        </div>
        <h3 style="font-size:1.15rem;font-weight:900;color:var(--t1);">Sudah Absen Pulang</h3>
        <p style="color:var(--t4);font-size:0.78rem;max-width:320px;">Anda telah melakukan absensi pulang untuk shift hari ini pada pukul</p>
        <div style="font-size:2.2rem;font-weight:900;color:var(--em);font-family:'JetBrains Mono',monospace;margin:0.25rem 0;">
            {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }} WIB
        </div>
        <div style="font-size:0.75rem;color:var(--t4);background:var(--bg-elevated);border:1px solid var(--border-soft);padding:0.45rem 1rem;border-radius:20px;">
            Masuk: <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t2);">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }} WIB</span>
            &nbsp;·&nbsp;
            Durasi: <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--em-light);">{{ $attendance->work_duration ?? '-' }}</span>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top:0.75rem;">Kembali ke Dashboard</a>
    </div>
    @elseif(!$attendance)
    <div class="alert alert-error" style="display:flex;flex-direction:column;gap:0.75rem;align-items:center;text-align:center;padding:2.5rem 1.5rem;">
        <span style="font-size:2rem;">⚠️</span>
        <div style="font-weight:800;font-size:0.9rem;">Anda belum absen masuk hari ini!</div>
        <p style="font-size:0.78rem;opacity:0.85;max-width:320px;">Silakan lakukan absen masuk terlebih dahulu sebelum mengakses menu absen pulang.</p>
        <a href="{{ route('attendance.check-in') }}" class="btn btn-primary" style="margin-top:0.5rem;box-shadow:none;">Absen Masuk Sekarang</a>
    </div>
    @else

    {{-- Status Card --}}
    <div class="card mb-5">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <div style="font-size:0.75rem;color:var(--t4);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Tanggal & Waktu</div>
                <div style="font-size:1.4rem;font-weight:900;color:var(--t1);font-family:'JetBrains Mono',monospace;" x-text="currentTime"></div>
                <div style="font-size:0.78rem;color:var(--t3);" x-text="currentDate"></div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:0.65rem;color:var(--t4);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Absen Masuk</div>
                <div style="font-size:1.05rem;font-weight:800;color:var(--em);font-family:'JetBrains Mono',monospace;">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }} WIB</div>
                <div style="font-size:0.65rem;color:var(--t4);margin-top:0.1rem;">Durasi Kerja: <span x-text="workDuration" style="font-family:'JetBrains Mono',monospace;font-weight:700;color:#C4B5FD;"></span></div>
            </div>
            <div class="gps-status" style="background:var(--bg-elevated);border:1px solid var(--border-soft);padding:0.45rem 0.85rem;border-radius:10px;">
                <div class="gps-dot" :class="gpsStatus"></div>
                <span style="color:var(--t2);font-weight:700;font-size:0.78rem;" x-text="gpsMessage"></span>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasRole(['super_admin', 'hrd', 'manager']))
    <div class="card mb-5" style="border:1px dashed var(--warning);background:rgba(245,158,11,0.04);">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <input type="checkbox" name="bypass_restrictions" id="bypass_restrictions" value="1" class="rounded border-slate-700 bg-slate-800 text-amber-500 focus:ring-amber-500" style="width:1rem;height:1rem;cursor:pointer;">
            <div>
                <label for="bypass_restrictions" class="font-bold text-amber-400 style-label" style="font-size:0.78rem;cursor:pointer;">Bypass Pembatasan Demo (Geofence & Jam Kerja)</label>
                <div style="font-size:0.67rem;color:var(--t4);margin-top:0.1rem;">Centang untuk mengizinkan absensi dari lokasi mana pun dan sebelum shift resmi berakhir (Khusus Demo).</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main form --}}
    <form method="POST" action="{{ route('attendance.check-out.store') }}" id="check-out-form">
        @csrf
        <input type="hidden" name="latitude"  x-model="latitude">
        <input type="hidden" name="longitude" x-model="longitude">
        <input type="hidden" name="accuracy"  x-model="accuracy">
        <input type="hidden" name="address"   x-model="address">
        <input type="hidden" name="photo"     x-model="photoData">

        <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="md:grid-cols-2">
            {{-- Camera Section --}}
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:0.4rem;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">
                    📷 Foto Selfie Pulang
                </h3>
                <div style="position:relative;background:var(--bg-base);border:1px solid var(--border-soft);border-radius:12px;overflow:hidden;aspect-ratio:4/3;">
                    <video id="camera-preview" x-show="!photoTaken" autoplay playsinline style="width:100%;height:100%;object-fit:cover;"></video>
                    <canvas id="photo-canvas" x-show="photoTaken" style="width:100%;height:100%;object-fit:cover;border-radius:12px;"></canvas>
                    <div x-show="!cameraReady && !photoTaken" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.75rem;padding:1rem;">
                        <span style="font-size:2rem;">📷</span>
                        <p style="font-size:0.75rem;color:var(--t4);text-align:center;" x-text="cameraMessage"></p>
                        <button type="button" @click="startCamera()" class="btn btn-secondary btn-sm">Aktifkan Kamera</button>
                    </div>
                    <div x-show="countdown > 0" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);">
                        <div style="font-size:4rem;font-weight:950;color:white;text-shadow:0 0 20px rgba(0,0,0,0.6);" x-text="countdown"></div>
                    </div>
                    <div x-show="photoTaken" style="position:absolute;top:0.75rem;right:0.75rem;">
                        <span class="badge badge-success">✓ Foto Diambil</span>
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <button type="button" @click="capturePhoto()" x-show="cameraReady && !photoTaken" class="btn btn-primary flex-1" :disabled="countdown > 0" style="justify-content:center;">
                        ⏱ <span x-text="countdown > 0 ? countdown + '...' : 'Ambil Foto'"></span>
                    </button>
                    <button type="button" @click="retakePhoto()" x-show="photoTaken" class="btn btn-secondary btn-sm w-full" style="justify-content:center;">
                        🔄 Ulangi Foto
                    </button>
                </div>
            </div>

            {{-- GPS Section --}}
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:0.4rem;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">
                    🗺 Lokasi GPS Anda
                </h3>
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
                </div>
            </div>
        </div>

        {{-- Fake GPS warning --}}
        <div x-show="fakeGpsDetected" class="alert alert-error" style="margin-top:1.25rem;">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div><strong>⚠️ FAKE GPS TERDETEKSI!</strong> Sistem mendeteksi penggunaan GPS palsu. Absensi tidak dapat dilakukan.</div>
        </div>

        {{-- Submit --}}
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary w-full" style="padding:0.875rem;font-size:0.9rem;justify-content:center;background:linear-gradient(135deg, var(--danger), #B91C1C);box-shadow:0 4px 18px rgba(239,68,68,0.25);font-weight:800;"
                :disabled="!canSubmit"
                :style="{ opacity: canSubmit ? '1' : '0.5', cursor: canSubmit ? 'pointer' : 'not-allowed' }">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                <span x-text="submitLabel"></span>
            </button>
            <p style="font-size:0.7rem;color:var(--t4);text-align:center;margin-top:0.6rem;">
                Pastikan GPS aktif sebelum melakukan absen pulang
            </p>
        </div>
    </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
function checkOutApp() {
    return {
        latitude: '', longitude: '', accuracy: '', address: '',
        gpsStatus: 'loading', gpsMessage: 'Memuat GPS...',
        fakeGpsDetected: false,
        stream: null, cameraReady: false,
        cameraMessage: 'Klik untuk mengaktifkan kamera',
        photoTaken: false, photoData: '', countdown: 0,
        currentTime: '', currentDate: '', workDuration: '',
        checkInTime: '{{ $attendance ? $attendance->check_in_time : "" }}',

        get canSubmit() { return this.latitude && this.longitude && this.photoTaken && !this.fakeGpsDetected; },
        get submitLabel() {
            if (this.fakeGpsDetected) return '❌ GPS Palsu Terdeteksi';
            if (!this.latitude) return '⏳ Menunggu Lokasi GPS...';
            if (!this.photoTaken) return '📷 Ambil Foto Selfie Terlebih Dahulu';
            return '✅ Absen Pulang Sekarang';
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.startGPS();
            this.startCamera();
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'}) + ' WIB';
            this.currentDate = now.toLocaleDateString('id-ID', {weekday:'long',year:'numeric',month:'long',day:'numeric'});
            if (this.checkInTime) {
                const checkIn = new Date(this.checkInTime);
                const diff = Math.floor((now - checkIn) / 60000);
                const h = Math.floor(diff / 60), m = diff % 60;
                this.workDuration = h + 'j ' + m + 'm';
            }
        },

        startGPS() {
            if (!navigator.geolocation) { this.gpsStatus = 'error'; this.gpsMessage = 'GPS tidak didukung'; return; }
            navigator.geolocation.watchPosition(
                (pos) => {
                    this.latitude  = pos.coords.latitude.toFixed(7);
                    this.longitude = pos.coords.longitude.toFixed(7);
                    this.accuracy  = Math.round(pos.coords.accuracy);
                    this.gpsStatus = 'active'; this.gpsMessage = 'GPS Aktif';
                    if (pos.coords.accuracy === 0 || pos.coords.accuracy < 1) this.fakeGpsDetected = true;
                    this.reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                },
                (err) => { this.gpsStatus = 'error'; this.gpsMessage = 'GPS Error: ' + err.message; },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 5000 }
            );
        },

        async reverseGeocode(lat, lng) {
            try {
                const r = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const d = await r.json();
                this.address = d.display_name || `${lat}, ${lng}`;
            } catch(e) { this.address = `${lat}, ${lng}`; }
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:{ideal:640},height:{ideal:480}}});
                document.getElementById('camera-preview').srcObject = this.stream;
                this.cameraReady = true;
            } catch(e) { this.cameraMessage = 'Kamera tidak dapat diakses.'; }
        },

        capturePhoto() {
            if (this.countdown > 0) return;
            this.countdown = 3;
            const iv = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) { clearInterval(iv); this.takeSnapshot(); }
            }, 1000);
        },

        takeSnapshot() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('photo-canvas');
            canvas.width = video.videoWidth || 640; canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0);
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
