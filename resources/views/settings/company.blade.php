@extends('layouts.app')

@section('title', 'Pengaturan Perusahaan')
@section('page-title', 'Pengaturan Perusahaan')
@section('breadcrumb', 'Pengaturan › Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 animate-fadeSlideIn">
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Konfigurasi Perusahaan</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola identitas resmi perusahaan, kontak, API Key peta, dan koordinat geofencing utama</p>
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
                <p style="font-weight:700;">Terjadi kesalahan pada pengisian data:</p>
                <ul style="margin-top:0.25rem;padding-left:1rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.78rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.5rem;">
            @csrf
            @method('PUT')

            {{-- SECTION 1: IDENTITAS UTAMA --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Identitas Utama</h3>
                
                <div style="display:grid;grid-template-columns:1fr;gap:1rem;">
                    {{-- Logo Upload --}}
                    <div style="display:flex;flex-direction:column;gap:1rem;padding:1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;" class="md:flex-row items-center">
                        <div style="position:relative;flex-shrink:0;">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" style="width:70px;height:70px;border-radius:10px;object-fit:contain;background:var(--bg-base);border:1px solid var(--border-soft);">
                            @else
                                <div style="width:70px;height:70px;border-radius:10px;background:var(--bg-base);border:1px solid var(--border-soft);display:flex;align-items:center;justify-content:center;color:var(--t4);font-weight:800;font-size:1.4rem;">CO</div>
                            @endif
                        </div>
                        <div style="flex:1;">
                            <label class="form-label" for="logo">Logo Perusahaan</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*" style="font-size:0.75rem;padding:0.45rem 0.75rem;">
                            <p style="font-size:0.62rem;color:var(--t5);margin-top:0.25rem;">Rasio persegi (1:1) direkomendasikan, Maksimal 2MB (JPG, PNG)</p>
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-2 gap-4">
                    {{-- Company Name --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="name">Nama Resmi Perusahaan <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nama Perusahaan" value="{{ old('name', $company->name) }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- Website --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="website">Website Resmi</label>
                        <input type="url" name="website" id="website" class="form-control" placeholder="https://www.perusahaan.com" value="{{ old('website', $company->website) }}">
                        @error('website') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-2 gap-4">
                    {{-- NPWP --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="npwp">NPWP Perusahaan</label>
                        <input type="text" name="npwp" id="npwp" class="form-control" placeholder="Nomor NPWP Perusahaan" value="{{ old('npwp', $company->npwp) }}">
                        @error('npwp') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- NIB --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="nib">NIB / Izin Usaha</label>
                        <input type="text" name="nib" id="nib" class="form-control" placeholder="Nomor Induk Berusaha (NIB)" value="{{ old('nib', $company->nib) }}">
                        @error('nib') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: KONTAK & ALAMAT --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Kontak & Alamat Kantor</h3>

                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-2 gap-4">
                    {{-- Email --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="email">Alamat Email Resmi</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="corporate@perusahaan.com" value="{{ old('email', $company->email) }}">
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="phone">Nomor Telepon Kantor</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Telepon Resmi / Fax" value="{{ old('phone', $company->phone) }}">
                        @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Physical Address --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="address">Alamat Fisik Kantor Utama</label>
                    <textarea name="address" id="address" rows="3" class="form-control" placeholder="Alamat lengkap kantor utama...">{{ old('address', $company->address) }}</textarea>
                    @error('address') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- SECTION 3: API KEY & INTEGRASI PETA (GEOFENCING) --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Integrasi Maps & Geofencing</h3>
                
                <p style="font-size:0.75rem;color:var(--t4);line-height:1.4;">Konfigurasi Maps API Key dan koordinat global pusat perusahaan. Konfigurasi ini berfungsi sebagai referensi peta atau wilayah geofencing default di sistem.</p>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="google_maps_key">Google Maps API Key</label>
                    <input type="password" name="google_maps_key" id="google_maps_key" class="form-control" placeholder="Masukkan Google Maps API Key untuk rendering visual peta" value="{{ old('google_maps_key', config('services.google_maps.key')) }}">
                    <p style="font-size:0.62rem;color:var(--t5);margin-top:0.25rem;">API Key akan disimpan secara aman untuk diintegrasikan pada radar GPS kehadiran karyawan</p>
                    @error('google_maps_key') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-3 gap-4">
                    {{-- Default Latitude --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="default_latitude">Pusat Latitude Default</label>
                        <input type="text" name="default_latitude" id="default_latitude" class="form-control" placeholder="Contoh: -6.2088" value="{{ old('default_latitude', '-6.2088') }}">
                    </div>

                    {{-- Default Longitude --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="default_longitude">Pusat Longitude Default</label>
                        <input type="text" name="default_longitude" id="default_longitude" class="form-control" placeholder="Contoh: 106.8456" value="{{ old('default_longitude', '106.8456') }}">
                    </div>

                    {{-- Geofence Radius Default --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="default_radius">Radius Geofence Default (Meter)</label>
                        <input type="number" name="default_radius" id="default_radius" class="form-control" placeholder="Contoh: 100" value="{{ old('default_radius', '100') }}">
                    </div>
                </div>
            </div>

            {{-- About / Company Overview --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <h3 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Deskripsi Tentang Perusahaan</h3>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="about">Tentang Kami / Profil Ringkas</label>
                    <textarea name="about" id="about" rows="4" class="form-control" placeholder="Tuliskan gambaran profil ringkas mengenai visi, misi, atau deskripsi tentang perusahaan...">{{ old('about', $company->about) }}</textarea>
                    @error('about') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
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
