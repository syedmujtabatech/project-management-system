@extends('layouts.app')

@section('title', 'Executive Project Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="card bg-primary text-white mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #3f51b5 100%) !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="text-white mb-1 fw-bold">Good day, {{ auth()->user()->name }}! 👋</h3>
                <p class="mb-0 text-white-50">
                    Welcome to the <strong>ApexPMS Enterprise</strong> workspace. You currently have 
                    <strong>{{ $activeProjects }} active projects</strong> and <strong>{{ $inProgressTasks }} tasks in active development</strong>.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tasks.kanban') }}" class="btn btn-light text-primary fw-semibold shadow-sm">
                    <i class="bx bx-grid-alt me-1"></i> Interactive Kanban
                </a>
                @if(auth()->user()->role !== 'client')
                <a href="{{ route('projects.create') }}" class="btn btn-outline-light fw-semibold">
                    <i class="bx bx-plus-circle me-1"></i> New Project
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 4 Key Metric Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Total Projects -->
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-glow h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold d-block text-muted">Total Projects</span>
                    <span class="badge bg-label-primary rounded p-2"><i class="bx bx-briefcase fs-4"></i></span>
                </div>
                <h3 class="card-title mb-1 fw-bold text-dark">{{ $totalProjects }}</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-check-circle"></i> {{ $completedProjects }} Completed
                </small>
                <span class="text-muted small">&bull; {{ $activeProjects }} Active</span>
            </div>
        </div>
    </div>

    <!-- Active Tasks -->
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-glow h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold d-block text-muted">Total Tasks</span>
                    <span class="badge bg-label-info rounded p-2"><i class="bx bx-task fs-4"></i></span>
                </div>
                <h3 class="card-title mb-1 fw-bold text-dark">{{ $totalTasks }}</h3>
                <small class="text-primary fw-semibold">
                    <i class="bx bx-loader-circle"></i> {{ $inProgressTasks }} In Progress
                </small>
                <span class="text-muted small">&bull; {{ $todoTasks }} To Do</span>
            </div>
        </div>
    </div>

    <!-- Total Budget -->
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-glow h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold d-block text-muted">Portfolio Budget</span>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar-circle fs-4"></i></span>
                </div>
                <h3 class="card-title mb-1 fw-bold text-dark">${{ number_format($totalBudget, 0) }}</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-buildings"></i> {{ $totalClients }} Enterprise Clients
                </small>
            </div>
        </div>
    </div>

    <!-- Logged Hours -->
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card-glow h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold d-block text-muted">Engineering Hours</span>
                    <span class="badge bg-label-warning rounded p-2"><i class="bx bx-time fs-4"></i></span>
                </div>
                <h3 class="card-title mb-1 fw-bold text-dark">{{ number_format($totalHoursLogged, 1) }}h</h3>
                <small class="text-info fw-semibold">
                    <i class="bx bx-user-check"></i> {{ $totalTeamMembers }} Team Specialists
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section: Tasks Breakdown & Category Breakdown -->
<div class="row g-3 mb-4">
    <!-- Tasks by Status Donut -->
    <div class="col-md-6 col-lg-5">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2 fw-bold"><i class="bx bx-pie-chart-alt-2 me-1 text-primary"></i>Task Distribution</h5>
                    <small class="text-muted">Real-time status of all agile tasks</small>
                </div>
            </div>
            <div class="card-body">
                <div id="taskStatusChart" style="min-height: 270px;"></div>
                <div class="d-flex justify-content-around text-center pt-2 border-top">
                    <div>
                        <span class="text-muted small">Backlog</span>
                        <h6 class="mb-0 fw-bold">{{ $taskStatusCounts['Backlog'] }}</h6>
                    </div>
                    <div>
                        <span class="text-muted small">To Do</span>
                        <h6 class="mb-0 fw-bold">{{ $taskStatusCounts['To Do'] }}</h6>
                    </div>
                    <div>
                        <span class="text-muted small">In Progress</span>
                        <h6 class="mb-0 fw-bold text-primary">{{ $taskStatusCounts['In Progress'] }}</h6>
                    </div>
                    <div>
                        <span class="text-muted small">In Review</span>
                        <h6 class="mb-0 fw-bold text-warning">{{ $taskStatusCounts['In Review'] }}</h6>
                    </div>
                    <div>
                        <span class="text-muted small">Done</span>
                        <h6 class="mb-0 fw-bold text-success">{{ $taskStatusCounts['Done'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects by Category Bar -->
    <div class="col-md-6 col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2 fw-bold"><i class="bx bx-bar-chart-alt me-1 text-info"></i>Projects by Industry Domain</h5>
                    <small class="text-muted">Domain allocation & active tech capabilities</small>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-label-primary">View All</a>
            </div>
            <div class="card-body">
                <div id="projectCategoryChart" style="min-height: 290px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Active Projects & Urgent Tasks -->
<div class="row g-3 mb-4">
    <!-- Active Projects Table -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="bx bx-folder me-2 text-primary"></i>Active Projects</h5>
                    <small class="text-muted">High-priority ongoing corporate initiatives</small>
                </div>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">Browse All Projects</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($recentProjects as $project)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary fw-bold">
                                            {{ substr($project->code, 0, 3) }}
                                        </span>
                                    </div>
                                    <div>
                                        <a href="{{ route('projects.show', $project) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $project->title }}
                                        </a>
                                        <div class="text-muted small">
                                            <code>{{ $project->code }}</code> &bull; {{ $project->category }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($project->client)
                                    <span class="fw-semibold">{{ $project->client->company_name }}</span>
                                @else
                                    <span class="text-muted">Internal</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pBadge = match($project->priority) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $pBadge }} text-capitalize">{{ $project->priority }}</span>
                            </td>
                            <td style="min-width: 140px;">
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress }}%" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small fw-semibold">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $sBadge = match($project->status) {
                                        'completed' => 'success',
                                        'in_progress' => 'primary',
                                        'planning' => 'info',
                                        'on_hold' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $sBadge }} text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-icon btn-label-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No projects found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines & Urgent Tasks -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="bx bx-alarm me-2 text-danger"></i>Upcoming Deadlines</h5>
                    <small class="text-muted">Critical tasks due soon</small>
                </div>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-label-secondary">All Tasks</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($upcomingTasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                        <div class="me-auto overflow-hidden">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge bg-label-secondary me-2 font-monospace">{{ $task->task_number }}</span>
                                @php
                                    $tBadge = match($task->priority) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $tBadge }} text-capitalize me-2" style="font-size: 0.7rem;">{{ $task->priority }}</span>
                            </div>
                            <a href="{{ route('tasks.show', $task) }}" class="fw-semibold text-dark text-truncate d-block text-decoration-none">
                                {{ $task->title }}
                            </a>
                            <small class="text-muted">{{ $task->project->title ?? 'N/A' }}</small>
                        </div>
                        <div class="text-end ps-2">
                            @if($task->due_date)
                                <div class="badge bg-label-{{ $task->due_date->isPast() ? 'danger' : 'info' }} small">
                                    {{ $task->due_date->format('M d') }}
                                </div>
                            @endif
                            <div class="mt-1">
                                @if($task->assignee)
                                    <span class="avatar avatar-xs" title="{{ $task->assignee->name }}">
                                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">
                                            {{ $task->assignee->initials }}
                                        </span>
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary" style="font-size: 0.65rem;">Unassigned</span>
                                @endif
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted">No pending tasks with upcoming deadlines.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Log -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold"><i class="bx bx-history me-2 text-warning"></i>Audit Trail & Team Activities</h5>
            <small class="text-muted">Real-time collaboration stream across all projects</small>
        </div>
    </div>
    <div class="card-body">
        <ul class="timeline mb-0 list-unstyled">
            @forelse($recentActivities as $act)
            <li class="d-flex align-items-start mb-3 pb-2 border-bottom">
                <div class="avatar avatar-sm me-3 flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold">
                        {{ $act->user ? $act->user->initials : 'SYS' }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="mb-0 fw-semibold text-dark">
                            {{ $act->action }}
                            @if($act->user)
                                <span class="text-muted fw-normal">by {{ $act->user->name }}</span>
                            @endif
                        </h6>
                        <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 text-secondary small">{{ $act->description }}</p>
                </div>
            </li>
            @empty
            <li class="text-center py-3 text-muted">No recent activity recorded yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Task Status Donut Chart
    const taskStatusCounts = @json(array_values($taskStatusCounts));
    const taskStatusLabels = @json(array_keys($taskStatusCounts));

    const taskChartOptions = {
        series: taskStatusCounts,
        labels: taskStatusLabels,
        chart: {
            type: 'donut',
            height: 270,
            toolbar: { show: false }
        },
        colors: ['#8592a3', '#03c3ec', '#696cff', '#ffab00', '#71dd37'],
        stroke: { width: 2, colors: ['#fff'] },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', horizontalAlign: 'center' },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        value: {
                            fontSize: '1.25rem',
                            fontWeight: 600,
                            formatter: (val) => val
                        },
                        total: {
                            show: true,
                            label: 'Total Tasks',
                            fontSize: '0.85rem',
                            formatter: () => '{{ $totalTasks }}'
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#taskStatusChart"), taskChartOptions).render();

    // 2. Projects by Category Bar Chart
    const categoryLabels = @json(array_keys($projectsByCategory));
    const categoryCounts = @json(array_values($projectsByCategory));

    const categoryChartOptions = {
        series: [{
            name: 'Active Projects',
            data: categoryCounts
        }],
        chart: {
            type: 'bar',
            height: 290,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true,
                dataLabels: { position: 'top' }
            }
        },
        colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d'],
        legend: { show: false },
        xaxis: {
            categories: categoryLabels,
            labels: { style: { fontSize: '12px' } }
        },
        yaxis: {
            labels: { style: { fontSize: '12px' } }
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => val + ' Projects',
            offsetX: 20,
            style: { fontSize: '11px', colors: ['#566a7f'] }
        }
    };
    new ApexCharts(document.querySelector("#projectCategoryChart"), categoryChartOptions).render();
});
</script>
@endpush
