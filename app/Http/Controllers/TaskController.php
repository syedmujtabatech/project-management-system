<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\User;
use App\Models\TaskComment;
use App\Models\TimeLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignee', 'milestone']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('task_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(12)->withQueryString();
        $projects = Project::orderBy('title')->get();
        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();

        return view('tasks.index', compact('tasks', 'projects', 'users'));
    }

    public function kanban(Request $request)
    {
        $query = Task::with(['project', 'assignee', 'comments']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $allTasks = $query->orderBy('order_column')->orderBy('updated_at', 'desc')->get();

        $columns = [
            'backlog' => [
                'title' => 'Backlog',
                'color' => 'secondary',
                'icon' => 'bx-layer',
                'tasks' => $allTasks->where('status', 'backlog'),
            ],
            'todo' => [
                'title' => 'To Do',
                'color' => 'info',
                'icon' => 'bx-list-check',
                'tasks' => $allTasks->where('status', 'todo'),
            ],
            'in_progress' => [
                'title' => 'In Progress',
                'color' => 'primary',
                'icon' => 'bx-time-five',
                'tasks' => $allTasks->where('status', 'in_progress'),
            ],
            'in_review' => [
                'title' => 'In Review',
                'color' => 'warning',
                'icon' => 'bx-search-alt',
                'tasks' => $allTasks->where('status', 'in_review'),
            ],
            'done' => [
                'title' => 'Done',
                'color' => 'success',
                'icon' => 'bx-check-circle',
                'tasks' => $allTasks->where('status', 'done'),
            ],
        ];

        $projects = Project::orderBy('title')->get();
        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();

        return view('tasks.kanban', compact('columns', 'projects', 'users'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:backlog,todo,in_progress,in_review,done',
            'order' => 'nullable|integer',
        ]);

        $oldStatus = $task->status;
        $task->status = $validated['status'];

        if (isset($validated['order'])) {
            $task->order_column = $validated['order'];
        }

        if ($task->status === 'done' && $oldStatus !== 'done') {
            $task->completed_at = now();
        } elseif ($task->status !== 'done') {
            $task->completed_at = null;
        }

        $task->save();

        if ($task->project) {
            $task->project->recalculateProgress();
        }

        ActivityLog::log(
            'Task Status Changed',
            "Task [{$task->task_number}] moved from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $task->status)) . "'",
            $task->project_id
        );

        return response()->json([
            'success' => true,
            'message' => "Task {$task->task_number} moved to " . ucfirst(str_replace('_', ' ', $task->status)),
            'project_progress' => $task->project ? $task->project->progress : null,
        ]);
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('title')->get();
        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();
        $selectedProject = $request->filled('project_id') ? Project::find($request->project_id) : null;
        $milestones = $selectedProject ? $selectedProject->milestones : Milestone::all();

        // Suggested task number
        $nextId = Task::max('id') + 1;
        $suggestedTaskNumber = 'TSK-' . (100 + $nextId);

        return view('tasks.create', compact('projects', 'users', 'selectedProject', 'milestones', 'suggestedTaskNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'task_number' => 'nullable|string|max:50',
            'project_id' => 'required|exists:projects,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,in_review,done',
            'estimated_hours' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $taskNumber = $validated['task_number'] ?: ('TSK-' . (100 + Task::max('id') + 1));

        $task = Task::create([
            'task_number' => strtoupper($taskNumber),
            'project_id' => $validated['project_id'],
            'milestone_id' => $validated['milestone_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'estimated_hours' => $validated['estimated_hours'] ?? 0,
            'logged_hours' => 0,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'completed_at' => $validated['status'] === 'done' ? now() : null,
            'order_column' => 0,
        ]);

        $task->project->recalculateProgress();

        ActivityLog::log('Created Task', "Created task [{$task->task_number}] {$task->title}", $task->project_id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $task->load([
            'project.client',
            'milestone',
            'assignee',
            'creator',
            'comments.user',
            'timeLogs.user',
        ]);

        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();

        return view('tasks.show', compact('task', 'users'));
    }

    public function edit(Task $task)
    {
        $projects = Project::orderBy('title')->get();
        $users = User::where('is_active', true)->where('role', '!=', 'client')->get();
        $milestones = $task->project ? $task->project->milestones : [];

        return view('tasks.edit', compact('task', 'projects', 'users', 'milestones'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,in_review,done',
            'estimated_hours' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $oldStatus = $task->status;

        $task->update([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'],
            'milestone_id' => $validated['milestone_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'estimated_hours' => $validated['estimated_hours'] ?? 0,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'description' => $validated['description'] ?? null,
            'completed_at' => ($validated['status'] === 'done') ? ($task->completed_at ?? now()) : null,
        ]);

        $task->project->recalculateProgress();

        ActivityLog::log('Updated Task', "Updated task [{$task->task_number}] {$task->title}", $task->project_id);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        $taskNumber = $task->task_number;
        $title = $task->title;
        $project = $task->project;

        $task->delete();

        if ($project) {
            $project->recalculateProgress();
        }

        ActivityLog::log('Deleted Task', "Deleted task [{$taskNumber}] {$title}", $project ? $project->id : null);

        return redirect()->route('tasks.index')->with('success', "Task {$taskNumber} deleted successfully!");
    }

    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        ActivityLog::log('Task Comment Added', auth()->user()->name . " commented on task [{$task->task_number}]", $task->project_id);

        return back()->with('success', 'Comment posted successfully!');
    }
}
