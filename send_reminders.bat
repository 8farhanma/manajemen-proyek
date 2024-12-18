@echo off
echo %date% %time% - Starting SendReminders >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
cd /d c:\xampp-pa\htdocs\manajemen-proyek
echo %date% %time% - Changed directory to: %CD% >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"

REM Set PHP path
set PHP_PATH=C:\xampp-pa\php
set PATH=%PHP_PATH%;%PATH%
echo %date% %time% - PHP Path set to: %PHP_PATH% >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"

REM Check if PHP exists
if exist "%PHP_PATH%\php.exe" (
    echo %date% %time% - PHP executable found >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
) else (
    echo %date% %time% - ERROR: PHP executable not found >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
    exit /b 1
)

REM Run the command
echo %date% %time% - Running command: php artisan reminders:send --force >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
"%PHP_PATH%\php.exe" artisan reminders:send --force -v >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log" 2>&1
if %ERRORLEVEL% EQU 0 (
    echo %date% %time% - Command executed successfully >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
) else (
    echo %date% %time% - ERROR: Command failed with code %ERRORLEVEL% >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"
)

echo %date% %time% - Finished SendReminders >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\windows_reminders.log"