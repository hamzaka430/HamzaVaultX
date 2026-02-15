@echo off
REM ============================================
REM Heroku Production Emergency Fix
REM Run this script to fix HTTP 500 error
REM ============================================

echo.
echo ========================================
echo HEROKU PRODUCTION EMERGENCY FIX
echo ========================================
echo.

echo [1/8] Setting Heroku-compatible logging...
call heroku config:set LOG_CHANNEL=errorlog --app hamzavaultx

echo.
echo [2/8] Enabling debug mode temporarily...
call heroku config:set APP_DEBUG=true --app hamzavaultx

echo.
echo [3/8] Setting local filesystem...
call heroku config:set FILESYSTEM_DISK=local --app hamzavaultx

echo.
echo [4/8] Removing AWS credentials temporarily...
call heroku config:unset AWS_ACCESS_KEY_ID AWS_SECRET_ACCESS_KEY AWS_ENDPOINT AWS_DEFAULT_REGION AWS_BUCKET AWS_USE_PATH_STYLE_ENDPOINT --app hamzavaultx

echo.
echo [5/8] Clearing Laravel caches...
call heroku run "php artisan optimize:clear" --app hamzavaultx

echo.
echo [6/8] Checking if Vite assets exist...
call heroku run "ls -la public/build/" --app hamzavaultx

echo.
echo [7/8] Restarting app...
call heroku restart --app hamzavaultx

echo.
echo [8/8] Opening logs...
echo.
echo ========================================
echo FIX APPLIED!
echo ========================================
echo.
echo Now visit: https://hamzavaultx.herokuapp.com/
echo.
echo Watching logs below (Ctrl+C to exit):
echo ========================================
echo.

call heroku logs --tail --app hamzavaultx
