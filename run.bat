@echo off
title Enterprise Project Management System - Local Launcher
color 0B
echo ==========================================================
echo Starting Enterprise Project Management System on Port 8002...
echo ==========================================================
timeout /t 2 /nobreak >nul
start http://127.0.0.1:8002
php artisan serve --port=8002
