@echo off
cd /d "%~dp0"
echo Stokara - Inventory Workspace
echo Pastikan MySQL pada Laragon sudah aktif.
echo Buka http://127.0.0.1:8000 di browser.
echo Tekan Ctrl+C untuk menghentikan aplikasi.
php artisan serve --host=127.0.0.1 --port=8000
pause