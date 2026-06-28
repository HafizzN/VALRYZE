@extends('layouts.app')

@section('title', 'Tambah Karyawan Baru')
@section('page-title', 'Tambah Karyawan')
@section('breadcrumb', 'Manajemen › Karyawan › Tambah')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 animate-fadeSlideIn" x-data="{ selectedDivision: '{{ old('division_id') }}', divisions: @js($divisions) }">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Formulir Karyawan Baru</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Lengkapi data pribadi, pekerjaan, dan unggahan dokumen resmi karyawan</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p style="font-weight:700;font-size:0.8rem;">Terjadi beberapa kesalahan input:</p>
                <ul style="padding-left:1rem;margin-top:0.25rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.75rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.5rem;">
        @csrf

        <!-- SECTION 1: INFORMASI AKUN -->
        <div class="card" style="display:flex;flex-direction:column;gap:1.25rem;">
            <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);border-bottom:1px solid var(--border-dim);padding-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.1rem;">
                <span style="width:20px;height:20px;background:var(--em-ghost);border:1px solid var(--em-border);color:var(--em);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:900;">1</span>
                Informasi Akun & Akses
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label" for="nik">Nomor Induk Karyawan (NIK) <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="nik" id="nik" class="form-control" style="font-family:'JetBrains Mono',monospace;letter-spacing:0.04em;" placeholder="Contoh: NIK20260012" value="{{ old('nik') }}" required>
                    @error('nik') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nama Lengkap Karyawan" value="{{ old('name') }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span style="color:var(--danger);">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="email@perusahaan.com" value="{{ old('email') }}" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Role Portal <span style="color:var(--danger);">*</span></label>
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
                    @error('role') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password Sistem</label>
                    <input type="text" class="form-control" style="opacity:0.55;font-family:'JetBrains Mono',monospace;" value="Secara default diatur sama dengan NIK" disabled>
                    <p style="font-size:0.67rem;color:var(--t4);margin-top:0.3rem;">Karyawan dapat mengubah password secara mandiri setelah login pertama kali</p>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DETAIL PEKERJAAN -->
        <div class="card" style="display:flex;flex-direction:column;gap:1.25rem;">
            <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);border-bottom:1px solid var(--border-dim);padding-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.1rem;">
                <span style="width:20px;height:20px;background:var(--em-ghost);border:1px solid var(--em-border);color:var(--em);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:900;">2</span>
                Penempatan & Status Kerja
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Divisi -->
                <div class="form-group">
                    <label class="form-label" for="division_id">Divisi <span style="color:var(--danger);">*</span></label>
                    <select name="division_id" id="division_id" class="form-control" x-model="selectedDivision" required>
                        <option value="" disabled selected>Pilih Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Jabatan (Dynamic based on Divisi) -->
                <div class="form-group">
                    <label class="form-label" for="position_id">Jabatan <span style="color:var(--danger);">*</span></label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        <option value="" disabled selected>Pilih Jabatan</option>
                        <!-- Alpine Loop -->
                        <template x-if="selectedDivision">
                            <template x-for="pos in divisions.find(d => d.id == selectedDivision)?.positions || []" :key="pos.id">
                                <option :value="pos.id" x-text="pos.name" :selected="pos.id == '{{ old('position_id') }}'"></option>
                            </template>
                        </template>
                    </select>
                    @error('position_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Shift Kerja -->
                <div class="form-group">
                    <label class="form-label" for="shift_id">Shift Kerja <span style="color:var(--danger);">*</span></label>
                    <select name="shift_id" id="shift_id" class="form-control" required>
                        <option value="" disabled selected>Pilih Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})</option>
                        @endforeach
                    </select>
                    @error('shift_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employment Type -->
                <div class="form-group">
                    <label class="form-label" for="employment_type">Tipe Kontrak <span style="color:var(--danger);">*</span></label>
                    <select name="employment_type" id="employment_type" class="form-control" required>
                        <option value="permanent" {{ old('employment_type') == 'permanent' ? 'selected' : '' }}>🏢 Karyawan Tetap</option>
                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>📄 Kontrak Kerja</option>
                        <option value="internship" {{ old('employment_type') == 'internship' ? 'selected' : '' }}>🎓 Magang (Internship)</option>
                        <option value="freelance" {{ old('employment_type') == 'freelance' ? 'selected' : '' }}>📎 Freelance</option>
                    </select>
                    @error('employment_type') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Join Date -->
                <div class="form-group">
                    <label class="form-label" for="join_date">Tanggal Mulai Masuk <span style="color:var(--danger);">*</span></label>
                    <input type="date" name="join_date" id="join_date" class="form-control" style="font-family:'JetBrains Mono',monospace;" value="{{ old('join_date', now()->format('Y-m-d')) }}" required>
                    @error('join_date') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Leave Quota -->
                <div class="form-group">
                    <label class="form-label" for="annual_leave_quota">Kuota Cuti Tahunan</label>
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        <input type="number" name="annual_leave_quota" id="annual_leave_quota" class="form-control" style="font-family:'JetBrains Mono',monospace;" min="0" value="{{ old('annual_leave_quota', 12) }}">
                        <span style="font-size:0.72rem;color:var(--t4);white-space:nowrap;">hari</span>
                    </div>
                    @error('annual_leave_quota') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 3: DATA PRIBADI -->
        <div class="card" style="display:flex;flex-direction:column;gap:1.25rem;">
            <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);border-bottom:1px solid var(--border-dim);padding-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.1rem;">
                <span style="width:20px;height:20px;background:var(--em-ghost);border:1px solid var(--em-border);color:var(--em);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:900;">3</span>
                Informasi Pribadi & Kontak
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Gender -->
                <div class="form-group">
                    <label class="form-label" for="gender">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>👨 Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>👩 Perempuan</option>
                    </select>
                    @error('gender') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label" for="phone">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" class="form-control" style="font-family:'JetBrains Mono',monospace;" placeholder="0812xxxxxxxx" value="{{ old('phone') }}">
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Religion -->
                <div class="form-group">
                    <label class="form-label" for="religion">Agama</label>
                    <input type="text" name="religion" id="religion" class="form-control" placeholder="Contoh: Islam, Kristen, Hindu, Budha, Konghucu" value="{{ old('religion') }}">
                    @error('religion') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Birth Place -->
                <div class="form-group">
                    <label class="form-label" for="birth_place">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" class="form-control" placeholder="Tempat Lahir" value="{{ old('birth_place') }}">
                    @error('birth_place') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Birth Date -->
                <div class="form-group">
                    <label class="form-label" for="birth_date">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control" style="font-family:'JetBrains Mono',monospace;" value="{{ old('birth_date') }}">
                    @error('birth_date') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Marital Status -->
                <div class="form-group">
                    <label class="form-label" for="marital_status">Status Pernikahan</label>
                    <select name="marital_status" id="marital_status" class="form-control">
                        <option value="">Pilih Status</option>
                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>🔓 Lajang (Single)</option>
                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>🔒 Menikah</option>
                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>💔 Cerai</option>
                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>🕊️ Duda / Janda</option>
                    </select>
                    @error('marital_status') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="address">Alamat Tinggal Lengkap</label>
                <textarea name="address" id="address" rows="3" class="form-control" placeholder="Tuliskan alamat tinggal lengkap saat ini...">{{ old('address') }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <!-- Photo -->
            <div class="form-group">
                <label class="form-label" for="photo">Foto Profil Karyawan (Format Gambar)</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                <p style="font-size:0.67rem;color:var(--t4);margin-top:0.3rem;">Ukuran foto maksimal: 2MB (JPG, JPEG, PNG)</p>
                @error('photo') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- SECTION 4: DOKUMEN KEPEGAWAIAN -->
        <div class="card" style="display:flex;flex-direction:column;gap:1.25rem;">
            <h3 style="font-size:0.85rem;font-weight:800;color:var(--t1);border-bottom:1px solid var(--border-dim);padding-bottom:0.6rem;display:flex;align-items:center;gap:0.5rem;margin-bottom:0.1rem;">
                <span style="width:20px;height:20px;background:var(--em-ghost);border:1px solid var(--em-border);color:var(--em);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:900;">4</span>
                Unggah Dokumen Kepegawaian (PDF / Gambar)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- KTP -->
                <div class="form-group">
                    <label class="form-label" for="ktp">Scan KTP</label>
                    <input type="file" name="ktp" id="ktp" class="form-control" accept=".pdf,image/*">
                    @error('ktp') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- NPWP -->
                <div class="form-group">
                    <label class="form-label" for="npwp">Scan NPWP</label>
                    <input type="file" name="npwp" id="npwp" class="form-control" accept=".pdf,image/*">
                    @error('npwp') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- CV -->
                <div class="form-group">
                    <label class="form-label" for="cv">Curriculum Vitae (CV)</label>
                    <input type="file" name="cv" id="cv" class="form-control" accept=".pdf">
                    @error('cv') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Contract -->
                <div class="form-group">
                    <label class="form-label" for="contract">Surat Perjanjian Kerja / Kontrak</label>
                    <input type="file" name="contract" id="contract" class="form-control" accept=".pdf">
                    @error('contract') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <p style="font-size:0.67rem;color:var(--t4);">Berkas unggahan maksimal 10MB per berkas.</p>
        </div>

        <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Daftarkan Karyawan
            </button>
        </div>
    </form>
</div>
@endsection
