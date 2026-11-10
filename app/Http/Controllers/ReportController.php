<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $projects = Project::with(['client', 'tasks', 'timeLogs'])->get();
        $totalBudget = $projects->sum('budget');
        $totalProjects = $projects->count();
        $completedProjects = $projects->where('status', 'completed')->count();
        $activeProjects = $projects->whereIn('status', ['in_progress', 'planning'])->count();

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $totalLoggedHours = TimeLog::sum('hours');

        // Member Workload Distribution
        $members = User::where('is_active', true)
            ->where('role', '!=', 'client')
            ->withCount([
                'tasks as total_tasks',
                'tasks as completed_tasks' => fn($q) => $q->where('status', 'done'),
                'tasks as pending_tasks' => fn($q) => $q->where('status', '!=', 'done'),
            ])
            ->withSum('timeLogs as total_hours', 'hours')
            ->get();

        return view('reports.index', compact(
            'projects',
            'totalBudget',
            'totalProjects',
            'completedProjects',
            'activeProjects',
            'totalTasks',
            'completedTasks',
            'totalLoggedHours',
            'members'
        ));
    }
}
