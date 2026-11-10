@extends('layouts.app')

@section('title', 'Projects Portfolio')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-briefcase-alt-2 me-2 text-primary"></i>Enterprise Projects Portfolio</h4>
        <p class="text-muted mb-0">Manage and track company deliverables, budgets, and milestones.</p>
    </div>
    @if(auth()->user()->role !== 'client')
    <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="bx bx-plus-circle me-1"></i> Create Project
    </a>
    @endif
</div>

<!-- Filters Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('projects.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-12">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search project name, code (PRJ-...), category..." value="{{ request('search') }}" />
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
            <div class="col-md-2 col-sm-6">
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Projects Grid -->
<div class="row g-4">
    @forelse($projects as $project)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm stat-card-glow">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-label-primary font-monospace">{{ $project->code }}</span>
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
                    <div class="d-flex gap-1">
                        <span class="badge bg-label-{{ $priorityColor }} text-capitalize">{{ $project->priority }}</span>
                        <span class="badge bg-label-{{ $statusColor }} text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                    </div>
                </div>

                <h5 class="card-title mb-1">
                    <a href="{{ route('projects.show', $project) }}" class="text-dark fw-bold text-decoration-none">
                        {{ $project->title }}
                    </a>
                </h5>

                <div class="text-muted small mb-3">
                    <i class="bx bx-buildings me-1"></i>
                    {{ $project->client ? $project->client->company_name : 'Internal Initiative' }} &bull; {{ $project->category }}
                </div>

                <p class="card-text text-secondary small flex-grow-1 mb-3" style="min-height: 40px;">
                    {{ Str::limit($project->description, 110) }}
                </p>

                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-muted">Progress</span>
                        <span class="small fw-bold text-dark">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress }}%" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Budget: <strong>${{ number_format($project->budget, 0) }}</strong></div>
                        <div class="text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $project->due_date ? $project->due_date->format('M d, Y') : 'Open Ended' }}
                        </div>
                    </div>

                    <!-- Team Avatars Stack -->
                    <div class="d-flex align-items-center avatar-group">
                        @foreach($project->members->take(3) as $member)
                        <div class="avatar avatar-xs pull-up" title="{{ $member->name }} ({{ $member->pivot->project_role ?? 'Member' }})">
                            <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">
                                {{ $member->initials }}
                            </span>
                        </div>
                        @endforeach
                        @if($project->members->count() > 3)
                        <div class="avatar avatar-xs">
                            <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size: 0.65rem;">
                                +{{ $project->members->count() - 3 }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3 pt-2 border-top">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="bx bx-detail me-1"></i> Overview
                    </a>
                    <a href="{{ route('tasks.kanban', ['project_id' => $project->id]) }}" class="btn btn-sm btn-label-secondary" title="Kanban Board">
                        <i class="bx bx-grid-alt"></i>
                    </a>
                    @if(auth()->user()->role !== 'client')
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-label-info" title="Edit">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card p-5 text-center border-0 shadow-sm">
            <i class="bx bx-folder-open fs-1 text-muted mb-2"></i>
            <h5>No projects match the criteria.</h5>
            <p class="text-muted">Try clearing the filters or create a new project.</p>
            @if(auth()->user()->role !== 'client')
            <div class="mt-2">
                <a href="{{ route('projects.create') }}" class="btn btn-primary">Create New Project</a>
            </div>
            @endif
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
