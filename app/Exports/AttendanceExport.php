<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private Collection $attendances) {}

    public function collection(): Collection
    {
        return $this->attendances->map(fn($a) => [
            $a->date->format('d/m/Y'),
            $a->user->nik ?? '-',
            $a->user->name,
            $a->user->division->name ?? '-',
            $a->check_in_time ?? '-',
            $a->check_out_time ?? '-',
            match($a->status) {
                'present' => 'Hadir', 'late' => 'Terlambat',
                'absent' => 'Absen', 'permission' => 'Izin',
                'leave' => 'Cuti', 'sick' => 'Sakit',
                'holiday' => 'Libur', default => '-'
            },
            $a->late_minutes > 0 ? $a->late_minutes . ' menit' : '-',
            $a->check_in_distance ? $a->check_in_distance . ' m' : '-',
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'NIK', 'Nama', 'Divisi', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterlambatan', 'Jarak'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kehadiran';
    }
}
