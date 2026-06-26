@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru')
@section('page-title', 'Buat Pengumuman')
@section('breadcrumb', 'Dokumen / Pengumuman / Buat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Publikasikan Pengumuman</h2>
            <p class="text-xs text-slate-500">Buat informasi baru untuk disebarkan kepada seluruh karyawan</p>
        </div>
        <a href="{{ route('announcements.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi kesalahan pada input data:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Title -->
            <div class="form-group">
                <label class="form-label" for="title">Judul Pengumuman <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Pengumuman Libur Hari Raya Idul Fitri 1447 H" value="{{ old('title') }}" required>
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="category">Kategori Pengumuman <span class="text-red-500">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>Informasi Umum (Info)</option>
                        <option value="meeting" {{ old('category') == 'meeting' ? 'selected' : '' }}>Rapat & Koordinasi (Meeting)</option>
                        <option value="holiday" {{ old('category') == 'holiday' ? 'selected' : '' }}>Hari Libur / Cuti Bersama (Holiday)</option>
                        <option value="activity" {{ old('category') == 'activity' ? 'selected' : '' }}>Kegiatan Perusahaan (Activity)</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Lain-lain (Other)</option>
                    </select>
                    @error('category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Pin Announcement Checkbox -->
                <div class="form-group">
                    <label class="form-label" for="is_pinned">Pin di Bagian Atas?</label>
                    <div class="flex items-center mt-2.5">
                        <input type="checkbox" name="is_pinned" id="is_pinned" value="1" class="rounded border-slate-200 bg-slate-100 text-emerald-700 focus:ring-indigo-500 h-4 w-4" {{ old('is_pinned') ? 'checked' : '' }}>
                        <span class="ml-2 text-xs text-slate-600">Sematkan pengumuman ini di urutan paling atas</span>
                    </div>
                    @error('is_pinned')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Published At -->
                <div class="form-group">
                    <label class="form-label" for="published_at">Tanggal Terbit</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                    <p class="text-[10px] text-slate-500 mt-1">Kosongkan untuk langsung menerbitkan sekarang</p>
                    @error('published_at')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expired At -->
                <div class="form-group">
                    <label class="form-label" for="expired_at">Tanggal Berakhir (Expired)</label>
                    <input type="datetime-local" name="expired_at" id="expired_at" class="form-control" value="{{ old('expired_at') }}">
                    <p class="text-[10px] text-slate-500 mt-1">Pengumuman akan otomatis diarsipkan setelah tanggal ini</p>
                    @error('expired_at')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Content -->
            <div class="form-group">
                <label class="form-label" for="content">Isi Pengumuman <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="8" class="form-control" placeholder="Tuliskan isi pengumuman secara detail disini..." required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Attachment File -->
            <div class="form-group">
                <label class="form-label" for="attachment">Lampiran Pendukung (PDF / Gambar, Opsional)</label>
                <input type="file" name="attachment" id="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <p class="text-[10px] text-slate-500 mt-1">Format berkas: PDF, JPG, JPEG, PNG (Maksimal 10MB)</p>
                @error('attachment')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Terbitkan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
