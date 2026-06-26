@extends('layouts.app')

@section('title', 'Jejak Audit Sistem')
@section('page-title', 'Jejak Audit')
@section('breadcrumb', 'Pengaturan / Jejak Audit')

@section('content')
<div class="space-y-6" x-data="auditLogApp()" x-init="selectedLog = null; modalOpen = false">
    <!-- Filters & Search -->
    <div class="card">
        <form method="GET" action="{{ route('settings.audit-logs') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="form-group mb-0">
                <label class="form-label" for="search">Cari Aktivitas</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama admin atau modul..." value="{{ request('search') }}">
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="action">Aksi</label>
                <select name="action" id="action" class="form-control">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create (Tambah)</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update (Ubah)</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete (Hapus)</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="model_type">Modul</label>
                <select name="model_type" id="model_type" class="form-control">
                    <option value="">Semua Modul</option>
                    <option value="User" {{ request('model_type') == 'User' ? 'selected' : '' }}>Karyawan</option>
                    <option value="Division" {{ request('model_type') == 'Division' ? 'selected' : '' }}>Divisi</option>
                    <option value="Position" {{ request('model_type') == 'Position' ? 'selected' : '' }}>Jabatan</option>
                    <option value="Shift" {{ request('model_type') == 'Shift' ? 'selected' : '' }}>Shift Kerja</option>
                    <option value="OfficeLocation" {{ request('model_type') == 'OfficeLocation' ? 'selected' : '' }}>Lokasi GPS</option>
                    <option value="System" {{ request('model_type') == 'System' ? 'selected' : '' }}>Sistem (Otomatis)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1 justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
                @if(request()->anyFilled(['search', 'action', 'model_type']))
                    <a href="{{ route('settings.audit-logs') }}" class="btn btn-secondary justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 180px;">Waktu Kejadian</th>
                        <th>Administrator</th>
                        <th style="width: 120px;">Aksi</th>
                        <th style="width: 180px;">Modul</th>
                        <th style="width: 100px;">Data ID</th>
                        <th class="text-right" style="width: 120px;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="font-mono text-xs text-slate-600">
                                {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar" style="width: 28px; height: 28px; font-size: 0.6rem;">
                                        {{ $log->user ? $log->user->initials : 'AD' }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $log->user ? $log->user->name : 'System Admin' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $log->user ? $log->user->email : 'admin@portal.com' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
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
                                <span class="badge {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td class="font-semibold text-slate-700">
                                @php
                                    $module = match($log->model_type) {
                                        'Division' => 'Divisi',
                                        'Position' => 'Jabatan',
                                        'Shift' => 'Shift Kerja',
                                        'OfficeLocation' => 'Lokasi Kantor',
                                        'User' => 'Karyawan',
                                        'System' => 'Sistem (Otomatis)',
                                        default => $log->model_type
                                    };
                                @endphp
                                {{ $module }}
                            </td>
                            <td class="font-mono text-xs text-slate-600">
                                #{{ $log->model_id ?? '-' }}
                            </td>
                            <td class="text-right">
                                <button 
                                    @click="selectedLog = {{ json_encode($log) }}; modalOpen = true" 
                                    class="btn btn-secondary btn-sm"
                                    style="padding: 0.25rem 0.5rem;"
                                >
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">
                                Tidak ada log aktivitas terdeteksi yang sesuai kriteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($logs) && $logs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $logs->hasPages())
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal for details -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-cloak>
        <div @click.away="modalOpen = false" class="card w-full max-w-2xl max-h-[85vh] flex flex-col p-6 shadow-2xl" style="border-color: rgba(255,255,255,0.08); background: var(--card-bg);">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100" style="border-bottom-color: var(--border-color);">
                <div>
                    <h3 class="text-lg font-bold text-slate-800" style="color: var(--text-main);">Detail Perubahan Aktivitas</h3>
                    <p class="text-xs text-slate-500">
                        Oleh <span class="font-semibold text-slate-700" style="color: var(--text-main);" x-text="selectedLog?.user?.name || 'System Admin'"></span> pada <span x-text="selectedLog ? new Date(selectedLog.created_at).toLocaleString('id-ID') : ''"></span>
                    </p>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Detail Contents -->
            <div class="flex-1 overflow-y-auto py-4 space-y-4" style="scrollbar-width: thin;">
                <!-- Action & Module Row -->
                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100" style="background: rgba(0,0,0,0.02); border-color: var(--border-color);">
                    <div>
                        <div class="text-[10px] text-slate-500 font-bold uppercase">Aksi</div>
                        <div class="mt-1">
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
                        <div class="text-[10px] text-slate-500 font-bold uppercase">Modul / ID Data</div>
                        <div class="mt-1 text-sm font-semibold text-slate-700" style="color: var(--text-main);">
                            <span x-text="selectedLog?.model_type || ''"></span>
                            <span class="text-slate-400">#</span><span x-text="selectedLog?.model_id || ''"></span>
                        </div>
                    </div>
                </div>

                <!-- Changes Detail -->
                <div>
                    <div class="text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">Rincian Perubahan</div>
                    
                    <!-- If Action is Create or Delete (shows flat key-values) -->
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
                                                <td class="font-mono text-xs font-semibold text-slate-600" x-text="formatKey(key)"></td>
                                                <td class="text-xs text-slate-800" style="color: var(--text-main);" x-text="formatVal(key, val)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <!-- If Action is Update (shows Old vs New side-by-side comparison) -->
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
                                            <tr :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'background-color: rgba(245,158,11,0.05);' : ''">
                                                <td class="font-mono text-xs font-semibold text-slate-600" x-text="formatKey(key)"></td>
                                                <td class="text-xs font-mono" 
                                                    :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'color: var(--danger); text-decoration: line-through; font-weight: 600;' : 'color: var(--text-muted);'"
                                                    x-text="formatVal(key, selectedLog?.details?.old?.[key])"></td>
                                                <td class="text-xs font-mono"
                                                    :style="JSON.stringify(selectedLog?.details?.old?.[key]) !== JSON.stringify(newVal) ? 'color: var(--success); font-weight: 700; background-color: rgba(22,163,74,0.1); padding: 0.1rem 0.25rem; border-radius: 4px;' : 'color: var(--text-main);'"
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

            <div class="pt-4 border-t border-slate-100 flex justify-end" style="border-top-color: var(--border-color);">
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
