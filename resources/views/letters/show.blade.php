@extends('layouts.app')

@section('title', 'Detail Surat — ' . $letter->letter_number)
@section('page-title', 'Detail Surat')
@section('breadcrumb', 'Dokumen / Surat Menyurat / Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ rejectModalOpen: false }">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $letter->subject }}</h2>
            <p class="text-xs text-slate-500">Nomer Surat: <span class="text-emerald-700 font-mono">{{ $letter->letter_number }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('letters.index') }}" class="btn btn-secondary btn-sm">
                Kembali
            </a>
            
            @if($letter->user_id == auth()->id() && $letter->status == 'pending')
                <a href="{{ route('letters.edit', $letter->id) }}" class="btn btn-secondary btn-sm text-amber-600">
                    Edit
                </a>
            @endif

            <a href="{{ route('letters.download', $letter->id) }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>

            @if($letter->user_id == auth()->id() || auth()->user()->hasRole(['super_admin', 'hrd']))
                <form action="{{ route('letters.destroy', $letter->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Letter Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card space-y-4">
                <h3 class="text-sm font-semibold text-slate-600 border-b border-slate-200 pb-2">Isi Surat</h3>
                
                <!-- Simple formatted paper layout inside dark theme -->
                <div class="bg-slate-100/40 border border-slate-200/60 p-6 rounded-lg font-mono text-sm leading-relaxed text-slate-700 space-y-6 min-h-[350px]">
                    <!-- Letter Header -->
                    <div class="text-center border-b border-slate-200 pb-4 space-y-1">
                        <div class="font-bold text-base text-slate-800 uppercase">{{ config('app.name', 'SMART HR PORTAL') }}</div>
                        <div class="text-xs text-slate-500">Sistem Manajemen Sumber Daya Manusia & Absensi</div>
                    </div>

                    <!-- Date & Number -->
                    <div class="flex justify-between text-xs">
                        <div>No: {{ $letter->letter_number }}</div>
                        <div>Tanggal: {{ $letter->created_at->format('d F Y') }}</div>
                    </div>

                    <!-- Content Body -->
                    <div class="py-4 whitespace-pre-wrap font-sans text-slate-700">
                        @if($letter->content)
                            {{ $letter->content }}
                        @else
                            <span class="text-slate-600 italic">(Tidak ada detail isi surat tertulis. Silakan download PDF atau lampiran untuk melihat dokumen penuh)</span>
                        @endif
                    </div>

                    <!-- Signature Placeholder -->
                    <div class="pt-8 flex justify-between text-xs font-sans">
                        <div>
                            <p class="text-slate-500">Pengaju,</p>
                            <div class="h-12"></div>
                            <p class="font-semibold text-slate-700">{{ $letter->user->name ?? 'Pemohon' }}</p>
                            <p class="text-slate-500 text-[10px]">{{ $letter->user->position->name ?? 'Karyawan' }}</p>
                        </div>
                        @if($letter->status == 'approved')
                        <div class="text-right">
                            <p class="text-slate-500">Disetujui Oleh,</p>
                            <div class="h-6 flex items-center justify-end">
                                <span class="text-[9px] px-2 py-0.5 border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 rounded">DIGITALLY SIGNED</span>
                            </div>
                            <p class="font-semibold text-slate-700 mt-2">{{ $letter->approvedBy->name ?? 'Manager / HRD' }}</p>
                            <p class="text-slate-500 text-[10px]">{{ $letter->approved_at ? $letter->approved_at->format('d M Y H:i') : '' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($letter->file_path)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-8 h-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <div class="text-xs font-semibold text-slate-700">Lampiran Dokumen PDF</div>
                                <div class="text-[10px] text-slate-500">Lihat lampiran pendukung asli yang diunggah</div>
                            </div>
                        </div>
                        <a href="{{ Storage::url($letter->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">
                            Buka File
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Details & Approval Actions -->
        <div class="space-y-6">
            <!-- Info Card -->
            <div class="card space-y-4">
                <h3 class="text-sm font-semibold text-slate-600 border-b border-slate-200 pb-2">Informasi Surat</h3>
                
                <div class="space-y-3 text-xs">
                    <div>
                        <div class="text-slate-500">Tipe / Kategori</div>
                        <div class="font-semibold text-slate-700 mt-0.5">{{ $letter->letter_type_name }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Tanggal Pengajuan</div>
                        <div class="font-semibold text-slate-700 mt-0.5">{{ $letter->created_at->format('d M Y - H:i') }} WIB</div>
                    </div>
                    <div>
                        <div class="text-slate-500">Status Kelayakan</div>
                        <div class="mt-1">
                            @switch($letter->status)
                                @case('approved')
                                    <span class="badge badge-success">Disetujui</span>
                                    @break
                                @case('rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @break
                                @default
                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                            @endswitch
                        </div>
                    </div>

                    @if($letter->status == 'rejected' && $letter->notes)
                        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
                            <div class="font-semibold">Catatan Penolakan:</div>
                            <div class="mt-1 font-mono text-[11px]">{{ $letter->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Box for Admin / HRD -->
            @if($letter->status == 'pending' && auth()->user()->hasRole(['super_admin', 'hrd']))
                <div class="card border-emerald-200 bg-emerald-50 space-y-4">
                    <h3 class="text-sm font-semibold text-slate-700">Persetujuan HRD / Admin</h3>
                    <p class="text-xs text-slate-600">Silakan evaluasi pengajuan surat ini dan tentukan keputusan Anda.</p>
                    
                    <div class="flex gap-2">
                        <!-- Approve Form -->
                        <form action="{{ route('letters.approve', $letter->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="btn btn-success w-full justify-center btn-sm">
                                Setujui
                            </button>
                        </form>

                        <!-- Open Reject Modal Trigger -->
                        <button @click="rejectModalOpen = true" class="btn btn-danger flex-1 justify-center btn-sm">
                            Tolak
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Rejection Modal -->
    <div x-show="rejectModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="rejectModalOpen = false" class="card w-full max-w-md bg-slate-50 border border-slate-200 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <h3 class="text-sm font-bold text-slate-800">Masukkan Alasan Penolakan</h3>
                <button @click="rejectModalOpen = false" class="text-slate-500 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('letters.reject', $letter->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="notes">Alasan / Catatan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Tuliskan mengapa surat ini ditolak..." required></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                    <button type="button" @click="rejectModalOpen = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
