@extends('layouts.app')

@section('title', 'Executive Portfolio Reports & Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-bar-chart-alt-2 me-2 text-primary"></i>Executive Reports & Performance Analytics</h4>
        <p class="text-muted mb-0">High-level enterprise delivery metrics, budget tracking, and resource utilization.</p>
    </div>
    <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="window.print()">
        <i class="bx bx-printer me-1"></i> Print / Export Report
    </button>
</div>

<!-- Financial & Delivery Highlights -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <span class="text-muted small">Total Portfolio Valuation</span>
            <h3 class="fw-bold text-primary mb-0">${{ number_format($totalBudget, 0) }}</h3>
            <small class="text-muted mt-1">{{ $totalProjects }} Active Initiatives</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <span class="text-muted small">Active / Ongoing Projects</span>
            <h3 class="fw-bold text-info mb-0">{{ $activeProjects }}</h3>
            <small class="text-success mt-1">{{ $completedProjects }} Successfully Delivered</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <span class="text-muted small">Agile Tasks Delivered</span>
            <h3 class="fw-bold text-success mb-0">{{ $completedTasks }} / {{ $totalTasks }}</h3>
            <small class="text-muted mt-1">{{ $totalTasks ? round(($completedTasks / $totalTasks) * 100) : 0 }}% Velocity</small>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <span class="text-muted small">Total Engineering Hours</span>
            <h3 class="fw-bold text-warning mb-0">{{ number_format($totalLoggedHours, 1) }}h</h3>
            <small class="text-muted mt-1">{{ $members->count() }} Technical Specialists</small>
        </div>
    </div>
</div>

<!-- Project Budget & Progress Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="bx bx-dollar-circle me-2 text-success"></i>Project Budget & Delivery Summary</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Project Code</th>
                    <th>Title & Domain</th>
                    <th>Client Stakeholder</th>
                    <th>Status</th>
                    <th>Budget</th>
                    <th>Logged Hours</th>
                    <th>Completion %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $p)
                <tr>
                    <td><span class="badge bg-label-primary font-monospace">{{ $p->code }}</span></td>
                    <td>
                        <a href="{{ route('projects.show', $p) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $p->title }}
                        </a>
                        <div class="small text-muted">{{ $p->category }}</div>
                    </td>
                    <td>{{ $p->client ? $p->client->company_name : 'Internal Initiative' }}</td>
                    <td>
                        @php
                            $stCol = match($p->status) {
                                'completed' => 'success',
                                'in_progress' => 'primary',
                                'planning' => 'info',
                                'on_hold' => 'warning',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-label-{{ $stCol }} text-capitalize">{{ str_replace('_', ' ', $p->status) }}</span>
                    </td>
                    <td><strong class="text-dark">${{ number_format($p->budget, 2) }}</strong></td>
                    <td><span class="badge bg-label-info">{{ $p->timeLogs->sum('hours') }} hrs</span></td>
                    <td style="min-width: 140px;">
                        <div class="d-flex align-items-center">
                            <div class="progress w-100 me-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $p->progress }}%"></div>
                            </div>
                            <span class="small fw-bold">{{ $p->progress }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Specialist Workload Distribution -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="bx bx-user me-2 text-primary"></i>Specialist Workload & Velocity Distribution</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Specialist Name</th>
                    <th>Department & Title</th>
                    <th>Total Assigned Tasks</th>
                    <th>Pending Tasks</th>
                    <th>Completed Tasks</th>
                    <th>Tracked Hours</th>
                    <th>Workload Ratio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $m)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">{{ $m->initials }}</span>
                            </div>
                            <span class="fw-bold text-dark">{{ $m->name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $m->job_title }}</div>
                        <div class="small text-muted">{{ $m->department }}</div>
                    </td>
                    <td><span class="badge bg-label-secondary fs-6">{{ $m->total_tasks }}</span></td>
                    <td><span class="badge bg-label-warning fs-6">{{ $m->pending_tasks }}</span></td>
                    <td><span class="badge bg-label-success fs-6">{{ $m->completed_tasks }}</span></td>
                    <td><strong class="text-primary">{{ $m->total_hours ?: 0 }} hrs</strong></td>
                    <td style="min-width: 130px;">
                        @php
                            $rate = $m->total_tasks > 0 ? round(($m->completed_tasks / $m->total_tasks) * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center">
                            <div class="progress w-100 me-2" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $rate }}%"></div>
                            </div>
                            <span class="small fw-semibold">{{ $rate }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
