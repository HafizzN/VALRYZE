@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna')
@section('breadcrumb', 'Pengaturan › Manajemen User › Tambah')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Tambah Pengguna Baru</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Buat pengguna baru untuk akses ke sistem admin portal</p>
        </div>
        <a href="{{ route('settings.users.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p style="font-weight:700;font-size:0.8rem;">Terjadi kesalahan input data:</p>
                <ul style="padding-left:1rem;margin-top:0.25rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.75rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('settings.users.store') }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" placeholder="Masukkan nama lengkap" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="nama@perusahaan.com" required>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- Role -->
            <div class="form-group">
                <label class="form-label" for="role">Tingkat Hak Akses (Role) <span style="color:var(--danger);">*</span></label>
                <select name="role" id="role" class="form-control" required>
                    <option value="" disabled selected>Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            @switch($role->name)
                                @case('super_admin') 🛡️ SUPER ADMIN @break
                                @case('hrd')         💼 HRD @break
                                @case('manager')     🎖️ MANAGER @break
                                @case('employee')    👤 KARYAWAN @break
                                @default             ⚙️ {{ strtoupper($role->name) }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
                @error('role')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi <span style="color:var(--danger);">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi <span style="color:var(--danger);">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
