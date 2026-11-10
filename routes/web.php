<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

// Public & Authentication
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Projects
    Route::resource('projects', ProjectController::class);

    // Milestones
    Route::post('/projects/{project}/milestones', [MilestoneController::class, 'store'])->name('milestones.store');
    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])->name('milestones.update');
    Route::post('/milestones/{milestone}/toggle', [MilestoneController::class, 'toggleStatus'])->name('milestones.toggle');
    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->name('milestones.destroy');

    // Tasks (Kanban route before resource to avoid wildcard clash)
    Route::get('/tasks/kanban', [TaskController::class, 'kanban'])->name('tasks.kanban');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    Route::resource('tasks', TaskController::class);

    // Clients
    Route::resource('clients', ClientController::class);

    // Timesheets / Time Logs
    Route::get('/timelogs', [TimeLogController::class, 'index'])->name('timelogs.index');
    Route::post('/timelogs', [TimeLogController::class, 'store'])->name('timelogs.store');
    Route::delete('/timelogs/{timelog}', [TimeLogController::class, 'destroy'])->name('timelogs.destroy');

    // Team
    Route::resource('team', TeamController::class)->parameters(['team' => 'user']);
    Route::post('/team/{user}/toggle', [TeamController::class, 'toggleActive'])->name('team.toggle');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Enterprise Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
