@extends('layouts.app')

@section('title', 'Detail Surat — ' . $letter->letter_number)
@section('page-title', 'Detail Surat')
@section('breadcrumb', 'Dokumen › Surat Menyurat › Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 animate-fadeSlideIn" x-data="{ rejectModalOpen: false }">
    {{-- Header Actions --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">{{ $letter->subject }}</h2>
            <p style="font-size:0.75rem;color:var(--t4);margin-top:0.25rem;">Nomor Surat: <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--em);">{{ $letter->letter_number }}</span></p>
        </div>
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <a href="{{ route('letters.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            
            @if($letter->user_id == auth()->id() && $letter->status == 'pending')
                <a href="{{ route('letters.edit', $letter->id) }}" class="btn btn-secondary btn-sm" style="color:#F59E0B;">Edit</a>
            @endif

            <a href="{{ route('letters.download', $letter->id) }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>

            @if($letter->user_id == auth()->id() || auth()->user()->hasRole(['super_admin', 'hrd']))
                <form action="{{ route('letters.destroy', $letter->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="lg:grid-cols-3">
        <!-- Letter Details (Left) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.72rem;font-weight:800;color:var(--t5);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">Isi Surat</h3>
                
                {{-- Paper Layout inside dark theme --}}
                <div style="background:var(--bg-elevated);border:1px solid var(--border-soft);padding:1.5rem;border-radius:12px;font-family:'Plus Jakarta Sans',sans-serif;font-size:0.85rem;line-height:1.6;color:var(--t2);display:flex;flex-direction:column;gap:1.5rem;min-height:350px;">
                    <!-- Letter Header -->
                    <div style="text-align:center;border-bottom:1px solid var(--border-dim);padding-bottom:1rem;display:flex;flex-direction:column;gap:0.15rem;">
                        <div style="font-weight:900;font-size:0.95rem;color:var(--t1);letter-spacing:0.04em;">{{ config('app.name', 'VALRYZE SMART HR PORTAL') }}</div>
                        <div style="font-size:0.67rem;color:var(--t4);">Sistem Manajemen Sumber Daya Manusia & Absensi</div>
                    </div>

                    <!-- Date & Number -->
                    <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:var(--t4);font-family:'JetBrains Mono',monospace;">
                        <div>No: {{ $letter->letter_number }}</div>
                        <div>Tanggal: {{ $letter->created_at->translatedFormat('d F Y') }}</div>
                    </div>

                    <!-- Content Body -->
                    <div style="white-space:pre-wrap;color:var(--t2);font-size:0.85rem;line-height:1.65;flex:1;">
                        @if($letter->content)
                            {{ $letter->content }}
                        @else
                            <span style="color:var(--t4);font-style:italic;">(Tidak ada detail isi surat tertulis. Silakan unduh PDF atau lampiran untuk melihat dokumen penuh)</span>
                        @endif
                    </div>

                    <!-- Signature Placeholder -->
                    <div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-top:2rem;">
                        <div>
                            <p style="color:var(--t4);">Pengaju,</p>
                            <div style="height:2.5rem;"></div>
                            <p style="font-weight:800;color:var(--t1);">{{ $letter->user->name ?? 'Pemohon' }}</p>
                            <p style="color:var(--t4);font-size:0.65rem;">{{ $letter->user->position->name ?? 'Karyawan' }}</p>
                        </div>
                        @if($letter->status == 'approved')
                        <div style="text-align:right;">
                            <p style="color:var(--t4);">Disetujui Oleh,</p>
                            <div style="height:2.2rem;display:flex;align-items:center;justify-content:flex-end;">
                                <span style="font-size:0.58rem;padding:0.1rem 0.4rem;border:1px solid rgba(16,185,129,0.3);background:var(--em-ghost);color:var(--em-light);border-radius:4px;font-weight:700;font-family:'JetBrains Mono',monospace;">DIGITALLY SIGNED</span>
                            </div>
                            <p style="font-weight:800;color:var(--t1);">{{ $letter->approvedBy->name ?? 'Manager / HRD' }}</p>
                            <p style="color:var(--t4);font-size:0.65rem;font-family:'JetBrains Mono',monospace;">{{ $letter->approved_at ? $letter->approved_at->format('d M Y H:i') : '' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($letter->file_path)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;gap:0.75rem;margin-top:0.5rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <span style="font-size:1.5rem;">📄</span>
                            <div>
                                <div style="font-size:0.8rem;font-weight:800;color:var(--t1);">Lampiran Dokumen PDF</div>
                                <div style="font-size:0.67rem;color:var(--t4);">Lihat berkas lampiran pendukung asli yang diunggah</div>
                            </div>
                        </div>
                        <a href="{{ Storage::url($letter->file_path) }}" target="_blank" class="btn btn-secondary btn-sm">Buka File</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Info (Right) -->
        <div class="space-y-4">
            <div class="card" style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.72rem;font-weight:800;color:var(--t5);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.1rem;">Informasi Surat</h3>
                
                <div style="display:flex;flex-direction:column;gap:0.85rem;font-size:0.76rem;">
                    <div>
                        <div style="color:var(--t4);">Tipe / Kategori</div>
                        <div style="font-weight:700;color:var(--t2);margin-top:0.15rem;">{{ $letter->letter_type_name }}</div>
                    </div>
                    <div>
                        <div style="color:var(--t4);">Tanggal Pengajuan</div>
                        <div style="font-weight:700;color:var(--t2);margin-top:0.15rem;font-family:'JetBrains Mono',monospace;">{{ $letter->created_at->format('d M Y - H:i') }} WIB</div>
                    </div>
                    <div>
                        <div style="color:var(--t4);">Status Persetujuan</div>
                        <div style="margin-top:0.35rem;">
                            @switch($letter->status)
                                @case('approved') <span class="badge badge-success">Disetujui</span> @break
                                @case('rejected') <span class="badge badge-danger">Ditolak</span> @break
                                @default          <span class="badge badge-warning">Menunggu</span>
                            @endswitch
                        </div>
                    </div>

                    @if($letter->status == 'rejected' && $letter->notes)
                        <div style="padding:0.75rem;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.22);border-radius:10px;color:#FCA5A5;margin-top:0.25rem;">
                            <div style="font-weight:800;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.04em;">Catatan Penolakan:</div>
                            <div style="margin-top:0.2rem;font-family:'JetBrains Mono',monospace;font-size:0.72rem;line-height:1.4;">{{ $letter->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Box for Admin / HRD -->
            @if($letter->status == 'pending' && auth()->user()->hasRole(['super_admin', 'hrd']))
                <div class="card" style="border-color:var(--em-border);background:var(--em-ghost);display:flex;flex-direction:column;gap:0.75rem;">
                    <h3 style="font-size:0.85rem;font-weight:800;color:var(--em-light);">Persetujuan HRD / Admin</h3>
                    <p style="font-size:0.72rem;color:var(--t3);line-height:1.45;">Silakan evaluasi pengajuan surat ini dan tentukan keputusan Anda.</p>
                    
                    <div style="display:flex;gap:0.5rem;margin-top:0.25rem;">
                        <form action="{{ route('letters.approve', $letter->id) }}" method="POST" style="flex:1;">
                            @csrf
                            <button type="submit" class="btn btn-success w-full justify-center btn-sm">Setujui</button>
                        </form>
                        <button @click="rejectModalOpen = true" class="btn btn-danger flex-1 justify-center btn-sm">Tolak</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Rejection Modal -->
    <div x-show="rejectModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display:none;" x-cloak x-transition>
        <div @click.away="rejectModalOpen = false" class="card w-full max-w-md shadow-2xl" style="border-color:rgba(255,255,255,0.08);display:flex;flex-direction:column;gap:1rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;">
                <h3 style="font-size:0.9rem;font-weight:900;color:var(--t1);">Masukkan Alasan Penolakan</h3>
                <button @click="rejectModalOpen = false" style="background:transparent;border:none;cursor:pointer;color:var(--t4);" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--t4)'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('letters.reject', $letter->id) }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="notes">Alasan / Catatan Penolakan <span style="color:var(--danger);">*</span></label>
                    <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Tuliskan mengapa surat ini ditolak..." required></textarea>
                </div>
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.5rem;padding-top:0.75rem;border-top:1px solid var(--border-dim);">
                    <button type="button" @click="rejectModalOpen = false" class="btn btn-secondary btn-sm">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
