@extends('layouts.app')

@section('title', 'Tasks Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-task me-2 text-primary"></i>Tasks & Deliverables Directory</h4>
        <p class="text-muted mb-0">Unified table of all sprint items across all enterprise projects.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tasks.kanban') }}" class="btn btn-outline-primary d-flex align-items-center">
            <i class="bx bx-grid-alt me-1"></i> Kanban Board
        </a>
        @if(auth()->user()->role !== 'client')
        <a href="{{ route('tasks.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bx bx-plus-circle me-1"></i> Create Task
        </a>
        @endif
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('tasks.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3 col-sm-12">
                <input type="text" name="search" class="form-control" placeholder="Search task name or code..." value="{{ request('search') }}" />
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>[{{ $p->code }}] {{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="backlog" {{ request('status') === 'backlog' ? 'selected' : '' }}>Backlog</option>
                    <option value="todo" {{ request('status') === 'todo' ? 'selected' : '' }}>To Do</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Tasks Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task Code</th>
                    <th>Title & Project</th>
                    <th>Assignee</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Estimated / Logged</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td><span class="badge bg-label-secondary font-monospace">{{ $task->task_number }}</span></td>
                    <td>
                        <a href="{{ route('tasks.show', $task) }}" class="fw-bold text-dark text-decoration-none d-block">
                            {{ $task->title }}
                        </a>
                        <small class="text-muted">
                            <i class="bx bx-folder me-1"></i>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-muted text-decoration-none">
                                {{ $task->project ? $task->project->title : 'N/A' }}
                            </a>
                        </small>
                    </td>
                    <td>
                        @if($task->assignee)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">
                                        {{ $task->assignee->initials }}
                                    </span>
                                </div>
                                <span class="small fw-semibold">{{ $task->assignee->name }}</span>
                            </div>
                        @else
                            <span class="badge bg-label-secondary small">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $pBadge = match($task->priority) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-label-{{ $pBadge }} text-capitalize">{{ $task->priority }}</span>
                    </td>
                    <td>
                        @php
                            $sBadge = match($task->status) {
                                'done' => 'success',
                                'in_progress' => 'primary',
                                'in_review' => 'warning',
                                'todo' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-label-{{ $sBadge }} text-capitalize">{{ str_replace('_', ' ', $task->status) }}</span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <strong>{{ $task->logged_hours }}h</strong> / {{ $task->estimated_hours }}h
                        </small>
                    </td>
                    <td>
                        <span class="small {{ ($task->due_date && $task->due_date->isPast() && $task->status !== 'done') ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-xs btn-label-primary" title="View details">
                                <i class="bx bx-show"></i>
                            </a>
                            @if(auth()->user()->role !== 'client')
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-xs btn-label-info" title="Edit">
                                <i class="bx bx-edit"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bx bx-task fs-1 d-block mb-2"></i>
                        No tasks matched your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tasks->hasPages())
    <div class="card-footer py-3 border-top">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection
