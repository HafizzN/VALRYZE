@extends('layouts.app')

@section('title', 'Profil Karyawan — ' . $employee->name)
@section('page-title', 'Profil Karyawan')
@section('breadcrumb', 'Manajemen › Karyawan › Detail')

@section('content')
<div class="space-y-5 animate-fadeSlideIn">
    {{-- Header Actions --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Rincian Profil Karyawan</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Informasi komprehensif mengenai data diri, karir, dokumen, dan performa kehadiran</p>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profil
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="lg:grid-cols-3">
        
        {{-- Left Side: Profile Summary --}}
        <div class="space-y-4">
            <div class="card text-center" style="display:flex;flex-direction:column;align-items:center;gap:1rem;">
                <div style="position:relative;display:inline-block;margin:0 auto;">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2.5px solid var(--em);">
                    @else
                        <div class="avatar" style="width:100px;height:100px;font-size:1.8rem;border-width:2.5px;">{{ $employee->initials }}</div>
                    @endif
                    <div style="position:absolute;bottom:0;right:0.25rem;">
                        @if($employee->status == 'active')
                            <span style="width:16px;height:16px;background:var(--success);border-radius:50%;border:2px solid var(--bg-card);display:block;" title="Aktif"></span>
                        @else
                            <span style="width:16px;height:16px;background:var(--danger);border-radius:50%;border:2px solid var(--bg-card);display:block;" title="Non-Aktif"></span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 style="font-size:1.05rem;font-weight:900;color:var(--t1);">{{ $employee->name }}</h3>
                    <p style="font-size:0.75rem;font-family:'JetBrains Mono',monospace;color:var(--em);margin-top:0.15rem;font-weight:700;">{{ $employee->nik }}</p>
                    <div style="display:flex;align-items:center;justify-content:center;gap:0.4rem;margin-top:0.6rem;flex-wrap:wrap;">
                        <span class="badge badge-purple" style="font-size:0.65rem;">{{ strtoupper($employee->roles->first()->name ?? 'Karyawan') }}</span>
                        @switch($employee->employment_type)
                            @case('permanent')  <span class="badge badge-success" style="font-size:0.65rem;">Tetap</span> @break
                            @case('contract')   <span class="badge badge-orange" style="font-size:0.65rem;">Kontrak</span> @break
                            @case('internship') <span class="badge badge-purple" style="font-size:0.65rem;">Magang</span> @break
                            @case('freelance')  <span class="badge badge-gray" style="font-size:0.65rem;">Freelance</span> @break
                            @default            <span class="badge badge-gray" style="font-size:0.65rem;">{{ ucfirst($employee->employment_type) }}</span>
                        @endswitch
                    </div>
                </div>

                <div style="width:100%;border-top:1px solid var(--border-dim);padding-top:0.85rem;display:flex;flex-direction:column;gap:0.45rem;text-align:left;font-size:0.76rem;">
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--t4);">Divisi:</span>
                        <span style="font-weight:700;color:var(--t2);">{{ $employee->division->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--t4);">Jabatan:</span>
                        <span style="font-weight:700;color:var(--t2);">{{ $employee->position->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--t4);">Shift Kerja:</span>
                        <span style="font-weight:700;color:var(--em);">{{ $employee->shift->name ?? 'Belum Diatur' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--t4);">Tanggal Gabung:</span>
                        <span style="font-weight:700;color:var(--t2);">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                </div>

                {{-- Leave Stats Widget --}}
                <div style="width:100%;border-top:1px solid var(--border-dim);padding-top:0.85rem;display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;text-align:center;">
                    <div style="padding:0.5rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:10px;">
                        <div style="font-size:1.1rem;font-weight:800;color:var(--t1);font-family:'JetBrains Mono',monospace;">{{ $employee->annual_leave_quota }}</div>
                        <div style="font-size:0.58rem;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;margin-top:0.1rem;font-weight:700;">Kuota Cuti</div>
                    </div>
                    <div style="padding:0.5rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:10px;">
                        <div style="font-size:1.1rem;font-weight:800;color:var(--em);font-family:'JetBrains Mono',monospace;">{{ $leaveBalance }}</div>
                        <div style="font-size:0.58rem;color:var(--t4);text-transform:uppercase;letter-spacing:0.04em;margin-top:0.1rem;font-weight:700;">Sisa Cuti</div>
                    </div>
                </div>
            </div>

            {{-- Quick Contacts --}}
            <div class="card" style="display:flex;flex-direction:column;gap:0.75rem;">
                <h4 style="font-size:0.72rem;font-weight:800;color:var(--t5);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.1rem;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;">Kontak Cepat</h4>
                <div style="display:flex;flex-direction:column;gap:0.45rem;">
                    <a href="mailto:{{ $employee->email }}" class="btn btn-secondary w-full" style="font-size:0.75rem;justify-content:flex-start;padding:0.45rem 0.75rem;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="truncate">{{ $employee->email }}</span>
                    </a>
                    @if($employee->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $employee->phone) }}" target="_blank" class="btn btn-secondary w-full" style="font-size:0.75rem;justify-content:flex-start;padding:0.45rem 0.75rem;color:var(--em-light);border-color:var(--em-border);">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $employee->phone }}</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Side: Detailed Details --}}
        <div class="lg:col-span-2 space-y-4" x-data="{ activeTab: 'personal' }">
            
            {{-- Navigation Tabs --}}
            <div style="display:flex;gap:0.35rem;background:var(--bg-card);border:1px solid var(--border-soft);padding:0.35rem;border-radius:12px;">
                <button @click="activeTab = 'personal'"
                        :style="activeTab === 'personal' ? 'background:var(--em-ghost);color:var(--em);border-color:var(--em-border);' : 'color:var(--t4);border-color:transparent;'"
                        style="flex:1;padding:0.5rem 0.25rem;text-align:center;font-size:0.75rem;font-weight:700;border-radius:8px;border:1px solid;transition:all 0.15s;cursor:pointer;">
                    👤 Biodata Pribadi
                </button>
                <button @click="activeTab = 'documents'"
                        :style="activeTab === 'documents' ? 'background:var(--em-ghost);color:var(--em);border-color:var(--em-border);' : 'color:var(--t4);border-color:transparent;'"
                        style="flex:1;padding:0.5rem 0.25rem;text-align:center;font-size:0.75rem;font-weight:700;border-radius:8px;border:1px solid;transition:all 0.15s;cursor:pointer;">
                    📂 Berkas Resmi
                </button>
                <button @click="activeTab = 'attendance'"
                        :style="activeTab === 'attendance' ? 'background:var(--em-ghost);color:var(--em);border-color:var(--em-border);' : 'color:var(--t4);border-color:transparent;'"
                        style="flex:1;padding:0.5rem 0.25rem;text-align:center;font-size:0.75rem;font-weight:700;border-radius:8px;border:1px solid;transition:all 0.15s;cursor:pointer;">
                    ⏱ Kehadiran (10 Terakhir)
                </button>
            </div>

            {{-- Tab 1: Personal Details --}}
            <div x-show="activeTab === 'personal'" class="card animate-fadeSlideIn" style="display:flex;flex-direction:column;gap:1.25rem;" x-transition>
                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-2 gap-6">
                    <div style="display:flex;flex-direction:column;gap:0.75rem;">
                        <h4 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Identitas & Kelahiran</h4>
                        <div style="display:grid;grid-template-columns:100px 1fr;gap:0.5rem;font-size:0.78rem;">
                            <span style="color:var(--t4);">Tempat Lahir:</span>
                            <span style="font-weight:700;color:var(--t2);">{{ $employee->birth_place ?? '-' }}</span>

                            <span style="color:var(--t4);">Tanggal Lahir:</span>
                            <span style="font-weight:700;color:var(--t2);">{{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->translatedFormat('d M Y') : '-' }}</span>

                            <span style="color:var(--t4);">Jenis Kelamin:</span>
                            <span style="font-weight:700;color:var(--t2);">{{ $employee->gender == 'male' ? 'Laki-laki' : ($employee->gender == 'female' ? 'Perempuan' : '-') }}</span>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:0.75rem;">
                        <h4 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Latar Belakang</h4>
                        <div style="display:grid;grid-template-columns:100px 1fr;gap:0.5rem;font-size:0.78rem;">
                            <span style="color:var(--t4);">Agama:</span>
                            <span style="font-weight:700;color:var(--t2);">{{ $employee->religion ?? '-' }}</span>

                            <span style="color:var(--t4);">Status Nikah:</span>
                            <span style="font-weight:700;color:var(--t2);">
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

                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    <h4 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.4rem;">Alamat Lengkap</h4>
                    <p style="font-size:0.78rem;line-height:1.5;color:var(--t3);background:var(--bg-elevated);border:1px solid var(--border-soft);padding:0.75rem 1rem;border-radius:10px;font-family:'JetBrains Mono',monospace;">
                        {{ $employee->address ?? 'Alamat belum diisi.' }}
                    </p>
                </div>
            </div>

            {{-- Tab 2: Official Documents --}}
            <div x-show="activeTab === 'documents'" class="card animate-fadeSlideIn" style="display:flex;flex-direction:column;gap:1rem;display:none;" x-transition>
                <h4 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;">Dokumen Kepegawaian</h4>
                
                <div style="display:grid;grid-template-columns:1fr;" class="md:grid-cols-2 gap-3">
                    @php 
                        $docTypes = [
                            'ktp' => '📄 Kartu Tanda Penduduk (KTP)',
                            'npwp' => '💳 Nomor Pokok Wajib Pajak (NPWP)',
                            'cv' => '📝 Curriculum Vitae (CV)',
                            'contract' => '💼 Kontrak Kerja / SPK'
                        ];
                    @endphp

                    @foreach($docTypes as $key => $label)
                        @php $doc = $employee->documents->where('type', $key)->first(); @endphp
                        <div style="padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
                            <div style="min-width:0;flex:1;">
                                <div style="font-size:0.8rem;font-weight:800;color:var(--t1);text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">{{ $label }}</div>
                                @if($doc)
                                    <div style="font-size:0.65rem;color:var(--t4);text-overflow:ellipsis;overflow:hidden;white-space:nowrap;font-family:'JetBrains Mono',monospace;margin-top:0.1rem;">{{ $doc->file_name }}</div>
                                @else
                                    <div style="font-size:0.65rem;color:var(--danger);font-style:italic;margin-top:0.1rem;">Belum diunggah</div>
                                @endif
                            </div>
                            @if($doc)
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-secondary btn-xs">Unduh</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab 3: Recent Attendance --}}
            <div x-show="activeTab === 'attendance'" class="card animate-fadeSlideIn" style="display:none;" x-transition>
                <h4 style="font-size:0.65rem;font-weight:800;color:var(--em);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--border-dim);padding-bottom:0.5rem;margin-bottom:0.75rem;">10 Kehadiran Terakhir</h4>
                
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
                                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.78rem;">
                                        {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:0.1rem;">
                                            <div style="font-weight:800;color:var(--t2);font-family:'JetBrains Mono',monospace;font-size:0.82rem;">
                                                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '—' }}
                                            </div>
                                            @if($attendance->check_in_latitude)
                                                <div style="font-size:0.6rem;color:var(--em);display:flex;align-items:center;gap:0.2rem;">
                                                    <span style="width:5px;height:5px;border-radius:50%;background:var(--em);display:inline-block;"></span> GPS Verified
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display:flex;flex-direction:column;gap:0.1rem;">
                                            <div style="font-weight:800;color:var(--t2);font-family:'JetBrains Mono',monospace;font-size:0.82rem;">
                                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '—' }}
                                            </div>
                                            @if($attendance->check_out_latitude)
                                                <div style="font-size:0.6rem;color:var(--em);display:flex;align-items:center;gap:0.2rem;">
                                                    <span style="width:5px;height:5px;border-radius:50%;background:var(--em);display:inline-block;"></span> GPS Verified
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @switch($attendance->status)
                                            @case('present')    <span class="badge badge-success">Hadir</span> @break
                                            @case('late')       <span class="badge badge-warning">Terlambat</span> @break
                                            @case('absent')     <span class="badge badge-danger">Mangkir</span> @break
                                            @case('leave')      <span class="badge badge-orange">Cuti</span> @break
                                            @case('permission') <span class="badge badge-purple">Izin</span> @break
                                            @default            <span class="badge badge-gray">{{ $attendance->status }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:2rem;color:var(--t4);font-size:0.75rem;">
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
