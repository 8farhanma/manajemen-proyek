@echo off
cd /d c:\xampp-pa\htdocs\manajemen-proyek
php artisan reminders:send >> C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log 2>&1
