@extends('layouts.app')

@section('title', 'Buat Surat Baru')
@section('page-title', 'Buat Surat')
@section('breadcrumb', 'Dokumen › Surat Menyurat › Buat')

@section('content')
<div class="max-w-3xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Ajukan Surat Baru</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Isi formulir di bawah untuk mengajukan surat resmi</p>
        </div>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.25rem;">
            <div class="flex-1">
                <p style="font-weight:700;">Oops! Terjadi beberapa kesalahan:</p>
                <ul style="margin-top:0.25rem;padding-left:1rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.78rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('letters.store') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            <!-- Subject / Title -->
            <div class="form-group">
                <label class="form-label" for="subject">Judul Surat / Perihal <span style="color:var(--danger);">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Contoh: Permohonan Izin Sakit / Surat Tugas Proyek A" value="{{ old('subject') }}" required>
                @error('subject')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category / Tipe Surat -->
                <div class="form-group">
                    <label class="form-label" for="letter_type">Kategori Surat <span style="color:var(--danger);">*</span></label>
                    <select name="letter_type" id="letter_type" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="permission"       {{ old('letter_type') == 'permission' ? 'selected' : '' }}>📝 Izin Kehadiran (SI)</option>
                        <option value="leave"            {{ old('letter_type') == 'leave' ? 'selected' : '' }}>🏖 Cuti (SC)</option>
                        <option value="assignment"       {{ old('letter_type') == 'assignment' ? 'selected' : '' }}>💼 Surat Tugas (SK/ST)</option>
                        <option value="field_duty"       {{ old('letter_type') == 'field_duty' ? 'selected' : '' }}>🗺 Dinas Luar (SD)</option>
                        <option value="work_certificate" {{ old('letter_type') == 'work_certificate' ? 'selected' : '' }}>📄 Keterangan Kerja (SKK)</option>
                        <option value="other"            {{ old('letter_type') == 'other' ? 'selected' : '' }}>📎 Lainnya / Surat Peringatan (SL)</option>
                    </select>
                    @error('letter_type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label class="form-label" for="date">Tanggal Surat <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Recipient Name -->
                <div class="form-group">
                    <label class="form-label" for="recipient_name">Nama Penerima / Ditujukan Kepada</label>
                    <input type="text" name="recipient_name" id="recipient_name" class="form-control" placeholder="Contoh: Direktur Utama / Kepala HRD" value="{{ old('recipient_name') }}">
                    @error('recipient_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Letter Number (Auto-generated preview) -->
                <div class="form-group">
                    <label class="form-label" for="letter_number_preview">Nomor Surat</label>
                    <input type="text" id="letter_number_preview" class="form-control" style="opacity:0.55;font-family:'JetBrains Mono',monospace;" value="Akan dibuat secara otomatis oleh sistem" disabled>
                </div>
            </div>

            <!-- Content (Textarea) -->
            <div class="form-group">
                <label class="form-label" for="content">Isi / Pesan Surat</label>
                <textarea name="content" id="content" rows="6" class="form-control" placeholder="Tuliskan isi surat secara lengkap di sini...">{{ old('content') }}</textarea>
                @error('content')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label class="form-label">Berkas Lampiran Pendukung (Opsional)</label>
                <div style="border:2px dashed var(--border-soft);border-radius:14px;padding:1.5rem;text-align:center;background:var(--bg-elevated);transition:all 0.25s ease;cursor:pointer;"
                     onmouseover="this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';"
                     onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';">
                    <label for="file" style="cursor:pointer;display:block;">
                        <svg style="width:28px;height:28px;color:var(--t5);margin:0 auto 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <div style="font-size:0.8rem;font-weight:600;color:var(--t3);">Klik untuk memilih lampiran dokumen pendukung</div>
                        <div style="font-size:0.68rem;color:var(--t4);margin-top:0.25rem;">PDF, JPG, PNG · Maks 10 MB</div>
                    </label>
                    <input type="file" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                </div>
                @error('file')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('letters.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
