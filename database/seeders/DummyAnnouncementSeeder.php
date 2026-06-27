<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class DummyAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // Super Administrator

        // Clear existing announcements to avoid cluttering
        Announcement::truncate();

        $announcements = [
            [
                'user_id' => $userId,
                'category' => 'holiday',
                'title' => 'Pengumuman Libur Nasional & Cuti Bersama Idul Adha 🌙',
                'content' => 'Sesuai dengan Keputusan Bersama Menteri, PT. Smart Teknologi Indonesia menetapkan libur nasional Idul Adha dan cuti bersama pada tanggal 29 dan 30 Juni 2026. Kegiatan operasional kantor ditiadakan dan akan aktif kembali pada tanggal 1 Juli 2026. Selamat berkumpul bersama keluarga!',
                'is_pinned' => true,
                'published_at' => now()->subDays(2),
                'expired_at' => now()->addDays(7)
            ],
            [
                'user_id' => $userId,
                'category' => 'meeting',
                'title' => 'Town Hall Meeting VALRYZE Q2 & Sosialisasi OKR 🎯',
                'content' => 'Diundang kepada seluruh karyawan PT. Smart Teknologi Indonesia untuk menghadiri Town Hall Meeting Triwulan II pada hari Jumat, 3 Juli 2026, pukul 14:00 WIB secara Hybrid di Ruang Serbaguna Lantai 3 dan via Zoom. Kita akan membahas evaluasi performa kuartal kedua dan sosialisasi OKR baru.',
                'is_pinned' => true,
                'published_at' => now()->subHours(5),
                'expired_at' => now()->addDays(10)
            ],
            [
                'user_id' => $userId,
                'category' => 'info',
                'title' => 'Pemberlakuan Kebijakan WFA & Shift Fleksibel Baru 💻',
                'content' => 'Mulai 1 Juli 2026, manajemen akan memberlakukan ujicoba kebijakan Work From Anywhere (WFA) maksimal 2 hari dalam seminggu bagi divisi IT dan HR. Silakan hubungi kepala divisi masing-masing untuk koordinasi jadwal piket harian agar layanan kantor tetap berjalan prima.',
                'is_pinned' => false,
                'published_at' => now()->subDays(1),
                'expired_at' => now()->addDays(14)
            ],
            [
                'user_id' => $userId,
                'category' => 'activity',
                'title' => 'Kompetisi Badminton & Fun Match Internal VALRYZE 🏸',
                'content' => 'Pendaftaran kompetisi Badminton Ganda Putra & Ganda Campuran VALRYZE Cup resmi dibuka! Pertandingan akan diselenggarakan pada hari Sabtu depan di GOR Senayan. Dapatkan trofi bergilir dan hadiah menarik. Hubungi divisi Activity untuk mendaftar.',
                'is_pinned' => false,
                'published_at' => now()->subHours(10),
                'expired_at' => now()->addDays(5)
            ]
        ];

        foreach ($announcements as $a) {
            Announcement::create($a);
        }
    }
}
