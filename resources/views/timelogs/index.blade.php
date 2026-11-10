@extends('layouts.app')

@section('title', 'Engineering Timesheets & Time Tracking')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-time-five me-2 text-primary"></i>Engineering Timesheets</h4>
        <p class="text-muted mb-0">Record and monitor development hours across client projects and tasks.</p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#newTimeLogModal">
        <i class="bx bx-plus-circle me-1"></i> Log Engineering Hours
    </button>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary fs-4"><i class="bx bx-time"></i></span>
                </div>
                <div>
                    <span class="text-muted small">Total Logged Hours (Filtered)</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalHours, 1) }}h</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-info fs-4"><i class="bx bx-user-check"></i></span>
                </div>
                <div>
                    <span class="text-muted small">Active Contributors</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ $users->count() }} Specialists</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-success fs-4"><i class="bx bx-briefcase"></i></span>
                </div>
                <div>
                    <span class="text-muted small">Monitored Projects</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ $projects->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('timelogs.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3 col-sm-6">
                <select name="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>[{{ $p->code }}] {{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="user_id" class="form-select">
                    <option value="">All Specialists</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From date" />
            </div>
            <div class="col-md-2 col-sm-6">
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To date" />
            </div>
            <div class="col-md-2 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('timelogs.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Timesheets Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Specialist</th>
                    <th>Project</th>
                    <th>Task Code</th>
                    <th>Hours Logged</th>
                    <th>Work Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timeLogs as $log)
                <tr>
                    <td>{{ $log->log_date ? $log->log_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">
                                    {{ $log->user ? $log->user->initials : 'U' }}
                                </span>
                            </div>
                            <span class="fw-semibold text-dark">{{ $log->user ? $log->user->name : 'N/A' }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('projects.show', $log->project) }}" class="fw-bold text-dark text-decoration-none">
                            [{{ $log->project ? $log->project->code : 'PRJ' }}] {{ $log->project ? Str::limit($log->project->title, 25) : 'N/A' }}
                        </a>
                    </td>
                    <td>
                        @if($log->task)
                            <a href="{{ route('tasks.show', $log->task) }}" class="badge bg-label-secondary font-monospace text-decoration-none">
                                {{ $log->task->task_number }}
                            </a>
                        @else
                            <span class="text-muted small">General Sprint Work</span>
                        @endif
                    </td>
                    <td><span class="badge bg-label-primary fs-6">{{ $log->hours }} hrs</span></td>
                    <td class="small text-secondary">{{ Str::limit($log->notes ?: 'General software engineering effort', 45) }}</td>
                    <td>
                        @if(auth()->user()->role === 'super_admin' || auth()->id() === $log->user_id)
                        <form action="{{ route('timelogs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this timesheet entry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-label-danger"><i class="bx bx-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No timesheets match your criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($timeLogs->hasPages())
    <div class="card-footer py-3 border-top">
        {{ $timeLogs->links() }}
    </div>
    @endif
</div>

<!-- Modal: Log Time -->
<div class="modal fade" id="newTimeLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('timelogs.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-time me-2 text-primary"></i>Submit Engineering Hours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="m_project_id">Select Project <span class="text-danger">*</span></label>
                    <select class="form-select" id="m_project_id" name="project_id" required>
                        <option value="">-- Choose Project --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">[{{ $p->code }}] {{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'project_manager')
                <div class="col-12">
                    <label class="form-label" for="m_user_id">Engineer Specialist</label>
                    <select class="form-select" id="m_user_id" name="user_id">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $u->id === auth()->id() ? 'selected' : '' }}>{{ $u->name }} ({{ $u->job_title }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label" for="m_hours">Hours Worked <span class="text-danger">*</span></label>
                    <input type="number" step="0.25" min="0.25" max="24" class="form-control" id="m_hours" name="hours" value="4.0" required />
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="m_log_date">Date of Work <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="m_log_date" name="log_date" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="col-12">
                    <label class="form-label" for="m_notes">Work Description</label>
                    <textarea class="form-control" id="m_notes" name="notes" rows="3" placeholder="Summary of what was achieved..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>Save Timesheet</button>
            </div>
        </form>
    </div>
</div>
@endsection
