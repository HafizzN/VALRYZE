@extends('layouts.app')

@section('title', 'Buat Surat Baru')
@section('page-title', 'Buat Surat')
@section('breadcrumb', 'Dokumen / Surat Menyurat / Buat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Ajukan Surat Baru</h2>
            <p class="text-xs text-slate-500">Isi formulir di bawah untuk mengajukan surat resmi</p>
        </div>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold">Oops! Terjadi beberapa kesalahan:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('letters.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Subject / Title -->
            <div class="form-group">
                <label class="form-label" for="subject">Judul Surat / Perihal <span class="text-red-500">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control" placeholder="Contoh: Permohonan Izin Sakit / Surat Tugas Proyek A" value="{{ old('subject') }}" required>
                @error('subject')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category / Tipe Surat -->
                <div class="form-group">
                    <label class="form-label" for="letter_type">Kategori Surat <span class="text-red-500">*</span></label>
                    <select name="letter_type" id="letter_type" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="permission" {{ old('letter_type') == 'permission' ? 'selected' : '' }}>Izin Kehadiran (SI)</option>
                        <option value="leave" {{ old('letter_type') == 'leave' ? 'selected' : '' }}>Cuti (SC)</option>
                        <option value="assignment" {{ old('letter_type') == 'assignment' ? 'selected' : '' }}>Surat Tugas (SK/ST)</option>
                        <option value="field_duty" {{ old('letter_type') == 'field_duty' ? 'selected' : '' }}>Dinas Luar (SD)</option>
                        <option value="work_certificate" {{ old('letter_type') == 'work_certificate' ? 'selected' : '' }}>Keterangan Kerja (SKK)</option>
                        <option value="other" {{ old('letter_type') == 'other' ? 'selected' : '' }}>Lainnya / Surat Peringatan (SL)</option>
                    </select>
                    @error('letter_type')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label class="form-label" for="date">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Recipient Name -->
                <div class="form-group">
                    <label class="form-label" for="recipient_name">Nama Penerima / Ditujukan Kepada</label>
                    <input type="text" name="recipient_name" id="recipient_name" class="form-control" placeholder="Contoh: Direktur Utama / Kepala HRD" value="{{ old('recipient_name') }}">
                    @error('recipient_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Letter Number (Auto-generated preview) -->
                <div class="form-group">
                    <label class="form-label" for="letter_number_preview">Nomer Surat</label>
                    <input type="text" id="letter_number_preview" class="form-control" style="opacity: 0.6;" value="Akan dibuat secara otomatis oleh sistem" disabled>
                </div>
            </div>

            <!-- Content (Textarea) -->
            <div class="form-group">
                <label class="form-label" for="content">Isi / Pesan Surat</label>
                <textarea name="content" id="content" rows="6" class="form-control" placeholder="Tuliskan isi surat secara lengkap disini...">{{ old('content') }}</textarea>
                @error('content')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label class="form-label" for="file_path">Lampiran Dokumen (PDF, Opsional)</label>
                <input type="file" name="file_path" id="file_path" class="form-control" accept=".pdf">
                <p class="text-[10px] text-slate-500 mt-1">Format file harus berupa PDF (Maksimal 10MB)</p>
                @error('file_path')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('letters.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ajukan Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
