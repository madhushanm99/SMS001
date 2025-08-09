@echo off
echo Starting Laravel Reverb WebSocket Server...
echo.
echo Make sure to run this in a separate terminal window
echo WebSocket server will run on http://127.0.0.1:8080
echo.
php artisan reverb:start --host=127.0.0.1 --port=8080 --hostname=127.0.0.1
pause
