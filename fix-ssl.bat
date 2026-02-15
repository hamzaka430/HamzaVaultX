@echo off
echo ========================================
echo  Fixing SSL Certificate Issue
echo ========================================
echo.
echo This script will download the CA certificate bundle
echo and configure PHP to use it.
echo.

REM Get PHP ini location
echo Finding PHP configuration...
php -r "echo 'PHP ini file: ' . php_ini_loaded_file() . PHP_EOL;"
echo.

REM Download CA certificate bundle
echo Downloading CA certificate bundle...
powershell -Command "& {Invoke-WebRequest -Uri 'https://curl.se/ca/cacert.pem' -OutFile 'cacert.pem'}"

if exist cacert.pem (
    echo ✓ CA certificate bundle downloaded successfully
    echo.
    echo The certificate bundle is saved at:
    cd
    echo.
    echo ========================================
    echo  MANUAL CONFIGURATION REQUIRED
    echo ========================================
    echo.
    echo Please follow these steps:
    echo.
    echo 1. Find your php.ini file location (shown above)
    echo 2. Open php.ini in a text editor as Administrator
    echo 3. Find the line: ;curl.cainfo =
    echo 4. Replace it with: curl.cainfo = "%CD%\cacert.pem"
    echo 5. Find the line: ;openssl.cafile=
    echo 6. Replace it with: openssl.cafile = "%CD%\cacert.pem"
    echo 7. Save the file and restart your PHP server
    echo.
    echo OR run this command in PowerShell as Administrator:
    echo.
    echo (Get-Content "PATH_TO_PHP_INI" ^| ForEach-Object { $_ -replace ';curl.cainfo =', 'curl.cainfo = "%CD%\cacert.pem"' -replace ';openssl.cafile=', 'openssl.cafile = "%CD%\cacert.pem"' }) ^| Set-Content "PATH_TO_PHP_INI"
    echo.
) else (
    echo ✗ Failed to download CA certificate bundle
    echo Please download manually from: https://curl.se/ca/cacert.pem
    echo.
)

pause
