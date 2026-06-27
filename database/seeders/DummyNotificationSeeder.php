<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class DummyNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Super Administrator

        $notifications = [
            [
                'user_id' => $userId,
                'type' => 'leave',
                'title' => 'Pengajuan Cuti Baru ✈️',
                'message' => 'Aditya Pratama mengajukan cuti tahunan selama 3 hari (Senin - Rabu).',
                'url' => route('leave.index'),
                'icon' => 'plane',
                'color' => '#8b5cf6',
                'created_at' => now()->subMinutes(12)
            ],
            [
                'user_id' => $userId,
                'type' => 'attendance',
                'title' => 'Peringatan Terlambat ⏰',
                'message' => 'Dian Sastrowardoyo melakukan check-in terlambat 25 menit hari ini.',
                'url' => route('reports.lateness'),
                'icon' => 'clock',
                'color' => '#f59e0b',
                'created_at' => now()->subMinutes(45)
            ],
            [
                'user_id' => $userId,
                'type' => 'overtime',
                'title' => 'Pengajuan Lembur Baru 💼',
                'message' => 'Candra Wijaya meminta persetujuan lembur selama 2 jam malam ini.',
                'url' => route('overtime.index'),
                'icon' => 'briefcase',
                'color' => '#0ea5e9',
                'created_at' => now()->subHours(2)
            ],
            [
                'user_id' => $userId,
                'type' => 'system',
                'title' => 'Pencadangan Berhasil 💾',
                'message' => 'Backup database portal VALRYZE berhasil disalin ke Google Cloud Storage.',
                'url' => route('dashboard'),
                'icon' => 'database',
                'color' => '#10b981',
                'created_at' => now()->subHours(6)
            ],
            [
                'user_id' => $userId,
                'type' => 'document',
                'title' => 'Dokumen SOP Baru 📄',
                'message' => 'Siti Rahayu mengunggah dokumen \'SOP Penilaian Kinerja Karyawan v1.2\'.',
                'url' => route('documents.index'),
                'icon' => 'document',
                'color' => '#a78bfa',
                'created_at' => now()->subDays(1)
            ]
        ];

        foreach ($notifications as $n) {
            Notification::create($n);
        }
    }
}
