@extends('layouts.app')

@section('title', 'Kalender Kerja & Kehadiran')
@section('page-title', 'Kalender Kerja')
@section('breadcrumb', 'Utama › Kalender')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-850 dark:text-slate-100">Kalender Kerja & Kehadiran</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tinjauan hari libur nasional, akhir pekan, dan rekap absensi bulanan secara visual</p>
        </div>
        
        <!-- Legend Summary (Premium Pill-based Legend with bulletproof inline styles) -->
        <div class="flex flex-wrap gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #10b981;"></span> Hadir
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #f59e0b;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #f59e0b;"></span> Terlambat
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2); color: #8b5cf6;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #8b5cf6;"></span> Cuti
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2); color: #0ea5e9;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #0ea5e9;"></span> Izin / Sakit
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #f43f5e;"></span> Libur Resmi
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #ef4444;"></span> Alpa
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold" style="background-color: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); color: #ec4899;">
                <span class="w-1.5 h-1.5 rounded-full" style="background-color: #ec4899;"></span> Ulang Tahun
            </span>
        </div>
    </div>

    <!-- Filters & Selection (Sleek Glassmorphic Form with bulletproof label colors) -->
    <div class="card bg-slate-900/10 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/80 backdrop-blur-md rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('calendar.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Month Picker -->
            <div class="form-group mb-0">
                <label class="form-label text-[11px] uppercase tracking-wider font-bold" style="color: var(--text-muted);" for="month">Pilih Bulan</label>
                <input type="month" name="month" id="month" class="form-control mt-1 border-slate-300 dark:border-slate-800 text-xs py-2" style="color: var(--text-main); background-color: var(--card-bg);" value="{{ $month }}" onchange="this.form.submit()">
            </div>

            <!-- Employee Dropdown (Admins / Managers with high-contrast text fix) -->
            @if($canFilter)
            <div class="form-group mb-0">
                <label class="form-label text-[11px] uppercase tracking-wider font-bold" style="color: var(--text-muted);" for="user_id">Pilih Karyawan</label>
                <select name="user_id" id="user_id" class="form-control mt-1 border-slate-300 dark:border-slate-800 text-xs py-2" style="color: var(--text-main); background-color: var(--card-bg);" onchange="this.form.submit()">
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $selectedUser->id == $emp->id ? 'selected' : '' }} style="color: var(--text-main); background-color: var(--card-bg);">
                            {{ $emp->name }} ({{ $emp->nik }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="md:col-span-2 flex justify-end">
                <div class="text-right border-l border-slate-200 dark:border-slate-800 pl-4">
                    <span class="text-[10px] uppercase tracking-wider font-bold" style="color: var(--text-muted);">Menampilkan Kalender:</span>
                    <div class="font-bold text-slate-800 dark:text-slate-100 text-sm mt-0.5">{{ $selectedUser->name }}</div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold mt-1">{{ $selectedUser->role_label }}</span>
                </div>
            </div>
        </form>
    </div>

    <!-- Calendar Grid Card -->
    <div class="card p-4 md:p-6 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
        @php
            $carbonMonth = \Carbon\Carbon::parse($month . '-01');
            $daysInMonth = $carbonMonth->daysInMonth;
            $startDayOfWeek = $carbonMonth->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $monthName = $carbonMonth->translatedFormat('F Y');
            
            // Generate weeks padding
            $paddingDays = $startDayOfWeek - 1;
            
            $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $todayStr = today()->format('Y-m-d');
        @endphp

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base md:text-lg font-bold text-slate-850 dark:text-slate-100 tracking-tight">{{ $monthName }}</h3>
            
            <!-- Navigation Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('calendar.index', array_merge(request()->query(), ['month' => $carbonMonth->copy()->subMonth()->format('Y-m')])) }}" class="btn btn-secondary btn-sm rounded-xl py-1.5 px-3 hover:bg-slate-100 dark:hover:bg-slate-800" title="Bulan Sebelumnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <a href="{{ route('calendar.index', array_merge(request()->query(), ['month' => now()->format('Y-m')])) }}" class="btn btn-secondary btn-sm rounded-xl py-1.5 px-4 font-bold hover:bg-slate-100 dark:hover:bg-slate-800 text-xs">Bulan Ini</a>
                <a href="{{ route('calendar.index', array_merge(request()->query(), ['month' => $carbonMonth->copy()->addMonth()->format('Y-m')])) }}" class="btn btn-secondary btn-sm rounded-xl py-1.5 px-3 hover:bg-slate-100 dark:hover:bg-slate-800" title="Bulan Berikutnya">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Days of Week Header -->
        <div class="calendar-days-header">
            @foreach($daysOfWeek as $dayName)
                <div class="{{ $dayName == 'Sabtu' || $dayName == 'Minggu' ? 'text-rose-500/90' : 'text-slate-400 dark:text-slate-500' }}">{{ $dayName }}</div>
            @endforeach
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid" id="calendar-grid">
            <!-- Empty padding days -->
            @for($i = 0; $i < $paddingDays; $i++)
                <div class="calendar-day-empty"></div>
            @endfor

            <!-- Days of Month -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDate = $carbonMonth->copy()->day($day);
                    $dateStr = $currentDate->format('Y-m-d');
                    $dayOfWeek = $currentDate->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
                    $isWeekend = ($dayOfWeek == 6 || $dayOfWeek == 7);
                    $isToday = ($dateStr === $todayStr);
                    
                    $holiday = $holidays->get($dateStr);
                    $dayAttendances = $attendances->get($dateStr);
                    $dayBirthdays = isset($birthdays) ? $birthdays->get(sprintf('%02d', $day)) : null;
                    
                    // Style classes mapping
                    $cardClass = 'calendar-day-card ';
                    if ($isToday) {
                        $cardClass .= 'is-today ';
                    }
                    if ($holiday) {
                        $cardClass .= 'is-holiday ';
                    }
                    if ($isWeekend) {
                        $cardClass .= 'is-weekend ';
                    }
                @endphp

                <div class="{{ $cardClass }} group"
                     @if(auth()->user()->hasRole(['super_admin', 'hrd']))
                        onclick="handleDayClick('{{ $dateStr }}', '{{ $holiday ? $holiday->id : '' }}', '{{ $holiday ? addslashes($holiday->name) : '' }}', '{{ $holiday ? addslashes($holiday->description) : '' }}')"
                     @endif
                     data-date="{{ $dateStr }}">
                     
                    <!-- Date Number & Today Indicator -->
                    <div class="flex items-start justify-between">
                        <span class="text-sm font-extrabold font-mono {{ $isToday ? 'text-emerald-500' : ($isWeekend || $holiday ? 'text-rose-500' : 'text-slate-600 dark:text-slate-400') }}">
                            {{ sprintf('%02d', $day) }}
                        </span>
                        
                        @if($isToday)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[8px] font-extrabold uppercase tracking-wide bg-emerald-500 text-white leading-none">TODAY</span>
                        @endif
                    </div>

                    <!-- Birthdays list -->
                    @if($dayBirthdays && $dayBirthdays->isNotEmpty())
                        <div class="mt-1.5 flex flex-col gap-1 w-full items-center justify-center">
                            @foreach($dayBirthdays as $bUser)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-extrabold w-full justify-center" style="background-color: rgba(236, 72, 153, 0.12); border: 1px solid rgba(236, 72, 153, 0.25); color: #ec4899; text-shadow: 0 0 10px rgba(236, 72, 153, 0.1);" title="Ulang Tahun: {{ $bUser->name }}">
                                    🎂 {{ explode(' ', $bUser->name)[0] }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Centered Elegant Day Content Info (Using robust inline RGBA styles for high fidelity pops) -->
                    <div class="mt-2 flex-1 flex flex-col justify-center items-center w-full">
                        @if($holiday)
                            <!-- Public Holiday -->
                            <div class="flex flex-col items-center justify-center py-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold" style="background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.25); color: #f43f5e;">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: #f43f5e;"></span> Libur
                                </span>
                                <span class="text-[9px] font-bold text-rose-600 dark:text-rose-400 mt-1.5 text-center truncate max-w-[90px] leading-tight" title="{{ $holiday->name }}">
                                    {{ $holiday->name }}
                                </span>
                            </div>
                        @elseif($dayAttendances && $dayAttendances->isNotEmpty())
                            <!-- Attendance statuses stacked vertically -->
                            <div class="flex flex-col gap-2 w-full mt-1">
                                @foreach($dayAttendances as $attendance)
                                    @php
                                        $status = $attendance->status;
                                        $shiftName = $attendance->shift ? $attendance->shift->name : '';
                                    @endphp
                                    
                                    @if($status === 'present')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #10b981;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #10b981;"></span> Hadir{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                            <span class="text-[8px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }} - 
                                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : 'Kerja' }}
                                            </span>
                                        </div>
                                    @elseif($status === 'late')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #f59e0b;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #f59e0b;"></span> Terlambat{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                            <span class="text-[8px] font-mono mt-0.5" style="color: #f59e0b;">
                                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }} (+{{ $attendance->late_minutes }}m)
                                            </span>
                                        </div>
                                    @elseif($status === 'leave')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.25); color: #8b5cf6;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #8b5cf6;"></span> Cuti{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                            <span class="text-[8px] mt-0.5 text-center truncate max-w-[90px]" style="color: #8b5cf6;" title="{{ $attendance->notes }}">
                                                {{ $attendance->notes ?? 'Tahunan' }}
                                            </span>
                                        </div>
                                    @elseif($status === 'permission')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.25); color: #0ea5e9;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #0ea5e9;"></span> Izin{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                            <span class="text-[8px] mt-0.5 text-center truncate max-w-[90px]" style="color: #0ea5e9;" title="{{ $attendance->notes }}">
                                                {{ $attendance->notes ?? 'Izin' }}
                                            </span>
                                        </div>
                                    @elseif($status === 'sick')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.25); color: #f97316;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #f97316;"></span> Sakit{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                            <span class="text-[8px] mt-0.5 text-center truncate max-w-[90px]" style="color: #f97316;" title="{{ $attendance->notes }}">
                                                {{ $attendance->notes ?? 'Sakit' }}
                                            </span>
                                        </div>
                                    @elseif($status === 'absent')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #ef4444;"></span> Alpa{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                        </div>
                                    @elseif($status === 'holiday')
                                        <div class="flex flex-col items-center justify-center py-0.5 border-b border-slate-100 dark:border-slate-800/50 last:border-0 w-full">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold w-full justify-center" style="background-color: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.25); color: #f43f5e;">
                                                <span class="w-1 h-1 rounded-full" style="background-color: #f43f5e;"></span> Libur{{ $shiftName ? ' (' . $shiftName . ')' : '' }}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <!-- No attendance record -->
                            @if($isWeekend)
                                <div class="flex flex-col items-center justify-center py-1 opacity-80">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold" style="background-color: rgba(100, 116, 139, 0.1); border: 1px solid rgba(100, 116, 139, 0.2); color: #64748b;">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: #64748b;"></span> Weekend
                                    </span>
                                </div>
                            @elseif($currentDate->isPast() && !$isToday)
                                <!-- Past weekday without attendance record is considered Alpa (Absent) -->
                                <div class="flex flex-col items-center justify-center py-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444;">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: #ef4444;"></span> Alpa
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-300 dark:text-slate-800 select-none">-</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

<!-- AJAX Holiday Modal (Super Admin & HRD Only with explicit card-bg and border-color inline styles to match dark/light theme) -->
@if(auth()->user()->hasRole(['super_admin', 'hrd']))
<div id="holiday-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="rounded-3xl p-6 shadow-2xl transform scale-95 transition-transform duration-300" style="width: 100%; max-width: 450px; margin: 1.5rem; background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main);">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4 pb-3" style="border-bottom: 1px solid var(--border-color);">
            <h3 class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-main);" id="modal-title">Atur Hari Libur</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="holiday-form" onsubmit="submitHoliday(event)">
            @csrf
            <input type="hidden" id="holiday-id" name="holiday_id">
            
            <div class="form-group">
                <label class="form-label text-[10px] uppercase font-bold tracking-wide" style="color: var(--text-muted);" for="holiday-date">Tanggal Libur</label>
                <input type="date" id="holiday-date" name="date" class="form-control mt-1 border text-xs" style="color: var(--text-main); background-color: var(--input-bg); border-color: var(--input-border);" readonly required>
            </div>

            <div class="form-group">
                <label class="form-label text-[10px] uppercase font-bold tracking-wide" style="color: var(--text-muted);" for="holiday-name">Nama Hari Libur</label>
                <input type="text" id="holiday-name" name="name" class="form-control mt-1 border text-xs py-2" style="color: var(--text-main); background-color: var(--input-bg); border-color: var(--input-border);" placeholder="Contoh: Tahun Baru Masehi" required>
            </div>

            <div class="form-group">
                <label class="form-label text-[10px] uppercase font-bold tracking-wide" style="color: var(--text-muted);" for="holiday-description">Deskripsi (Opsional)</label>
                <textarea id="holiday-description" name="description" rows="3" class="form-control mt-1 border text-xs" style="color: var(--text-main); background-color: var(--input-bg); border-color: var(--input-border);" placeholder="Keterangan tambahan mengenai libur nasional..."></textarea>
            </div>

            <!-- Error container -->
            <div id="modal-errors" class="text-xs text-rose-500 mb-4 hidden bg-rose-500/5 border border-rose-500/10 p-3 rounded-xl"></div>

            <!-- Modal Actions -->
            <div class="flex gap-2 justify-end mt-6 pt-3" style="border-top: 1px solid var(--border-color);">
                <button type="button" onclick="closeModal()" class="btn btn-secondary text-xs rounded-xl py-2 px-4" style="color: var(--text-main); background-color: var(--card-bg); border: 1px solid var(--border-color);">Batal</button>
                <button type="button" id="btn-delete-holiday" onclick="deleteHoliday()" class="btn btn-danger text-xs rounded-xl py-2 px-4 hidden">Hapus Libur</button>
                <button type="submit" id="btn-submit-holiday" class="btn btn-primary text-xs rounded-xl py-2 px-5">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Modal controls (Admin / HRD Only)
    const modal = document.getElementById('holiday-modal');
    const form = document.getElementById('holiday-form');
    const modalTitle = document.getElementById('modal-title');
    const holidayIdInput = document.getElementById('holiday-id');
    const holidayDateInput = document.getElementById('holiday-date');
    const holidayNameInput = document.getElementById('holiday-name');
    const holidayDescInput = document.getElementById('holiday-description');
    const btnDelete = document.getElementById('btn-delete-holiday');
    const errorDiv = document.getElementById('modal-errors');

    function handleDayClick(dateStr, holidayId, holidayName, holidayDesc) {
        if (!modal) return;
        
        // Reset form errors
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        form.reset();

        holidayDateInput.value = dateStr;

        if (holidayId) {
            // Editing/deleting existing holiday
            modalTitle.textContent = 'Detail Hari Libur Resmi';
            holidayIdInput.value = holidayId;
            holidayNameInput.value = holidayName;
            holidayDescInput.value = holidayDesc;
            
            // Show delete button, set inputs to disabled if not editing
            btnDelete.classList.remove('hidden');
            document.getElementById('btn-submit-holiday').classList.add('hidden');
        } else {
            // Registering new holiday
            modalTitle.textContent = 'Daftarkan Hari Libur Baru';
            holidayIdInput.value = '';
            btnDelete.classList.add('hidden');
            document.getElementById('btn-submit-holiday').classList.remove('hidden');
        }

        // Open modal with smooth transition
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Submit New Holiday via AJAX
    function submitHoliday(event) {
        event.preventDefault();
        if (!form) return;

        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => { data[key] = value; });

        fetch('{{ route("calendar.holidays.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 || res.body.success) {
                closeModal();
                window.location.reload();
            } else {
                errorDiv.classList.remove('hidden');
                errorDiv.textContent = res.body.message || 'Terjadi kesalahan saat memproses data.';
            }
        })
        .catch(err => {
            errorDiv.classList.remove('hidden');
            errorDiv.textContent = 'Koneksi jaringan terganggu. Silakan coba kembali.';
        });
    }

    // Delete Holiday via AJAX
    function deleteHoliday() {
        const id = holidayIdInput.value;
        if (!id) return;

        if (!confirm('Apakah Anda yakin ingin menghapus hari libur resmi ini? Seluruh absensi otomatis karyawan pada hari tersebut juga akan dihapus.')) {
            return;
        }

        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';

        const deleteUrlTemplate = '{{ route("calendar.holidays.destroy", ":id") }}';
        fetch(deleteUrlTemplate.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200 || res.body.success) {
                closeModal();
                window.location.reload();
            } else {
                errorDiv.classList.remove('hidden');
                errorDiv.textContent = res.body.message || 'Gagal menghapus hari libur.';
            }
        })
        .catch(err => {
            errorDiv.classList.remove('hidden');
            errorDiv.textContent = 'Koneksi jaringan terganggu. Gagal menghapus.';
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Weekdays header styles with divider line */
    .calendar-days-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
        text-align: center;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 12px;
    }
    
    /* Premium Translucent Day Card */
    .calendar-day-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 12px;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        color: var(--text-main);
    }
    
    .calendar-day-card:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 16px -8px rgba(0, 0, 0, 0.05);
        border-color: var(--primary-light);
        z-index: 10;
    }
    
    /* Today highlighting with subtle inner gradient glow */
    .calendar-day-card.is-today {
        border: 2px solid var(--primary-light);
        background: linear-gradient(135deg, var(--card-bg), rgba(34, 197, 94, 0.05));
        box-shadow: 0 0 15px rgba(34, 197, 94, 0.12), inset 0 0 10px rgba(34, 197, 94, 0.02);
    }
    
    /* Beautiful subtle weekend styling */
    .calendar-day-card.is-weekend {
        background: rgba(248, 250, 252, 0.4);
        border-color: var(--border-color);
    }
    
    .dark .calendar-day-card.is-weekend {
        background: rgba(255, 255, 255, 0.015);
        border-color: var(--border-color);
    }
    
    /* Muted tint for national holidays */
    .calendar-day-card.is-holiday {
        background: rgba(239, 68, 68, 0.03);
        border-color: rgba(239, 68, 68, 0.2);
    }
    
    /* Grid fillers for empty trailing/leading calendar days */
    .calendar-day-empty {
        background: rgba(241, 245, 249, 0.2);
        border: 1px dashed var(--border-color);
        border-radius: 16px;
        min-height: 120px;
        opacity: 0.4;
    }
    
    .dark .calendar-day-empty {
        background: rgba(255, 255, 255, 0.005);
        border-color: var(--border-color);
    }
    
    /* Media responsiveness overrides */
    @media (max-width: 1024px) {
        .calendar-day-card {
            min-height: 105px;
            padding: 10px;
        }
    }
    
    @media (max-width: 768px) {
        .calendar-days-header {
            font-size: 0.65rem;
            gap: 4px;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .calendar-grid {
            gap: 6px;
        }
        .calendar-day-card {
            min-height: 85px;
            padding: 6px;
            border-radius: 10px;
        }
        .calendar-day-empty {
            min-height: 85px;
            border-radius: 10px;
        }
    }
</style>
@endpush
