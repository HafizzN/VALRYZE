<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AccrueLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:accrue {--force : Memaksa jalankan akrual meskipun sudah pernah dijalankan bulan ini}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically accrue 1 day of annual leave for active employees monthly.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $alreadyRun = \App\Models\ActivityLog::where('action', 'accrue_leave')
                ->where('model_type', 'System')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->exists();

            if ($alreadyRun) {
                $this->warn("⚠️ Akrual cuti untuk bulan " . now()->translatedFormat('F Y') . " sudah pernah dijalankan sebelumnya. Gunakan opsi --force jika ingin tetap menjalankan ulang.");
                return \Symfony\Component\Console\Command\Command::FAILURE;
            }
        }

        $users = User::where('status', 'active')->get();
        $count = 0;

        foreach ($users as $user) {
            $user->increment('annual_leave_quota', 1);
            $count++;
        }

        // Log the system activity
        \App\Models\ActivityLog::log('accrue_leave', 'System', null, [
            'count' => $count,
            'force' => $this->option('force')
        ]);

        $this->info("Berhasil menambahkan 1 hari jatah cuti untuk {$count} karyawan aktif.");
        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
