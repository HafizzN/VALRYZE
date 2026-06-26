@echo off
:: register-queue-service.bat
:: Right-click and "Run as Administrator" to register Laravel Queue Scheduled Task

:: Check for Administrator privileges
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [OK] Berjalan sebagai Administrator.
) else (
    echo [WARNING] Menjalankan ulang sebagai Administrator...
    powershell -Command "Start-Process -FilePath '%0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
echo Mendaftarkan Laravel Queue Worker Scheduled Task...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0automate-queue.ps1"
echo.
echo Selesai! Tekan tombol apa saja untuk keluar.
pause
