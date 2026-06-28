@extends('layouts.app')

@section('title', 'Ubah Pengguna')
@section('page-title', 'Edit Pengguna')
@section('breadcrumb', 'Pengaturan / Manajemen User / Edit')

@section('content')
<div class="max-w-2xl mx-auto font-sans">
    <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">Form Ubah Pengguna</h3>
            <a href="{{ route('settings.users.index') }}" class="text-xs text-slate-500 hover:text-slate-700 flex items-center gap-1 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('settings.users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold text-slate-600">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Masukkan nama lengkap" required>
                @error('name')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-600">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="name@company.com" required>
                @error('email')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="space-y-1.5">
                <label for="role" class="block text-xs font-bold text-slate-600">Tingkat Hak Akses (Role)</label>
                <select name="role" id="role" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white" required>
                    <option value="" disabled>Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                            @switch($role->name)
                                @case('super_admin')
                                    SUPER ADMIN
                                    @break
                                @case('hrd')
                                    HRD
                                    @break
                                @case('manager')
                                    MANAGER
                                    @break
                                @case('employee')
                                    KARYAWAN
                                    @break
                                @default
                                    {{ strtoupper($role->name) }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200">
                <p class="text-[10px] text-amber-800 leading-relaxed">
                    💡 Biarkan bagian kata sandi di bawah ini kosong jika Anda tidak ingin mengubah kata sandi pengguna ini.
                </p>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-slate-600">Kata Sandi Baru (Opsional)</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Kosongkan jika tidak diubah">
                @error('password')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-600">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Kosongkan jika tidak diubah">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 border-none rounded-xl px-4 py-2">Batal</a>
                <button type="submit" class="btn btn-primary text-xs bg-indigo-600 hover:bg-indigo-700 border-none rounded-xl px-4 py-2">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
