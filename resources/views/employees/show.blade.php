@extends('layouts.app')

@section('title', 'Profil Karyawan — ' . $employee->name)
@section('page-title', 'Profil Karyawan')
@section('breadcrumb', 'Manajemen / Karyawan / Detail')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Rincian Profil Karyawan</h2>
            <p class="text-xs text-slate-500">Informasi komprehensif mengenai data diri, karir, dokumen, dan performa kehadiran</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                Kembali
            </a>
            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profil
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Side: Profile Summary -->
        <div class="space-y-6">
            <div class="card text-center space-y-4">
                <div class="relative inline-block mx-auto">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-28 h-28 rounded-full object-cover border-2 border-indigo-500 mx-auto">
                    @else
                        <div class="avatar text-xl w-28 h-28 mx-auto font-bold">{{ $employee->initials }}</div>
                    @endif
                    <div class="absolute bottom-1 right-2">
                        @if($employee->status == 'active')
                            <span class="w-4.5 h-4.5 bg-emerald-500 rounded-full border-2 border-slate-900 block" title="Aktif"></span>
                        @else
                            <span class="w-4.5 h-4.5 bg-red-500 rounded-full border-2 border-slate-900 block" title="Non-Aktif"></span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $employee->name }}</h3>
                    <p class="text-xs font-mono text-emerald-700 mt-0.5">{{ $employee->nik }}</p>
                    <div class="flex items-center justify-center gap-1.5 mt-2">
                        <span class="badge badge-purple">{{ strtoupper($employee->roles->first()->name ?? 'Karyawan') }}</span>
                        @switch($employee->employment_type)
                            @case('permanent')
                                <span class="badge badge-success">Permanent</span>
                                @break
                            @case('contract')
                                <span class="badge badge-orange">Kontrak</span>
                                @break
                            @default
                                <span class="badge badge-gray">{{ ucfirst($employee->employment_type) }}</span>
                        @endswitch
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4 space-y-2.5 text-left text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Divisi:</span>
                        <span class="font-semibold text-slate-700">{{ $employee->division->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jabatan:</span>
                        <span class="font-semibold text-slate-700">{{ $employee->position->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Shift Kerja:</span>
                        <span class="font-semibold text-emerald-600">{{ $employee->shift->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Gabung:</span>
                        <span class="font-semibold text-slate-700">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                <!-- Leave Stats Widget -->
                <div class="border-t border-slate-200 pt-4 grid grid-cols-2 gap-2 text-center">
                    <div class="p-2 bg-slate-50/40 rounded border border-slate-200">
                        <div class="text-base font-bold text-slate-800">{{ $employee->annual_leave_quota }}</div>
                        <div class="text-[9px] text-slate-500 uppercase">Kuota Cuti</div>
                    </div>
                    <div class="p-2 bg-slate-50/40 rounded border border-slate-200">
                        <div class="text-base font-bold text-emerald-700">{{ $leaveBalance }}</div>
                        <div class="text-[9px] text-slate-500 uppercase">Sisa Cuti</div>
                    </div>
                </div>
            </div>

            <!-- Quick Contacts -->
            <div class="card space-y-3">
                <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200 pb-2">Kontak Cepat</h4>
                
                <div class="space-y-3">
                    <a href="mailto:{{ $employee->email }}" class="btn btn-secondary w-full justify-start text-xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="truncate">{{ $employee->email }}</span>
                    </a>
                    @if($employee->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $employee->phone) }}" target="_blank" class="btn btn-secondary w-full justify-start text-xs text-emerald-400">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $employee->phone }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Detailed Details -->
        <div class="lg:col-span-2 space-y-6" x-data="{ activeTab: 'personal' }">
            
            <!-- Navigation Tabs -->
            <div class="flex border-b border-slate-200 gap-1 bg-slate-50/50 p-1.5 rounded-lg">
                <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:text-slate-800'" class="flex-1 py-2 text-center text-xs font-semibold rounded-md transition duration-150">
                    Biodata Pribadi
                </button>
                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:text-slate-800'" class="flex-1 py-2 text-center text-xs font-semibold rounded-md transition duration-150">
                    Berkas Resmi
                </button>
                <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:text-slate-800'" class="flex-1 py-2 text-center text-xs font-semibold rounded-md transition duration-150">
                    Riwayat Kehadiran (10 Terakhir)
                </button>
            </div>

            <!-- Tab 1: Personal Details -->
            <div x-show="activeTab === 'personal'" class="card space-y-6" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase text-emerald-700 tracking-wider">Identitas & Kelahiran</h4>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <span class="text-slate-500">Tempat Lahir:</span>
                            <span class="col-span-2 font-semibold text-slate-800">{{ $employee->birth_place ?? '-' }}</span>

                            <span class="text-slate-500">Tanggal Lahir:</span>
                            <span class="col-span-2 font-semibold text-slate-800">{{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d M Y') : '-' }}</span>

                            <span class="text-slate-500">Jenis Kelamin:</span>
                            <span class="col-span-2 font-semibold text-slate-800">{{ $employee->gender == 'male' ? 'Laki-laki' : ($employee->gender == 'female' ? 'Perempuan' : '-') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase text-emerald-700 tracking-wider">Latar Belakang</h4>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <span class="text-slate-500">Agama:</span>
                            <span class="col-span-2 font-semibold text-slate-800">{{ $employee->religion ?? '-' }}</span>

                            <span class="text-slate-500">Status Nikah:</span>
                            <span class="col-span-2 font-semibold text-slate-800">
                                @switch($employee->marital_status)
                                    @case('single') Lajang @break
                                    @case('married') Menikah @break
                                    @case('divorced') Cerai @break
                                    @case('widowed') Duda/Janda @break
                                    @default -
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200/80 pt-4 space-y-3">
                    <h4 class="text-xs font-bold uppercase text-emerald-700 tracking-wider">Alamat Lengkap</h4>
                    <p class="text-xs leading-relaxed text-slate-700 bg-slate-100/20 p-3 rounded border border-slate-200 font-mono">
                        {{ $employee->address ?? 'Alamat belum diisi.' }}
                    </p>
                </div>
            </div>

            <!-- Tab 2: Official Documents -->
            <div x-show="activeTab === 'documents'" class="card space-y-4" x-transition style="display: none;">
                <h4 class="text-xs font-bold uppercase text-emerald-700 tracking-wider border-b border-slate-200 pb-2">Dokumen Kepegawaian</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php 
                        $docTypes = [
                            'ktp' => 'Kartu Tanda Penduduk (KTP)',
                            'npwp' => 'Nomor Pokok Wajib Pajak (NPWP)',
                            'cv' => 'Curriculum Vitae (CV)',
                            'contract' => 'Kontrak Kerja / SPK'
                        ];
                    @endphp

                    @foreach($docTypes as $key => $label)
                        @php $doc = $employee->documents->where('type', $key)->first(); @endphp
                        <div class="p-3.5 bg-slate-50/60 border border-slate-200 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="p-2 rounded bg-slate-100 text-emerald-700 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-slate-800 truncate">{{ $label }}</div>
                                    @if($doc)
                                        <div class="text-[9px] text-slate-500 truncate">{{ $doc->file_name }}</div>
                                    @else
                                        <div class="text-[9px] text-red-500 italic">Belum diunggah</div>
                                    @endif
                                </div>
                            </div>
                            @if($doc)
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-primary btn-sm py-1 px-2.5 text-[10px]">
                                    Unduh
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab 3: Recent Attendance -->
            <div x-show="activeTab === 'attendance'" class="card" x-transition style="display: none;">
                <h4 class="text-xs font-bold uppercase text-emerald-700 tracking-wider border-b border-slate-200 pb-2 mb-3">Kehadiran Terakhir</h4>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk (Check In)</th>
                                <th>Pulang (Check Out)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $attendance)
                                <tr>
                                    <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                    <td>
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') : '--:--:--' }}</div>
                                            @if($attendance->check_in_latitude)
                                                <div class="text-[9px] text-slate-500 dark:text-slate-400 flex items-center gap-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> GPS Verified
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') : '--:--:--' }}</div>
                                            @if($attendance->check_out_latitude)
                                                <div class="text-[9px] text-slate-500 dark:text-slate-400 flex items-center gap-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> GPS Verified
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @switch($attendance->status)
                                            @case('present')
                                                <span class="badge badge-success">Hadir</span>
                                                @break
                                            @case('late')
                                                <span class="badge badge-warning">Terlambat</span>
                                                @break
                                            @case('absent')
                                                <span class="badge badge-danger">Mangkir</span>
                                                @break
                                            @case('leave')
                                                <span class="badge badge-orange">Cuti</span>
                                                @break
                                            @case('permission')
                                                <span class="badge badge-purple">Izin</span>
                                                @break
                                            @default
                                                <span class="badge badge-gray">{{ $attendance->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-slate-500 text-xs">
                                        Belum ada riwayat kehadiran terdaftar untuk karyawan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
