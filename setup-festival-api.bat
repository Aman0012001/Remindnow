@echo off
echo ========================================
echo Festival API - Quick Setup Script
echo ========================================
echo.

echo [1/5] Installing Google API Client...
call composer require google/apiclient:"^2.0"
if %errorlevel% neq 0 (
    echo ERROR: Failed to install Google API Client
    pause
    exit /b 1
)
echo ✓ Google API Client installed
echo.

echo [2/5] Installing Firebase PHP SDK...
call composer require kreait/firebase-php:"^7.0"
if %errorlevel% neq 0 (
    echo ERROR: Failed to install Firebase SDK
    pause
    exit /b 1
)
echo ✓ Firebase SDK installed
echo.

echo [3/5] Running database migrations...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Migrations failed
    pause
    exit /b 1
)
echo ✓ Migrations completed
echo.

echo [4/5] Clearing cache...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call composer dump-autoload
echo ✓ Cache cleared
echo.

echo [5/5] Setup complete!
echo.
echo ========================================
echo Next Steps:
echo ========================================
echo 1. Start server: php artisan serve
echo 2. Get token: php artisan tinker
echo    Then run: $user = App\Models\User::first(); $token = $user->createToken('test')->plainTextToken; echo $token;
echo 3. Test in Postman: http://localhost:8000/api/festivals/upcoming
echo.
echo See LOCALHOST_TESTING_GUIDE.md for complete instructions
echo ========================================
echo.
pause
