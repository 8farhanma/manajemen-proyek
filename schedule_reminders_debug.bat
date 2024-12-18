@echo off
echo %date% %time% - Starting Laravel Scheduler >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log"

cd /d "C:\xampp-pa\htdocs\manajemen-proyek"
echo %date% %time% - Changed to directory: %CD% >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log"

"C:\xampp-pa\php\php.exe" artisan schedule:run >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log" 2>&1
if %ERRORLEVEL% EQU 0 (
    echo %date% %time% - Scheduler completed successfully >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log"
) else (
    echo %date% %time% - Scheduler failed with error code %ERRORLEVEL% >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log"
)

echo %date% %time% - Finished Laravel Scheduler >> "C:\xampp-pa\htdocs\manajemen-proyek\storage\logs\scheduler.log"
exit 0
