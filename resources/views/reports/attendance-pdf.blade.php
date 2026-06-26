<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran Karyawan — {{ $month }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9pt;
            color: #444;
            margin: 0;
        }
        .report-meta {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .report-meta td {
            padding: 2px 0;
        }
        .report-meta .right {
            text-align: right;
        }
        /* Table Styles */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.report-table th {
            background-color: #f3f4f6;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #111;
            padding: 5px 4px;
            text-align: left;
        }
        table.report-table td {
            border: 1px solid #111;
            padding: 5px 4px;
        }
        table.report-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: Courier, monospace;
        }
        /* Badges for print */
        .status-present { color: #047857; font-weight: bold; }
        .status-late { color: #b45309; font-weight: bold; }
        .status-absent { color: #b91c1c; font-weight: bold; }
        .status-leave { color: #c2410c; font-weight: bold; }
        .status-permission { color: #6d28d9; font-weight: bold; }
        .status-holiday { color: #64748b; font-weight: bold; }
    </style>
</head>
<body>

    <!-- Report Header -->
    <div class="header">
        <h1>Laporan Rekapitulasi Kehadiran Karyawan</h1>
        <p>Smart HR Portal — Sistem Absensi & Manajemen Kepegawaian Digital</p>
    </div>

    <!-- Metadata -->
    <table class="report-meta">
        <tr>
            <td>
                Bulan Laporan: <strong>{{ \Carbon\Carbon::parse($month . '-01')->isoFormat('MMMM Y') }}</strong>
            </td>
            <td class="right">
                Tanggal Cetak: {{ now()->isoFormat('D MMMM Y - HH:mm') }} WIB
            </td>
        </tr>
    </table>

    <!-- Main Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px">No</th>
                <th style="width: 75px">Tanggal</th>
                <th style="width: 70px">NIK</th>
                <th>Nama Karyawan</th>
                <th style="width: 95px">Divisi</th>
                <th style="width: 95px">Jabatan</th>
                <th style="width: 55px" class="text-center">Jam Masuk</th>
                <th style="width: 55px" class="text-center">Jam Pulang</th>
                <th style="width: 50px" class="text-center">Late (Min)</th>
                <th style="width: 65px" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td class="text-center font-mono">{{ $loop->iteration }}</td>
                    <td class="font-mono text-center">{{ \Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                    <td class="font-mono">{{ $attendance->user->nik ?? '-' }}</td>
                    <td><strong>{{ $attendance->user->name ?? '-' }}</strong></td>
                    <td>{{ $attendance->user->division->name ?? '-' }}</td>
                    <td>{{ $attendance->user->position->name ?? '-' }}</td>
                    <td class="font-mono text-center">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') : '--:--:--' }}</td>
                    <td class="font-mono text-center">{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') : '--:--:--' }}</td>
                    <td class="font-mono text-center">
                        {{ $attendance->late_minutes > 0 ? $attendance->late_minutes : '0' }}
                    </td>
                    <td class="text-center">
                        @switch($attendance->status)
                            @case('present')
                                <span class="status-present">HADIR</span>
                                @break
                            @case('late')
                                <span class="status-late">TELAT</span>
                                @break
                            @case('absent')
                                <span class="status-absent">MANGKIR</span>
                                @break
                            @case('leave')
                                <span class="status-leave">CUTI</span>
                                @break
                            @case('permission')
                                <span class="status-permission">IZIN</span>
                                @break
                            @case('holiday')
                                <span class="status-holiday">LIBUR</span>
                                @break
                            @default
                                <span>{{ strtoupper($attendance->status) }}</span>
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">
                        Tidak ada rekaman data kehadiran pada periode bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
