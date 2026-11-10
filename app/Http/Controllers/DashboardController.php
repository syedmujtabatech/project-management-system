<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\TimeLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Query scopes for Client / Team Member if needed
        $projectsQuery = Project::query();
        $tasksQuery = Task::query();

        if ($user->role === 'client') {
            $client = Client::where('email', $user->email)->first();
            if ($client) {
                $projectsQuery->where('client_id', $client->id);
                $tasksQuery->whereHas('project', fn($q) => $q->where('client_id', $client->id));
            }
        } elseif ($user->role === 'team_member') {
            // Team member sees projects they are member of or assigned tasks
            $projectsQuery->whereHas('members', fn($q) => $q->where('users.id', $user->id));
        }

        // Metrics
        $totalProjects = (clone $projectsQuery)->count();
        $activeProjects = (clone $projectsQuery)->whereIn('status', ['in_progress', 'planning'])->count();
        $completedProjects = (clone $projectsQuery)->where('status', 'completed')->count();
        
        $totalTasks = (clone $tasksQuery)->count();
        $todoTasks = (clone $tasksQuery)->whereIn('status', ['backlog', 'todo'])->count();
        $inProgressTasks = (clone $tasksQuery)->whereIn('status', ['in_progress', 'in_review'])->count();
        $completedTasks = (clone $tasksQuery)->where('status', 'done')->count();
        $overdueTasks = (clone $tasksQuery)->where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today())
            ->count();

        $totalClients = Client::where('status', 'active')->count();
        $totalTeamMembers = User::where('is_active', true)->where('role', '!=', 'client')->count();
        $totalBudget = (clone $projectsQuery)->sum('budget');
        $totalHoursLogged = TimeLog::sum('hours');

        // Recent Projects
        $recentProjects = (clone $projectsQuery)
            ->with(['client', 'manager', 'members'])
            ->latest()
            ->take(5)
            ->get();

        // Urgent & Upcoming Tasks
        $upcomingTasks = (clone $tasksQuery)
            ->with(['project', 'assignee'])
            ->where('status', '!=', 'done')
            ->orderBy('due_date', 'asc')
            ->take(6)
            ->get();

        // Recent Activity
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        // Chart Data: Task Status Breakdown
        $taskStatusCounts = [
            'Backlog' => (clone $tasksQuery)->where('status', 'backlog')->count(),
            'To Do' => (clone $tasksQuery)->where('status', 'todo')->count(),
            'In Progress' => (clone $tasksQuery)->where('status', 'in_progress')->count(),
            'In Review' => (clone $tasksQuery)->where('status', 'in_review')->count(),
            'Done' => (clone $tasksQuery)->where('status', 'done')->count(),
        ];

        // Chart Data: Projects by Category
        $projectsByCategory = Project::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return view('dashboard.index', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalTasks',
            'todoTasks',
            'inProgressTasks',
            'completedTasks',
            'overdueTasks',
            'totalClients',
            'totalTeamMembers',
            'totalBudget',
            'totalHoursLogged',
            'recentProjects',
            'upcomingTasks',
            'recentActivities',
            'taskStatusCounts',
            'projectsByCategory'
        ));
    }
}
