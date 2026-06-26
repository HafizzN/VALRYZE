@extends('layouts.app')

@section('title', 'Edit Profil Karyawan — ' . $employee->name)
@section('page-title', 'Edit Karyawan')
@section('breadcrumb', 'Manajemen / Karyawan / Edit')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ selectedDivision: '{{ old('division_id', $employee->division_id) }}', divisions: @js($divisions) }">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Profil Karyawan</h2>
            <p class="text-xs text-slate-500">Perbarui data kepegawaian, data pribadi, atau unggahan dokumen resmi</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary btn-sm">
                Batal
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                Daftar Karyawan
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi beberapa kesalahan input:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: INFORMASI AKUN -->
        <div class="card space-y-4">
            <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 flex items-center gap-2">
                <span class="w-5 h-5 bg-indigo-500/20 text-emerald-700 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                Informasi Akun & Akses
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label" for="nik">Nomor Induk Karyawan (NIK) <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" id="nik" class="form-control" placeholder="Contoh: NIK20260012" value="{{ old('nik', $employee->nik) }}" required>
                    @error('nik') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nama Lengkap Karyawan" value="{{ old('name', $employee->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="email@perusahaan.com" value="{{ old('email', $employee->email) }}" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label" for="role">Hak Akses / Role Portal <span class="text-red-500">*</span></label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="" disabled>Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $employee->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ strtoupper($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status Aktif Karyawan <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="resign" {{ old('status', $employee->status) == 'resign' ? 'selected' : '' }}>Resign</option>
                    </select>
                    @error('status') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password Portal</label>
                    <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin mengubah password">
                    <p class="text-[9px] text-slate-500 mt-1">Kosongkan jika tidak ada perubahan password</p>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DETAIL PEKERJAAN -->
        <div class="card space-y-4">
            <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 flex items-center gap-2">
                <span class="w-5 h-5 bg-indigo-500/20 text-emerald-700 rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                Penempatan & Status Kerja
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Divisi -->
                <div class="form-group">
                    <label class="form-label" for="division_id">Divisi <span class="text-red-500">*</span></label>
                    <select name="division_id" id="division_id" class="form-control" x-model="selectedDivision" required>
                        <option value="" disabled>Pilih Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Jabatan (Dynamic based on Divisi) -->
                <div class="form-group">
                    <label class="form-label" for="position_id">Jabatan <span class="text-red-500">*</span></label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        <option value="" disabled>Pilih Jabatan</option>
                        <!-- Alpine Loop -->
                        <template x-if="selectedDivision">
                            <template x-for="pos in divisions.find(d => d.id == selectedDivision)?.positions || []" :key="pos.id">
                                <option :value="pos.id" x-text="pos.name" :selected="pos.id == '{{ old('position_id', $employee->position_id) }}'"></option>
                            </template>
                        </template>
                    </select>
                    @error('position_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Shift Kerja -->
                <div class="form-group">
                    <label class="form-label" for="shift_id">Shift Kerja <span class="text-red-500">*</span></label>
                    <select name="shift_id" id="shift_id" class="form-control" required>
                        <option value="" disabled>Pilih Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                        @endforeach
                    </select>
                    @error('shift_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Employment Type -->
                <div class="form-group">
                    <label class="form-label" for="employment_type">Tipe Kontrak <span class="text-red-500">*</span></label>
                    <select name="employment_type" id="employment_type" class="form-control" required>
                        <option value="permanent" {{ old('employment_type', $employee->employment_type) == 'permanent' ? 'selected' : '' }}>Karyawan Tetap</option>
                        <option value="contract" {{ old('employment_type', $employee->employment_type) == 'contract' ? 'selected' : '' }}>Kontrak Kerja</option>
                        <option value="internship" {{ old('employment_type', $employee->employment_type) == 'internship' ? 'selected' : '' }}>Magang (Internship)</option>
                        <option value="freelance" {{ old('employment_type', $employee->employment_type) == 'freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                    @error('employment_type') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Join Date -->
                <div class="form-group">
                    <label class="form-label" for="join_date">Tanggal Mulai Masuk <span class="text-red-500">*</span></label>
                    <input type="date" name="join_date" id="join_date" class="form-control" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}" required>
                    @error('join_date') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Leave Quota -->
                <div class="form-group">
                    <label class="form-label" for="annual_leave_quota">Kuota Cuti Tahunan</label>
                    <input type="number" name="annual_leave_quota" id="annual_leave_quota" class="form-control" min="0" value="{{ old('annual_leave_quota', $employee->annual_leave_quota) }}">
                    @error('annual_leave_quota') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- SECTION 3: DATA PRIBADI -->
        <div class="card space-y-4">
            <h3 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 flex items-center gap-2">
                <span class="w-5 h-5 bg-indigo-500/20 text-emerald-700 rounded-full flex items-center justify-center text-[10px] font-bold">3</span>
                Informasi Pribadi & Kontak
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Gender -->
                <div class="form-group">
                    <label class="form-label" for="gender">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label" for="phone">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="0812xxxxxxxx" value="{{ old('phone', $employee->phone) }}">
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Religion -->
                <div class="form-group">
                    <label class="form-label" for="religion">Agama</label>
                    <input type="text" name="religion" id="religion" class="form-control" placeholder="Contoh: Islam, Kristen, Hindu, Budha" value="{{ old('religion', $employee->religion) }}">
                    @error('religion') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Birth Place -->
                <div class="form-group">
                    <label class="form-label" for="birth_place">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" class="form-control" placeholder="Tempat Lahir" value="{{ old('birth_place', $employee->birth_place) }}">
                    @error('birth_place') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Birth Date -->
                <div class="form-group">
                    <label class="form-label" for="birth_date">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date', $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('Y-m-d') : '') }}">
                    @error('birth_date') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <!-- Marital Status -->
                <div class="form-group">
                    <label class="form-label" for="marital_status">Status Pernikahan</label>
                    <select name="marital_status" id="marital_status" class="form-control">
                        <option value="">Pilih Status</option>
                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Lajang (Single)</option>
                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Menikah</option>
                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Cerai</option>
                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Duda / Janda</option>
                    </select>
                    @error('marital_status') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="form-group">
                <label class="form-label" for="address">Alamat Tinggal Lengkap</label>
                <textarea name="address" id="address" rows="3" class="form-control" placeholder="Tuliskan alamat tinggal lengkap saat ini...">{{ old('address', $employee->address) }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <!-- Photo -->
            <div class="form-group">
                <label class="form-label" for="photo">Foto Profil Karyawan</label>
                @if($employee->photo)
                    <div class="flex items-center gap-4 mb-3 p-2 bg-slate-100/20 rounded border border-slate-200">
                        <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-16 h-16 rounded-full object-cover border border-slate-200">
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Foto profil terdaftar</p>
                            <p class="text-[9px] text-slate-500">Unggah foto baru untuk mengganti foto ini</p>
                        </div>
                    </div>
                @endif
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                <p class="text-[10px] text-slate-500 mt-1">Ukuran foto maksimal: 2MB (JPG, JPEG, PNG)</p>
                @error('photo') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
