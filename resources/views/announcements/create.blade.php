@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru')
@section('page-title', 'Buat Pengumuman')
@section('breadcrumb', 'Dokumen › Pengumuman › Buat')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);">📢 Publikasikan Pengumuman</h2>
            <p style="font-size:0.75rem;color:var(--t4);margin-top:0.2rem;">Buat informasi baru untuk disebarkan kepada seluruh karyawan</p>
        </div>
        <a href="{{ route('announcements.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1.25rem;">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <div style="font-weight:700;">Terdapat kesalahan:</div>
            <ul style="margin-top:0.25rem;padding-left:1rem;">
                @foreach($errors->all() as $e)<li style="font-size:0.78rem;">{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <div class="form-group">
                <label class="form-label" for="title">Judul Pengumuman <span style="color:var(--danger);">*</span></label>
                <input type="text" name="title" id="title" class="form-control"
                       placeholder="Contoh: Pengumuman Libur Hari Raya Idul Fitri 1447 H"
                       value="{{ old('title') }}" required>
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Category + Pin --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="category">Kategori <span style="color:var(--danger);">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="info"     {{ old('category')=='info'     ? 'selected':'' }}>ℹ️ Informasi Umum</option>
                        <option value="meeting"  {{ old('category')=='meeting'  ? 'selected':'' }}>🗓 Rapat & Koordinasi</option>
                        <option value="holiday"  {{ old('category')=='holiday'  ? 'selected':'' }}>🏖 Hari Libur Resmi</option>
                        <option value="activity" {{ old('category')=='activity' ? 'selected':'' }}>🎯 Kegiatan Perusahaan</option>
                        <option value="other"    {{ old('category')=='other'    ? 'selected':'' }}>📝 Lain-lain</option>
                    </select>
                    @error('category')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Sematkan di Atas?</label>
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;cursor:pointer;margin-top:0.4rem;transition:all 0.2s;"
                           onmouseover="this.style.borderColor='rgba(245,158,11,0.4)'" onmouseout="this.style.borderColor='var(--border-soft)'">
                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                               {{ old('is_pinned') ? 'checked' : '' }}
                               style="accent-color:#F59E0B;width:1rem;height:1rem;cursor:pointer;">
                        <div>
                            <div style="font-size:0.82rem;font-weight:700;color:#FCD34D;">📌 Pin Pengumuman</div>
                            <div style="font-size:0.68rem;color:var(--t4);margin-top:0.1rem;">Tampil di urutan paling atas</div>
                        </div>
                    </label>
                    @error('is_pinned')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Published + Expired --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="published_at">Tanggal Terbit</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control"
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    <div style="font-size:0.65rem;color:var(--t5);margin-top:0.3rem;">Kosongkan untuk langsung terbit</div>
                    @error('published_at')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="expired_at">Tanggal Berakhir (Opsional)</label>
                    <input type="datetime-local" name="expired_at" id="expired_at" class="form-control"
                           value="{{ old('expired_at') }}">
                    <div style="font-size:0.65rem;color:var(--t5);margin-top:0.3rem;">Otomatis diarsipkan setelah tanggal ini</div>
                    @error('expired_at')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Content --}}
            <div class="form-group">
                <label class="form-label" for="content">Isi Pengumuman <span style="color:var(--danger);">*</span></label>
                <textarea name="content" id="content" rows="9" class="form-control"
                          placeholder="Tuliskan isi pengumuman secara detail di sini..." required>{{ old('content') }}</textarea>
                @error('content')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                <label class="form-label">Lampiran (PDF / Gambar, Opsional)</label>
                <div style="border:2px dashed var(--border-soft);border-radius:14px;padding:1.5rem;text-align:center;background:var(--bg-elevated);transition:all 0.25s ease;cursor:pointer;"
                     onmouseover="this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';"
                     onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';">
                    <label for="attachment" style="cursor:pointer;display:block;">
                        <svg style="width:28px;height:28px;color:var(--t5);margin:0 auto 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <div style="font-size:0.8rem;font-weight:600;color:var(--t3);">Klik untuk lampirkan file</div>
                        <div style="font-size:0.68rem;color:var(--t4);margin-top:0.2rem;">PDF, JPG, PNG · Maks 10 MB</div>
                    </label>
                    <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                </div>
                @error('attachment')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Buttons --}}
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Terbitkan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
