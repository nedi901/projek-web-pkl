@echo off
rem Jalankan server lalu buka browser langsung ke halaman login.
rem Cara pakai: klik dua kali file ini (letakkan di folder yang sama dengan file "artisan").
cd /d "%~dp0"
start "" cmd /c "timeout /t 3 /nobreak >nul & start "" http://127.0.0.1:8000/login"
php artisan serve
