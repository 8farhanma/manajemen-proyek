@echo off
cd /d "C:\xampp-pa\htdocs\manajemen-proyek"
"C:\xampp-pa\php\php.exe" artisan reminders:send --force
if %ERRORLEVEL% EQU 0 (
    echo Command completed successfully
) else (
    echo Command failed with error code %ERRORLEVEL%
)
exit /b 0
