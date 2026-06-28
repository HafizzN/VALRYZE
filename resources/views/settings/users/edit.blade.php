@extends('layouts.app')

@section('title', 'Ubah Pengguna')
@section('page-title', 'Edit Pengguna')
@section('breadcrumb', 'Pengaturan › Manajemen User › Edit')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Edit Data Pengguna</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Perbarui rincian informasi, hak akses, atau status akun pengguna</p>
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
        <form action="{{ route('settings.users.update', $user->id) }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" placeholder="Masukkan nama lengkap" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" placeholder="nama@perusahaan.com" required>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- Role -->
            <div class="form-group">
                <label class="form-label" for="role">Tingkat Hak Akses (Role) <span style="color:var(--danger);">*</span></label>
                <select name="role" id="role" class="form-control" required>
                    <option value="" disabled>Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
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

            {{-- Info Tip --}}
            <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.9rem 1rem;background:var(--em-ghost);border:1px solid var(--em-border);border-radius:12px;font-size:0.76rem;color:var(--t3);">
                <svg style="width:17px;height:17px;color:var(--em);flex-shrink:0;margin-top:0.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span style="font-weight:800;color:var(--em-light);">Ubah Password: </span>
                    Biarkan kolom di bawah ini kosong jika Anda tidak ingin mengubah kata sandi pengguna ini.
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
