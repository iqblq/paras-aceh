@echo off
title PARAS Kanwil DJPb Provinsi Aceh - Laravel 11 MVC
color 0b
echo ========================================================
echo    PARAS - PORTAL REALISASI ANGGARAN SATKER
echo    KANWIL DJPB PROVINSI ACEH (LARAVEL 11 MVC)
echo ========================================================
echo.
echo Membuka browser: http://localhost:8000 ...
start http://localhost:8000
echo.
echo Menjalankan Laravel Development Server pada port 8000...
echo Tekan CTRL + C untuk menghentikan server.
echo.
"C:\xampp\php\php.exe" artisan serve --port=8000
pause
