@extends('layouts.app')

@section('title', 'Engineering Team Specialists')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-group me-2 text-primary"></i>Engineering Team & Specialists</h4>
        <p class="text-muted mb-0">Manage roles, departments, technical capacity, and assignments.</p>
    </div>
    @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'project_manager')
    <a href="{{ route('team.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="bx bx-user-plus me-1"></i> Add Specialist
    </a>
    @endif
</div>

<!-- Search & Role Filter -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('team.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6 col-sm-12">
                <input type="text" name="search" class="form-control" placeholder="Search name, email, job title, department..." value="{{ request('search') }}" />
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="project_manager" {{ request('role') === 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                    <option value="team_member" {{ request('role') === 'team_member' ? 'selected' : '' }}>Team Member</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('team.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Team Members Grid -->
<div class="row g-4">
    @forelse($members as $m)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm stat-card-glow text-center p-4">
            <div class="avatar avatar-xl mx-auto mb-3">
                <span class="avatar-initial rounded-circle bg-primary text-white fs-2 fw-bold">
                    {{ $m->initials }}
                </span>
            </div>

            <h5 class="mb-1 fw-bold text-dark">{{ $m->name }}</h5>
            <div class="text-muted small mb-2">{{ $m->job_title ?: 'Engineer' }}</div>

            <div class="d-flex justify-content-center gap-1 mb-3">
                @php
                    $roleBadge = match($m->role) {
                        'super_admin' => 'danger',
                        'project_manager' => 'primary',
                        default => 'info'
                    };
                @endphp
                <span class="badge bg-label-{{ $roleBadge }} text-capitalize">{{ str_replace('_', ' ', $m->role) }}</span>
                <span class="badge bg-label-secondary">{{ $m->department ?: 'General' }}</span>
                @if(!$m->is_active)
                    <span class="badge bg-label-warning">Inactive</span>
                @endif
            </div>

            <div class="small text-muted mb-3">
                <div><i class="bx bx-envelope me-1"></i> {{ $m->email }}</div>
                <div><i class="bx bx-phone me-1"></i> {{ $m->phone ?: 'No direct line' }}</div>
            </div>

            <!-- Stats -->
            <div class="row g-2 py-2 border-top border-bottom bg-light rounded mb-3">
                <div class="col-6">
                    <span class="text-muted small d-block">Assigned Tasks</span>
                    <span class="fw-bold fs-6 text-dark">{{ $m->assigned_tasks_count }}</span>
                </div>
                <div class="col-6 border-start">
                    <span class="text-muted small d-block">Completed Tasks</span>
                    <span class="fw-bold fs-6 text-success">{{ $m->completed_tasks_count }}</span>
                </div>
            </div>

            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'project_manager')
            <div class="d-flex justify-content-center gap-2 mt-auto">
                <a href="{{ route('team.edit', $m) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                    <i class="bx bx-edit me-1"></i> Edit Profile
                </a>
                @if(auth()->id() !== $m->id)
                <form action="{{ route('team.toggle', $m) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-{{ $m->is_active ? 'warning' : 'success' }}" title="{{ $m->is_active ? 'Deactivate account' : 'Activate account' }}">
                        <i class="bx bx-power-off"></i>
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">No specialists found matching your search.</div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $members->links() }}
</div>
@endsection
