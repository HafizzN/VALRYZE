@extends('layouts.app')

@section('title', 'Jejak Audit Sistem')
@section('page-title', 'Jejak Audit')
@section('breadcrumb', 'Pengaturan › Jejak Audit')

@section('content')
<div class="space-y-5 animate-fadeSlideIn" x-data="auditLogApp()" x-init="selectedLog = null; modalOpen = false">
    {{-- Filters & Search --}}
    <div class="card">
        <form method="GET" action="{{ route('settings.audit-logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="search">Cari Aktivitas</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama admin atau modul..." value="{{ request('search') }}">
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="action">Aksi</label>
                <select name="action" id="action" class="form-control">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Tambah (Create)</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Ubah (Update)</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Hapus (Delete)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="model_type">Modul</label>
                <select name="model_type" id="model_type" class="form-control">
                    <option value="">Semua Modul</option>
                    <option value="User"           {{ request('model_type') == 'User' ? 'selected' : '' }}>👥 Karyawan</option>
                    <option value="Division"       {{ request('model_type') == 'Division' ? 'selected' : '' }}>🏢 Divisi</option>
                    <option value="Position"       {{ request('model_type') == 'Position' ? 'selected' : '' }}>👔 Jabatan</option>
                    <option value="Shift"          {{ request('model_type') == 'Shift' ? 'selected' : '' }}>⏱ Shift Kerja</option>
                    <option value="OfficeLocation" {{ request('model_type') == 'OfficeLocation' ? 'selected' : '' }}>🗺 Lokasi GPS</option>
                    <option value="System"         {{ request('model_type') == 'System' ? 'selected' : '' }}>⚙️ Sistem (Otomatis)</option>
                </select>
            </div>

            <div style="display:flex;gap:0.5rem;">
                <button type="submit" class="btn btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'action', 'model_type']))
                    <a href="{{ route('settings.audit-logs') }}" class="btn btn-secondary justify-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 180px;">Waktu Kejadian</th>
                        <th>Administrator</th>
                        <th style="width: 120px; text-align:center;">Aksi</th>
                        <th>Modul</th>
                        <th style="width: 100px;">Data ID</th>
                        <th class="text-right" style="width: 120px;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--t4);">
                                {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') }} WIB
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <div class="avatar" style="width:28px;height:28px;font-size:0.6rem;overflow:hidden;flex-shrink:0;">
                                        @if($log->user?->photo)
                                            <img src="{{ $log->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ $log->user ? $log->user->initials : 'SYS' }}
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-size:0.8rem;font-weight:700;color:var(--t1);">{{ $log->user ? $log->user->name : 'Sistem Otomatis' }}</div>
                                        <div style="font-size:0.65rem;color:var(--t4);">{{ $log->user ? $log->user->email : 'system@valryze.com' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                @php
                                    $badge = match($log->action) {
                                        'create' => 'badge-success',
                                        'update' => 'badge-warning',
                                        'delete' => 'badge-danger',
                                        default => 'badge-gray'
                                    };
                                    $label = match($log->action) {
                                        'create' => 'TAMBAH',
                                        'update' => 'UBAH',
                                        'delete' => 'HAPUS',
                                        default => strtoupper($log->action)
                                    };
                                @endphp
                                <span class="badge {{ $badge }}" style="font-size:0.65rem;">{{ $label }}</span>
                            </td>
                            <td>
                                @php
                                    $module = match($log->model_type) {
                                        'Division' => '🏢 Divisi',
                                        'Position' => '👔 Jabatan',
                                        'Shift' => '⏱ Shift Kerja',
                                        'OfficeLocation' => '🗺 Lokasi Kantor',
                                        'User' => '👥 Karyawan',
                                        'System' => '⚙️ Sistem',
                                        default => $log->model_type
                                    };
                                @endphp
                                <span style="font-weight:700;color:var(--t2);font-size:0.8rem;">{{ $module }}</span>
                            </td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--t3);">
                                #{{ $log->model_id ?? '-' }}
                            </td>
                            <td style="text-align:right;">
                                <button 
                                    @click="selectedLog = {{ json_encode($log) }}; modalOpen = true" 
                                    class="btn btn-secondary btn-sm"
                                    style="padding:0.25rem 0.55rem;font-size:0.7rem;"
                                >
                                    Buka
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:3.5rem;color:var(--t4);">
                                <div style="font-size:2rem;margin-bottom:0.75rem;">📋</div>
                                <div style="font-weight:700;color:var(--t3);">Tidak ada log aktivitas terdeteksi</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($logs) && $logs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $logs->hasPages())
            <div style="margin-top:1.5rem;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Modal for details (Alpine.js) --}}
    <div x-show="modalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-cloak x-transition>
        <div @click.away="modalOpen = false" class="card w-full max-w-2xl max-h-[85vh] flex flex-col p-6 shadow-2xl" style="border-color:rgba(255,255,255,0.08);">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-dim);padding-bottom:0.75rem;">
                <div>
                    <h3 style="font-size:1rem;font-weight:900;color:var(--t1);">Detail Perubahan Aktivitas</h3>
                    <p style="font-size:0.7rem;color:var(--t4);margin-top:0.15rem;">
                        Oleh <span style="font-weight:700;color:var(--t2);" x-text="selectedLog?.user?.name || 'Sistem Otomatis'"></span> pada <span x-text="selectedLog ? new Date(selectedLog.created_at).toLocaleString('id-ID') : ''"></span>
                    </p>
                </div>
                <button @click="modalOpen = false" style="background:transparent;border:none;cursor:pointer;color:var(--t4);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--t4)'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Detail Contents --}}
            <div class="flex-1 overflow-y-auto py-4 space-y-4" style="scrollbar-width: thin;">
                {{-- Action info block --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:0.75rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;">
                    <div>
                        <div style="font-size:0.6rem;font-weight:800;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;">Aksi</div>
                        <div style="margin-top:0.25rem;">
                            <span class="badge" 
                                  :class="{
                                      'badge-success': selectedLog?.action === 'create',
                                      'badge-warning': selectedLog?.action === 'update',
                                      'badge-danger': selectedLog?.action === 'delete'
                                  }"
                                  x-text="selectedLog?.action?.toUpperCase() || ''"></span>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.6rem;font-weight:800;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;">Modul / ID Data</div>
                        <div style="margin-top:0.25rem;font-size:0.8rem;font-weight:700;color:var(--t1);">
                            <span x-text="selectedLog?.model_type || ''"></span>
                            <span style="color:var(--em);">#</span><span x-text="selectedLog?.model_id || ''"></span>
                        </div>
                    </div>
                </div>

                {{-- Changes Details Table --}}
                <div>
                    <div style="font-size:0.65rem;font-weight:800;color:var(--t5);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.5rem;">Rincian Parameter</div>
                    
                    {{-- Create / Delete view --}}
                    <template x-if="selectedLog?.action !== 'update'">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="selectedLog?.details">
                                        <template x-for="[key, val] in Object.entries(selectedLog.details)">
                                            <tr>
                                                <td style="font-family:'JetBrains Mono',monospace;font-size:0.72rem;font-weight:700;color:var(--t3);" x-text="formatKey(key)"></td>
                                                <td style="font-size:0.75rem;color:var(--t2);" x-text="formatVal(key, val)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    {{-- Update Old vs New view --}}
                    <template x-if="selectedLog?.action === 'update'">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th style="width: 45%;">Sebelumnya</th>
                                        <th style="width: 45%;">Menjadi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="selectedLog?.details?.new">
                                        <template x-for="[key, newVal] in Object.entries(selectedLog.details.new)">
                                            <tr :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'background: rgba(245,158,11,0.04);' : ''">
                                                <td style="font-family:'JetBrains Mono',monospace;font-size:0.72rem;font-weight:700;color:var(--t3);" x-text="formatKey(key)"></td>
                                                <td style="font-size:0.72rem;font-family:'JetBrains Mono',monospace;" 
                                                    :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'color: var(--danger); text-decoration: line-through; font-weight:700;' : 'color: var(--t4);'"
                                                    x-text="formatVal(key, selectedLog?.details?.old?.[key])"></td>
                                                <td style="font-size:0.72rem;font-family:'JetBrains Mono',monospace;"
                                                    :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'color: var(--success); font-weight:800; background: rgba(16,185,129,0.08); padding:0.15rem 0.35rem; border-radius:6px;' : 'color: var(--t2);'"
                                                    x-text="formatVal(key, newVal)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>

            <div style="border-top:1px solid var(--border-dim);padding-top:0.85rem;display:flex;justify-content:flex-end;">
                <button type="button" @click="modalOpen = false" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function auditLogApp() {
    return {
        selectedLog: null,
        modalOpen: false,

        formatKey(key) {
            const mappings = {
                'name': 'Nama',
                'email': 'Email',
                'nik': 'NIK',
                'phone': 'Nomor Telepon',
                'address': 'Alamat',
                'gender': 'Jenis Kelamin',
                'division_id': 'ID Divisi',
                'position_id': 'ID Jabatan',
                'shift_id': 'ID Shift Kerja',
                'status': 'Status',
                'start_time': 'Jam Mulai Shift',
                'end_time': 'Jam Selesai Shift',
                'late_tolerance_minutes': 'Toleransi Terlambat',
                'early_out_tolerance_minutes': 'Toleransi Pulang Cepat',
                'is_overnight': 'Shift Malam (Overnight)',
                'color': 'Warna Aksen',
                'is_active': 'Status Aktif',
                'latitude': 'Latitude Kantor',
                'longitude': 'Longitude Kantor',
                'radius_meters': 'Radius Geofence (Meter)',
                'annual_leave_quota': 'Kuota Cuti Tahunan',
                'count': 'Jumlah Karyawan Terproses',
                'force': 'Dipaksa Jalan (Force)',
                'old': 'Data Lama',
                'new': 'Data Baru'
            };
            return mappings[key] || key;
        },

        formatVal(key, val) {
            if (val === null || val === undefined) return 'KOSONG (NULL)';
            if (val === true || val === 1 || val === '1') {
                if (key === 'is_active' || key === 'status') return 'Aktif';
                if (key === 'is_overnight' || key === 'force') return 'Ya';
                return 'Aktif';
            }
            if (val === false || val === 0 || val === '0') {
                if (key === 'is_active' || key === 'status') return 'Nonaktif';
                if (key === 'is_overnight' || key === 'force') return 'Tidak';
                return 'Nonaktif';
            }
            if (key === 'gender') {
                return val === 'male' ? 'Laki-laki' : (val === 'female' ? 'Perempuan' : val);
            }
            if (key === 'late_tolerance_minutes' || key === 'early_out_tolerance_minutes') {
                return val + ' Menit';
            }
            if (key === 'radius_meters') {
                return val + ' Meter';
            }
            if (typeof val === 'object') {
                return JSON.stringify(val);
            }
            return val;
        }
    }
}
</script>
@endpush
