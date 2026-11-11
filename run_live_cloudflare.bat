@echo off
title Enterprise Project Management System - Live Cloudflare Launcher
color 0B
echo Starting Enterprise PMS Local Server on Port 8002...
start "" /b php artisan serve --port=8002
echo Starting Cloudflare Tunnel for Enterprise PMS...
"C:\Users\Mujtaba Arshad\.gemini\antigravity\cloudflared.exe" tunnel --url http://127.0.0.1:8002
pause
