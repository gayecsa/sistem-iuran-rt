@echo off
cd /d d:\laragon\www\iuran-rt001
set phpPath=d:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe

echo Starting migration and seeding... > migration_output.txt 2>&1
"%phpPath%" artisan migrate:fresh --seed --force >> migration_output.txt 2>&1

echo. >> migration_output.txt
echo Migration finished at %date% %time% >> migration_output.txt
