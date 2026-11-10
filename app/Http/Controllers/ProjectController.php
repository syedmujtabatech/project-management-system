<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['client', 'manager', 'members', 'tasks']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query->latest()->paginate(9)->withQueryString();
        $clients = Client::where('status', 'active')->get();

        return view('projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $managers = User::whereIn('role', ['super_admin', 'project_manager'])->get();
        $teamMembers = User::where('is_active', true)->where('role', '!=', 'client')->get();

        // Suggested next project code
        $nextId = Project::max('id') + 1;
        $suggestedCode = 'PRJ-' . date('Y') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('projects.create', compact('clients', 'managers', 'teamMembers', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code',
            'client_id' => 'nullable|exists:clients,id',
            'manager_id' => 'nullable|exists:users,id',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'client_id' => $validated['client_id'] ?? null,
            'manager_id' => $validated['manager_id'] ?? auth()->id(),
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'currency' => $validated['currency'] ?? 'USD',
            'description' => $validated['description'] ?? null,
            'progress' => 0,
        ]);

        if (!empty($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        ActivityLog::log('Created Project', "Created project [{$project->code}] {$project->title}", $project->id);

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully!');
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'manager',
            'members',
            'milestones.tasks',
            'tasks.assignee',
            'timeLogs.user',
            'timeLogs.task',
        ]);

        $totalEstimated = $project->tasks->sum('estimated_hours');
        $totalLogged = $project->timeLogs->sum('hours');
        $completedTasks = $project->tasks->where('status', 'done')->count();
        $totalTasks = $project->tasks->count();

        $allUsers = User::where('is_active', true)->where('role', '!=', 'client')->get();

        return view('projects.show', compact('project', 'totalEstimated', 'totalLogged', 'completedTasks', 'totalTasks', 'allUsers'));
    }

    public function edit(Project $project)
    {
        $clients = Client::where('status', 'active')->orderBy('company_name')->get();
        $managers = User::whereIn('role', ['super_admin', 'project_manager'])->get();
        $teamMembers = User::where('is_active', true)->where('role', '!=', 'client')->get();
        $selectedMembers = $project->members->pluck('id')->toArray();

        return view('projects.edit', compact('project', 'clients', 'managers', 'teamMembers', 'selectedMembers'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code,' . $project->id,
            'client_id' => 'nullable|exists:clients,id',
            'manager_id' => 'nullable|exists:users,id',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update([
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'client_id' => $validated['client_id'] ?? null,
            'manager_id' => $validated['manager_id'] ?? null,
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'currency' => $validated['currency'] ?? 'USD',
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['members'])) {
            $project->members()->sync($validated['members']);
        }

        $project->recalculateProgress();

        ActivityLog::log('Updated Project', "Updated project [{$project->code}] {$project->title}", $project->id);

        return redirect()->route('projects.show', $project)->with('success', 'Project details updated successfully!');
    }

    public function destroy(Project $project)
    {
        $title = $project->title;
        $code = $project->code;
        $project->delete();

        ActivityLog::log('Deleted Project', "Deleted project [{$code}] {$title}");

        return redirect()->route('projects.index')->with('success', "Project {$code} deleted successfully!");
    }
}
