@echo off
cd /d "C:\xampp-pa\htdocs\manajemen-proyek"
"C:\xampp-pa\php\php.exe" artisan schedule:run
exit
