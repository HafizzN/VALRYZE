# Self-elevate the script if not running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "=========================================================================" -ForegroundColor Yellow
    Write-Host "PENTING: Skrip ini memerlukan hak akses Administrator untuk mengubah" -ForegroundColor Yellow
    Write-Host "konfigurasi waktu sistem Windows." -ForegroundColor Yellow
    Write-Host "=========================================================================" -ForegroundColor Yellow
    Write-Host "Mencoba menjalankan ulang sebagai Administrator..." -ForegroundColor Cyan
    
    Start-Process powershell -ArgumentList "-NoProfile -ExecutionPolicy Bypass -File `"$PSCommandPath`"" -Verb RunAs
    exit
}

Write-Host "=========================================================================" -ForegroundColor Green
Write-Host "MENGONFIGURASI SINKRONISASI WAKTU OTOMATIS (NTP SERVER) PADA WINDOWS" -ForegroundColor Green
Write-Host "=========================================================================" -ForegroundColor Green

# 1. Mengatur server NTP tujuan (menggunakan pool regional Indonesia & global)
$ntpServers = "0.id.pool.ntp.org,0x9 1.id.pool.ntp.org,0x9 pool.ntp.org,0x9 time.windows.com,0x9"
Write-Host "[1/5] Menetapkan peer list NTP ke: $ntpServers" -ForegroundColor Cyan
w32tm /config /manualpeerlist:"$ntpServers" /syncfromflags:manual /reliable:YES /update

if ($LASTEXITCODE -ne 0) {
    Write-Host "Gagal mengonfigurasi manualpeerlist." -ForegroundColor Red
    exit 1
}

# 2. Mengatur tipe startup Windows Time service (w32time) menjadi Otomatis
Write-Host "[2/5] Mengatur Windows Time Service agar berjalan secara Otomatis..." -ForegroundColor Cyan
Set-Service w32time -StartupType Automatic

# 3. Merestart Windows Time Service untuk menerapkan perubahan
Write-Host "[3/5] Memulai ulang Windows Time Service..." -ForegroundColor Cyan
Restart-Service w32time

# 4. Melakukan sinkronisasi waktu paksa sekarang
Write-Host "[4/5] Melakukan sinkronisasi waktu dengan server NTP..." -ForegroundColor Cyan
w32tm /resync /force

# 5. Menampilkan status sinkronisasi terbaru
Write-Host "[5/5] Memverifikasi status konfigurasi waktu..." -ForegroundColor Cyan
Write-Host "-------------------------------------------------------------------------"
w32tm /query /status
Write-Host "-------------------------------------------------------------------------"

Write-Host "Konfigurasi NTP berhasil diselesaikan." -ForegroundColor Green
Write-Host "Tekan tombol apa saja untuk keluar..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
