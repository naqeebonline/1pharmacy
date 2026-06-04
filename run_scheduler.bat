@echo off
cd /d "D:\wamp64\www\hospital"
php artisan schedule:run >> NUL 2>&1
