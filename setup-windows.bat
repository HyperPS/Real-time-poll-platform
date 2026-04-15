@echo off
REM Real-Time Live Poll Platform - Windows Setup Helper
REM This script validates the environment and guides users through setup

title Real-Time Poll Platform - Setup Helper
color 0A

echo.
echo =====================================================
echo  Real-Time Live Poll Platform - Setup Helper
echo =====================================================
echo.

REM Check for XAMPP PHP
echo Checking for PHP installation...
set PHP_PATH=C:\xampp\php\php.exe

if exist "%PHP_PATH%" (
    echo [OK] PHP found at %PHP_PATH%
    for /f "tokens=*" %%A in ('"%PHP_PATH%" -v') do (
        echo.
        echo %%A
        goto php_found
    )
) else (
    echo [ERROR] PHP not found at %PHP_PATH%
    echo.
    echo Please install XAMPP from: https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

:php_found
echo.
echo Checking for MySQL...
set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe

if exist "%MYSQL_PATH%" (
    echo [OK] MySQL found at %MYSQL_PATH%
) else (
    echo [WARNING] MySQL not found at %MYSQL_PATH%
    echo [INFO] You'll need to set up the database manually
)

echo.
echo =====================================================
echo  Setup Steps
echo =====================================================
echo.
echo 1. Start XAMPP Control Panel (xampp-control.exe)
echo 2. Click START for Apache and MySQL
echo 3. Wait for both to show "Running" status
echo 4. Open browser and go to: http://localhost/phpmyadmin
echo 5. Import database/schema.sql to create tables
echo 6. Copy this Mohit folder to: C:\xampp\htdocs\Mohit
echo 7. Access application at: http://localhost/Mohit/public/
echo.
echo Login with:
echo   Email: admin@polling.test
echo   Password: admin123
echo.
echo =====================================================
echo.
echo Press any key to view detailed setup guide...
pause > nul

REM Open localhost setup guide
start LOCALHOST-SETUP.md

echo.
echo Setup guide opened in your default text editor.
echo.
echo Next steps:
echo 1. Follow the setup instructions in LOCALHOST-SETUP.md
echo 2. Start XAMPP services
echo 3. Create the database
echo 4. Access http://localhost/Mohit/public/
echo.
pause
