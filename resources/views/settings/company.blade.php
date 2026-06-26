@extends('layouts.app')

@section('title', 'Pengaturan Perusahaan')
@section('page-title', 'Pengaturan Perusahaan')
@section('breadcrumb', 'Pengaturan / Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Konfigurasi Perusahaan</h2>
            <p class="text-xs text-slate-500">Kelola identitas resmi perusahaan, kontak, API Key peta, dan koordinat geofencing utama</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi kesalahan pada pengisian data:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: IDENTITAS UTAMA -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 border-b border-slate-200 pb-2">Identitas Utama</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Logo Upload -->
                    <div class="form-group col-span-2 flex flex-col md:flex-row items-center gap-4 p-4 bg-slate-100/20 rounded-lg border border-slate-200">
                        <div class="relative">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" class="w-20 h-20 rounded-lg object-contain bg-slate-50 border border-slate-750">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-2xl">CO</div>
                            @endif
                        </div>
                        <div class="flex-1 space-y-1">
                            <label class="form-label mb-1" for="logo">Logo Perusahaan</label>
                            <input type="file" name="logo" id="logo" class="form-control text-xs" accept="image/*">
                            <p class="text-[9px] text-slate-500">Direkomendasikan rasio persegi (1:1), Maksimal 2MB (JPG, PNG)</p>
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Resmi Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nama Perusahaan" value="{{ old('name', $company->name) }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Website -->
                    <div class="form-group">
                        <label class="form-label" for="website">Website Resmi</label>
                        <input type="url" name="website" id="website" class="form-control" placeholder="https://www.perusahaan.com" value="{{ old('website', $company->website) }}">
                        @error('website') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NPWP -->
                    <div class="form-group">
                        <label class="form-label" for="npwp">NPWP Perusahaan</label>
                        <input type="text" name="npwp" id="npwp" class="form-control" placeholder="Nomor NPWP Perusahaan" value="{{ old('npwp', $company->npwp) }}">
                        @error('npwp') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- NIB -->
                    <div class="form-group">
                        <label class="form-label" for="nib">NIB / Izin Usaha</label>
                        <input type="text" name="nib" id="nib" class="form-control" placeholder="Nomor Induk Berusaha (NIB)" value="{{ old('nib', $company->nib) }}">
                        @error('nib') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: KONTAK & ALAMAT -->
            <div class="space-y-4 pt-4 border-t border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 border-b border-slate-200 pb-2">Kontak & Alamat Kantor</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Email Resmi</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="corporate@perusahaan.com" value="{{ old('email', $company->email) }}">
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label class="form-label" for="phone">Nomor Telepon Kantor</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Telepon Resmi / Fax" value="{{ old('phone', $company->phone) }}">
                        @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Physical Address -->
                <div class="form-group">
                    <label class="form-label" for="address">Alamat Fisik Kantor Utama</label>
                    <textarea name="address" id="address" rows="3" class="form-control" placeholder="Alamat lengkap kantor utama...">{{ old('address', $company->address) }}</textarea>
                    @error('address') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- SECTION 3: API KEY & INTEGRASI PETA (GEOFENCING) -->
            <div class="space-y-4 pt-4 border-t border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 border-b border-slate-200 pb-2">Integrasi Maps & Geofencing</h3>
                
                <p class="text-xs text-slate-500">Konfigurasi Maps API Key dan koordinat global pusat perusahaan. Konfigurasi ini berfungsi sebagai referensi peta atau wilayah geofencing default di sistem.</p>

                <div class="form-group">
                    <label class="form-label" for="google_maps_key">Google Maps API Key</label>
                    <input type="password" name="google_maps_key" id="google_maps_key" class="form-control" placeholder="Masukkan Google Maps API Key untuk rendering visual peta" value="{{ old('google_maps_key', config('services.google_maps.key')) }}">
                    <p class="text-[9px] text-slate-500 mt-1">API Key akan disimpan secara aman untuk diintegrasikan pada radar GPS kehadiran karyawan</p>
                    @error('google_maps_key') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Default Latitude -->
                    <div class="form-group">
                        <label class="form-label" for="default_latitude">Pusat Latitude Default</label>
                        <input type="text" name="default_latitude" id="default_latitude" class="form-control" placeholder="Contoh: -6.2088" value="{{ old('default_latitude', '-6.2088') }}">
                    </div>

                    <!-- Default Longitude -->
                    <div class="form-group">
                        <label class="form-label" for="default_longitude">Pusat Longitude Default</label>
                        <input type="text" name="default_longitude" id="default_longitude" class="form-control" placeholder="Contoh: 106.8456" value="{{ old('default_longitude', '106.8456') }}">
                    </div>

                    <!-- Geofence Radius Default -->
                    <div class="form-group">
                        <label class="form-label" for="default_radius">Radius Geofence Default (Meter)</label>
                        <input type="number" name="default_radius" id="default_radius" class="form-control" placeholder="Contoh: 100" value="{{ old('default_radius', '100') }}">
                    </div>
                </div>
            </div>

            <!-- About / Company Overview -->
            <div class="space-y-4 pt-4 border-t border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-700 border-b border-slate-200 pb-2">Deskripsi Tentang Perusahaan</h3>
                <div class="form-group">
                    <label class="form-label" for="about">Tentang Kami / Profil Ringkas</label>
                    <textarea name="about" id="about" rows="4" class="form-control" placeholder="Tuliskan gambaran profil ringkas mengenai visi, misi, atau deskripsi tentang perusahaan...">{{ old('about', $company->about) }}</textarea>
                    @error('about') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
