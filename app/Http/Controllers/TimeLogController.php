<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeLog::with(['project', 'task', 'user']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->where('log_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('log_date', '<=', $request->to_date);
        }

        $totalHours = (clone $query)->sum('hours');
        $timeLogs = $query->latest('log_date')->paginate(15)->withQueryString();

        $projects = Project::orderBy('title')->get();
        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();

        return view('timelogs.index', compact('timeLogs', 'totalHours', 'projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'user_id' => 'nullable|exists:users,id',
            'hours' => 'required|numeric|min:0.25|max:24',
            'log_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = $validated['user_id'] ?? auth()->id();

        $timeLog = TimeLog::create([
            'project_id' => $validated['project_id'],
            'task_id' => $validated['task_id'] ?? null,
            'user_id' => $userId,
            'hours' => $validated['hours'],
            'log_date' => $validated['log_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($timeLog->task) {
            $timeLog->task->increment('logged_hours', $timeLog->hours);
        }

        ActivityLog::log('Time Logged', auth()->user()->name . " logged {$timeLog->hours}h on project", $timeLog->project_id);

        return back()->with('success', 'Time logged successfully!');
    }

    public function destroy(TimeLog $timelog)
    {
        if ($timelog->task) {
            $timelog->task->decrement('logged_hours', min($timelog->hours, $timelog->task->logged_hours));
        }

        $timelog->delete();

        return back()->with('success', 'Time log entry removed.');
    }
}
