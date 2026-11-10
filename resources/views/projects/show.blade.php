@extends('layouts.app')

@section('title', $project->title . ' (' . $project->code . ')')

@section('content')
<!-- Project Header Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-label-primary font-monospace fs-6 px-2 py-1">{{ $project->code }}</span>
                    @php
                        $priorityColor = match($project->priority) {
                            'urgent' => 'danger',
                            'high' => 'warning',
                            'medium' => 'info',
                            default => 'secondary'
                        };
                        $statusColor = match($project->status) {
                            'completed' => 'success',
                            'in_progress' => 'primary',
                            'planning' => 'info',
                            'on_hold' => 'warning',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-label-{{ $priorityColor }} text-capitalize">{{ $project->priority }} Priority</span>
                    <span class="badge bg-label-{{ $statusColor }} text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                    <span class="badge bg-label-dark">{{ $project->category }}</span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $project->title }}</h3>
                <div class="text-muted small">
                    <span class="me-3"><i class="bx bx-buildings me-1"></i> Client: <strong>{{ $project->client ? $project->client->company_name : 'Internal Initiative' }}</strong></span>
                    <span class="me-3"><i class="bx bx-user me-1"></i> Manager: <strong>{{ $project->manager ? $project->manager->name : 'Unassigned' }}</strong></span>
                    <span><i class="bx bx-calendar me-1"></i> Timeline: {{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }} &rarr; {{ $project->due_date ? $project->due_date->format('M d, Y') : 'Open Ended' }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('tasks.kanban', ['project_id' => $project->id]) }}" class="btn btn-primary d-flex align-items-center">
                    <i class="bx bx-grid-alt me-1"></i> Kanban Board
                </a>
                @if(auth()->user()->role !== 'client')
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bx bx-plus me-1"></i> Add Task
                </button>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="bx bx-flag me-1"></i> Add Milestone
                </button>
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#logTimeModal">
                    <i class="bx bx-time me-1"></i> Log Hours
                </button>
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-label-secondary">
                    <i class="bx bx-edit"></i>
                </a>
                @endif
            </div>
        </div>

        <!-- Progress and Mini KPIs -->
        <div class="row g-3 mt-3 pt-3 border-top align-items-center">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small fw-bold text-muted">Completion Velocity</span>
                    <span class="small fw-bold text-primary">{{ $project->progress }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress }}%" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">{{ $completedTasks }} of {{ $totalTasks }} tasks completed</small>
            </div>
            <div class="col-md-2 col-6 text-center border-start">
                <span class="text-muted small d-block">Budget</span>
                <span class="fw-bold text-dark fs-5">${{ number_format($project->budget, 0) }}</span>
            </div>
            <div class="col-md-2 col-6 text-center border-start">
                <span class="text-muted small d-block">Hours Logged</span>
                <span class="fw-bold text-info fs-5">{{ number_format($totalLogged, 1) }}h</span>
            </div>
            <div class="col-md-2 col-6 text-center border-start">
                <span class="text-muted small d-block">Est. Hours</span>
                <span class="fw-bold text-secondary fs-5">{{ number_format($totalEstimated, 1) }}h</span>
            </div>
            <div class="col-md-2 col-6 text-center border-start">
                <span class="text-muted small d-block">Team Members</span>
                <span class="fw-bold text-primary fs-5">{{ $project->members->count() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="nav-align-top mb-4">
    <ul class="nav nav-tabs nav-fill" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-overview">
                <i class="bx bx-info-circle me-1"></i> Overview & Specs
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-tasks">
                <i class="bx bx-task me-1"></i> Tasks <span class="badge rounded-pill bg-label-primary ms-1">{{ $totalTasks }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-milestones">
                <i class="bx bx-flag me-1"></i> Milestones <span class="badge rounded-pill bg-label-info ms-1">{{ $project->milestones->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-team">
                <i class="bx bx-group me-1"></i> Team Specialists <span class="badge rounded-pill bg-label-secondary ms-1">{{ $project->members->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-timelogs">
                <i class="bx bx-time-five me-1"></i> Timesheets
            </button>
        </li>
    </ul>

    <div class="tab-content border-0 shadow-sm p-4 bg-white rounded-bottom">
        <!-- 1. Overview Tab -->
        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3">Project Description & Scope of Work</h5>
                    <div class="p-3 bg-light rounded text-secondary mb-4" style="line-height: 1.7;">
                        {{ $project->description ?: 'No detailed scope written for this project yet.' }}
                    </div>

                    <h5 class="fw-bold mb-3">Milestone Progress Roadmap</h5>
                    <div class="timeline-roadmap">
                        @forelse($project->milestones as $m)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="avatar avatar-sm me-3">
                                @if($m->status === 'achieved')
                                    <span class="avatar-initial rounded-circle bg-success text-white"><i class="bx bx-check"></i></span>
                                @elseif($m->status === 'in_progress')
                                    <span class="avatar-initial rounded-circle bg-primary text-white"><i class="bx bx-loader"></i></span>
                                @else
                                    <span class="avatar-initial rounded-circle bg-secondary text-white"><i class="bx bx-time"></i></span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1 fw-bold">{{ $m->title }}</h6>
                                    <span class="badge bg-label-{{ $m->status === 'achieved' ? 'success' : ($m->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $m->status)) }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-1">{{ $m->description }}</p>
                                <small class="text-secondary"><i class="bx bx-calendar me-1"></i>Target: {{ $m->due_date ? $m->due_date->format('M d, Y') : 'No date' }} &bull; {{ $m->tasks->count() }} Tasks Linked</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No milestones established yet. Click "Add Milestone" to plan sprints.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card bg-light border-0 p-3 mb-3">
                        <h6 class="fw-bold mb-3"><i class="bx bx-info-circle me-1"></i>Project Metadata</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Initiation Date:</span>
                                <span class="fw-semibold">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'N/A' }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Target Deadline:</span>
                                <span class="fw-semibold text-danger">{{ $project->due_date ? $project->due_date->format('M d, Y') : 'N/A' }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Financial Allocation:</span>
                                <span class="fw-semibold">${{ number_format($project->budget, 2) }} {{ $project->currency }}</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Estimated Hours:</span>
                                <span class="fw-semibold">{{ $totalEstimated }} hrs</span>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Logged Effort:</span>
                                <span class="fw-semibold text-info">{{ $totalLogged }} hrs</span>
                            </li>
                        </ul>
                    </div>

                    @if($project->client)
                    <div class="card bg-light border-0 p-3">
                        <h6 class="fw-bold mb-2"><i class="bx bx-buildings me-1"></i>Client Overview</h6>
                        <div class="fw-bold">{{ $project->client->company_name }}</div>
                        <div class="small text-muted mb-2">{{ $project->client->contact_person }}</div>
                        <div class="small text-secondary mb-1"><i class="bx bx-envelope me-1"></i>{{ $project->client->email }}</div>
                        <div class="small text-secondary mb-2"><i class="bx bx-phone me-1"></i>{{ $project->client->phone ?: 'N/A' }}</div>
                        <a href="{{ route('clients.show', $project->client) }}" class="btn btn-sm btn-outline-primary w-100">Client CRM Record</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Tasks Tab -->
        <div class="tab-pane fade" id="tab-tasks" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Project Deliverables & Work Items</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('tasks.kanban', ['project_id' => $project->id]) }}" class="btn btn-sm btn-label-primary">
                        <i class="bx bx-grid-alt me-1"></i> Kanban Mode
                    </a>
                    @if(auth()->user()->role !== 'client')
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bx bx-plus me-1"></i> New Task
                    </button>
                    @endif
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Task Title</th>
                            <th>Milestone</th>
                            <th>Assignee</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->tasks as $t)
                        <tr>
                            <td><span class="badge bg-label-secondary font-monospace">{{ $t->task_number }}</span></td>
                            <td>
                                <a href="{{ route('tasks.show', $t) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $t->title }}
                                </a>
                            </td>
                            <td><span class="small text-muted">{{ $t->milestone ? $t->milestone->title : '-' }}</span></td>
                            <td>
                                @if($t->assignee)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">
                                                {{ $t->assignee->initials }}
                                            </span>
                                        </div>
                                        <span class="small">{{ $t->assignee->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pCol = match($t->priority) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $pCol }} text-capitalize">{{ $t->priority }}</span>
                            </td>
                            <td>
                                @php
                                    $stCol = match($t->status) {
                                        'done' => 'success',
                                        'in_progress' => 'primary',
                                        'in_review' => 'warning',
                                        'todo' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $stCol }} text-capitalize">{{ str_replace('_', ' ', $t->status) }}</span>
                            </td>
                            <td>
                                <span class="small {{ ($t->due_date && $t->due_date->isPast() && $t->status !== 'done') ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $t->due_date ? $t->due_date->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tasks.show', $t) }}" class="btn btn-xs btn-label-primary"><i class="bx bx-show"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No tasks defined for this project yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Milestones Tab -->
        <div class="tab-pane fade" id="tab-milestones" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Project Sprints & Key Milestones</h5>
                @if(auth()->user()->role !== 'client')
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMilestoneModal">
                    <i class="bx bx-plus me-1"></i> Add Milestone
                </button>
                @endif
            </div>

            <div class="row g-3">
                @forelse($project->milestones as $m)
                <div class="col-md-6">
                    <div class="card border shadow-none p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-dark">{{ $m->title }}</h6>
                            <span class="badge bg-label-{{ $m->status === 'achieved' ? 'success' : ($m->status === 'in_progress' ? 'primary' : 'secondary') }} text-capitalize">
                                {{ str_replace('_', ' ', $m->status) }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-3 flex-grow-1">{{ $m->description }}</p>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-muted"><i class="bx bx-calendar me-1"></i>Target: {{ $m->due_date ? $m->due_date->format('M d, Y') : 'N/A' }}</span>
                            @if(auth()->user()->role !== 'client')
                            <div class="d-flex gap-1">
                                <form action="{{ route('milestones.toggle', $m) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-{{ $m->status === 'achieved' ? 'secondary' : 'success' }}">
                                        {{ $m->status === 'achieved' ? 'Reopen' : 'Mark Achieved' }}
                                    </button>
                                </form>
                                <form action="{{ route('milestones.destroy', $m) }}" method="POST" onsubmit="return confirm('Delete this milestone?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4 text-muted">No milestones planned yet.</div>
                @endforelse
            </div>
        </div>

        <!-- 4. Team Specialists Tab -->
        <div class="tab-pane fade" id="tab-team" role="tabpanel">
            <h5 class="fw-bold mb-3">Project Resource Allocation</h5>
            <div class="row g-3">
                @forelse($project->members as $mem)
                <div class="col-md-4 col-sm-6">
                    <div class="card border shadow-none p-3 text-center">
                        <div class="avatar avatar-md mx-auto mb-2">
                            <span class="avatar-initial rounded-circle bg-primary text-white fs-5">{{ $mem->initials }}</span>
                        </div>
                        <h6 class="mb-0 fw-bold">{{ $mem->name }}</h6>
                        <span class="badge bg-label-info my-1">{{ $mem->pivot->project_role ?? $mem->job_title }}</span>
                        <small class="text-muted d-block">{{ $mem->department ?? 'Engineering' }}</small>
                        <small class="text-secondary"><i class="bx bx-envelope me-1"></i>{{ $mem->email }}</small>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4 text-muted">No team members assigned to this project yet.</div>
                @endforelse
            </div>
        </div>

        <!-- 5. Timesheets Tab -->
        <div class="tab-pane fade" id="tab-timelogs" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Project Engineering Timesheets</h5>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#logTimeModal">
                    <i class="bx bx-time me-1"></i> Log Effort
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Engineer</th>
                            <th>Associated Task</th>
                            <th>Hours</th>
                            <th>Work Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->timeLogs as $log)
                        <tr>
                            <td>{{ $log->log_date ? $log->log_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $log->user ? $log->user->name : 'N/A' }}</div>
                            </td>
                            <td>
                                @if($log->task)
                                    <span class="badge bg-label-secondary font-monospace">{{ $log->task->task_number }}</span>
                                    {{ Str::limit($log->task->title, 35) }}
                                @else
                                    <span class="text-muted">General Project Work</span>
                                @endif
                            </td>
                            <td><span class="badge bg-label-primary fs-6">{{ $log->hours }}h</span></td>
                            <td class="small text-secondary">{{ $log->notes ?: 'General engineering & research' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No timesheets submitted for this project yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Add Task -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('tasks.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-task me-2 text-primary"></i>Create Task in {{ $project->code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="task_title">Task Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="task_title" name="title" placeholder="e.g. Implement Redis caching layer" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="task_milestone">Milestone Sprint</label>
                    <select class="form-select" id="task_milestone" name="milestone_id">
                        <option value="">-- No Milestone / Ad-hoc --</option>
                        @foreach($project->milestones as $m)
                            <option value="{{ $m->id }}">{{ $m->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="task_assigned_to">Assignee</label>
                    <select class="form-select" id="task_assigned_to" name="assigned_to">
                        <option value="">-- Unassigned --</option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->job_title }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="task_priority">Priority</label>
                    <select class="form-select" id="task_priority" name="priority">
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="task_status">Status</label>
                    <select class="form-select" id="task_status" name="status">
                        <option value="backlog">Backlog</option>
                        <option value="todo" selected>To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="in_review">In Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="task_estimated_hours">Est. Hours</label>
                    <input type="number" step="0.5" class="form-control" id="task_estimated_hours" name="estimated_hours" value="8" />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="task_start_date">Start Date</label>
                    <input type="date" class="form-control" id="task_start_date" name="start_date" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="task_due_date">Due Date</label>
                    <input type="date" class="form-control" id="task_due_date" name="due_date" />
                </div>
                <div class="col-12">
                    <label class="form-label" for="task_description">Task Requirements / Acceptance Criteria</label>
                    <textarea class="form-control" id="task_description" name="description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Add Milestone -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('milestones.store', $project) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-flag me-2 text-info"></i>Add Project Milestone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="m_title">Milestone Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="m_title" name="title" placeholder="e.g. Sprint 3: Core API Integration" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="m_due_date">Target Date</label>
                    <input type="date" class="form-control" id="m_due_date" name="due_date" />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="m_status">Status</label>
                    <select class="form-select" id="m_status" name="status">
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="achieved">Achieved</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="m_desc">Description / Objectives</label>
                    <textarea class="form-control" id="m_desc" name="description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>Add Milestone</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Log Time -->
<div class="modal fade" id="logTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('timelogs.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-time me-2 text-success"></i>Log Engineering Hours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="log_task">Associated Task (Optional)</label>
                    <select class="form-select" id="log_task" name="task_id">
                        <option value="">-- General Project Work --</option>
                        @foreach($project->tasks as $t)
                            <option value="{{ $t->id }}">[{{ $t->task_number }}] {{ $t->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="log_hours">Hours Worked <span class="text-danger">*</span></label>
                    <input type="number" step="0.25" min="0.25" max="24" class="form-control" id="log_hours" name="hours" value="4.0" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="log_date">Log Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="log_date" name="log_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-12">
                    <label class="form-label" for="log_notes">Work Log Notes</label>
                    <textarea class="form-control" id="log_notes" name="notes" rows="3" placeholder="Summary of what was achieved..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="bx bx-save me-1"></i>Submit Timesheet</button>
            </div>
        </form>
    </div>
</div>
@endsection
