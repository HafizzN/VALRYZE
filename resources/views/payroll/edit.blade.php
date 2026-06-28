@extends('layouts.app')

@section('title', 'Ubah Gaji Karyawan')
@section('page-title', 'Ubah Gaji')
@section('breadcrumb', 'Payroll / Pengaturan Gaji / Edit')

@section('content')
<div class="max-w-2xl mx-auto font-sans">
    <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">Atur Komponen Gaji</h3>
            <a href="{{ route('payroll.index') }}" class="text-xs text-slate-500 hover:text-slate-700 flex items-center gap-1 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Employee Profile Info Box -->
        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
            <div class="avatar text-sm w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-800">{{ $user->initials }}</div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">{{ $user->name }}</h4>
                <p class="text-slate-500 text-[10px]">{{ $user->position->name ?? '-' }} • {{ $user->division->name ?? '-' }}</p>
                <p class="text-slate-400 text-[9px] font-mono mt-0.5">NIK: {{ $user->nik ?? 'TIDAK TERTAUT' }}</p>
            </div>
        </div>

        <form action="{{ route('payroll.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Info text -->
            <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-xl">
                <p class="text-[10px] text-indigo-700 leading-relaxed">
                    💡 **Petunjuk:** Kosongkan kolom parameter di bawah ini untuk mengaktifkan kalkulasi otomatis / default yang dihitung berdasarkan tingkatan jabatan karyawan.
                </p>
            </div>

            <!-- Basic Salary -->
            <div class="space-y-1.5">
                <label for="basic_salary" class="block text-xs font-bold text-slate-600">Gaji Pokok (Rupiah)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                    <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary', $user->basic_salary ? (int)$user->basic_salary : '') }}" class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono" placeholder="Contoh: 8500000">
                </div>
                @error('basic_salary')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Allowance -->
            <div class="space-y-1.5">
                <label for="allowance" class="block text-xs font-bold text-slate-600">Tunjangan Jabatan / Operasional (Rupiah)</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                    <input type="number" name="allowance" id="allowance" value="{{ old('allowance', $user->allowance ? (int)$user->allowance : '') }}" class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono" placeholder="Contoh: 1200000">
                </div>
                @error('allowance')
                    <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- BPJS Deduction -->
                <div class="space-y-1.5">
                    <label for="bpjs_deduction" class="block text-xs font-bold text-slate-600">Potongan BPJS (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" name="bpjs_deduction" id="bpjs_deduction" value="{{ old('bpjs_deduction', $user->bpjs_deduction ? (int)$user->bpjs_deduction : '') }}" class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono" placeholder="Contoh: 150000">
                    </div>
                    @error('bpjs_deduction')
                        <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tax Deduction -->
                <div class="space-y-1.5">
                    <label for="tax_deduction" class="block text-xs font-bold text-slate-600">Potongan Pajak PPh21 (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" name="tax_deduction" id="tax_deduction" value="{{ old('tax_deduction', $user->tax_deduction ? (int)$user->tax_deduction : '') }}" class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono" placeholder="Contoh: 250000">
                    </div>
                    @error('tax_deduction')
                        <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Live Calculation Display Card -->
            <div class="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 mt-6 space-y-3">
                <h4 class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider">Kalkulator Gaji Real-time</h4>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Gaji Pokok (+)</span>
                    <span class="font-mono" id="calc-basic">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Tunjangan (+)</span>
                    <span class="font-mono" id="calc-allowance">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Total Potongan (-)</span>
                    <span class="font-mono text-rose-400" id="calc-deductions">Rp 0</span>
                </div>
                <div class="border-t border-slate-800 pt-2 flex justify-between items-center text-sm font-bold">
                    <span class="text-slate-200">Estimasi Bersih (THP)</span>
                    <span class="font-mono text-emerald-400 text-base" id="calc-net">Rp 0</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('payroll.index') }}" class="btn btn-secondary text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 border-none rounded-xl px-4 py-2">Batal</a>
                <button type="submit" class="btn btn-primary text-xs bg-indigo-600 hover:bg-indigo-700 border-none rounded-xl px-4 py-2">Simpan Parameter</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBasic = document.getElementById('basic_salary');
        const inputAllowance = document.getElementById('allowance');
        const inputBpjs = document.getElementById('bpjs_deduction');
        const inputTax = document.getElementById('tax_deduction');

        const calcBasic = document.getElementById('calc-basic');
        const calcAllowance = document.getElementById('calc-allowance');
        const calcDeductions = document.getElementById('calc-deductions');
        const calcNet = document.getElementById('calc-net');

        // Initial default values calculation based on position/role if empty
        const defaultBasic = 6500000;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function calculate() {
            let basic = parseFloat(inputBasic.value) || 0;
            let isDefault = basic === 0;
            
            // If empty, show fallback estimation
            if (isDefault) {
                basic = defaultBasic;
            }

            let allowance = parseFloat(inputAllowance.value) || (isDefault ? basic * 0.15 : 0);
            let bpjs = parseFloat(inputBpjs.value) || (isDefault ? basic * 0.03 : 0);
            let tax = parseFloat(inputTax.value) || (isDefault ? basic * 0.05 : 0);

            let deductions = bpjs + tax;
            let net = basic + allowance - deductions;

            calcBasic.textContent = formatRupiah(basic) + (isDefault ? ' (Default)' : '');
            calcAllowance.textContent = formatRupiah(allowance) + (isDefault ? ' (Default 15%)' : '');
            calcDeductions.textContent = formatRupiah(deductions) + (isDefault ? ' (Default 8%)' : '');
            calcNet.textContent = formatRupiah(net);
        }

        [inputBasic, inputAllowance, inputBpjs, inputTax].forEach(input => {
            input.addEventListener('input', calculate);
        });

        calculate(); // Run once initially
    });
</script>
@endsection
