# automate-queue.ps1
# Jalankan PowerShell ini sebagai Administrator (atau melalui register-queue-service.bat)
# untuk mendaftarkan Laravel Queue Worker & Command Scheduler sebagai Windows Scheduled Tasks otomatis.

$workingDir = "C:\Users\HAFIZUL HANIF\Documents\Portal"
$queueTaskName = "LaravelQueueWorker"
$scheduleTaskName = "LaravelScheduler"

# Pemeriksaan Hak Akses Administrator
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not ($currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator))) {
    Write-Warning "Harap jalankan PowerShell sebagai Administrator untuk mendaftarkan Scheduled Tasks!"
    Exit
}

Write-Host "Mendaftarkan Scheduled Tasks untuk Laravel di Windows..." -ForegroundColor Cyan

# ==========================================
# 1. DAFTARKAN QUEUE WORKER (DAEMON PROCESS)
# ==========================================
# Aksi: Menjalankan php artisan queue:work
$queueAction = New-ScheduledTaskAction -Execute "php" -Argument "artisan queue:work --sleep=3 --tries=3" -WorkingDirectory $workingDir

# Pemicu: Berjalan saat komputer menyala (At Startup)
$queueTrigger = New-ScheduledTaskTrigger -AtStartup

# Pengaturan:
# - Tetap berjalan saat menggunakan baterai
# - Restart otomatis jika gagal (RestartCount 999 kali, jeda 1 menit)
# - Izinkan durasi berjalan tanpa batas (365 hari)
$queueSettings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RestartCount 999 `
    -RestartInterval (New-TimeSpan -Minutes 1) `
    -ExecutionTimeLimit (New-TimeSpan -Days 365)

# Daftarkan Queue Task ke Sistem
Register-ScheduledTask `
    -TaskName $queueTaskName `
    -Action $queueAction `
    -Trigger $queueTrigger `
    -Settings $queueSettings `
    -User "SYSTEM" `
    -Description "Smart HR Portal Laravel Queue Worker - Background Daemon Process" `
    -Force

# ==========================================
# 2. DAFTARKAN COMMAND SCHEDULER (EVERY MINUTE)
# ==========================================
# Aksi: Menjalankan php artisan schedule:run
$scheduleAction = New-ScheduledTaskAction -Execute "php" -Argument "artisan schedule:run" -WorkingDirectory $workingDir

# Pemicu: Mulai hari ini dan ulangi setiap 1 menit selamanya
$scheduleTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1)

# Pengaturan:
# - Tetap berjalan saat menggunakan baterai
# - Mulai segera jika jadwal terlewat
$scheduleSettings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable

# Daftarkan Schedule Task ke Sistem
Register-ScheduledTask `
    -TaskName $scheduleTaskName `
    -Action $scheduleAction `
    -Trigger $scheduleTrigger `
    -Settings $scheduleSettings `
    -User "SYSTEM" `
    -Description "Smart HR Portal Laravel Command Scheduler - Runs every minute" `
    -Force

# ==========================================
# 3. AKTIFKAN DAN JALANKAN TUGAS SEKARANG JUGA
# ==========================================
Write-Host "Memulai dan mengaktifkan tugas Scheduled Tasks sekarang..." -ForegroundColor Yellow
Start-ScheduledTask -TaskName $queueTaskName
Start-ScheduledTask -TaskName $scheduleTaskName

Write-Host ""
Write-Host "=========================================================================" -ForegroundColor Cyan
Write-Host " Scheduled Tasks Berhasil Didaftarkan dan Dijalankan" -ForegroundColor Green
Write-Host "=========================================================================" -ForegroundColor Cyan
Write-Host "1. '$queueTaskName' : Menangani antrean tugas latar belakang (email, dll)." -ForegroundColor Gray
Write-Host "2. '$scheduleTaskName' : Menjalankan tugas terjadwal (detak jantung, akrual, dll)." -ForegroundColor Gray
Write-Host "3. Kedua layanan berjalan secara otomatis & permanen di latar belakang." -ForegroundColor Gray
Write-Host "=========================================================================" -ForegroundColor Cyan
Write-Host ""
