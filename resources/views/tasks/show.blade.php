@extends('layouts.app')

@section('title', '[' . $task->task_number . '] ' . $task->title)

@section('content')
<div class="row g-4">
    <!-- Left Column: Details & Comments -->
    <div class="col-lg-8">
        <!-- Main Task Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-label-secondary font-monospace fs-6">{{ $task->task_number }}</span>
                        @php
                            $pBadge = match($task->priority) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                default => 'secondary'
                            };
                            $sBadge = match($task->status) {
                                'done' => 'success',
                                'in_progress' => 'primary',
                                'in_review' => 'warning',
                                'todo' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-label-{{ $pBadge }} text-capitalize">{{ $task->priority }} Priority</span>
                        <span class="badge bg-label-{{ $sBadge }} text-capitalize">{{ str_replace('_', ' ', $task->status) }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        @if(auth()->user()->role !== 'client')
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('tasks.kanban', ['project_id' => $task->project_id]) }}" class="btn btn-sm btn-label-secondary">
                            <i class="bx bx-grid-alt me-1"></i> View in Kanban
                        </a>
                    </div>
                </div>

                <h3 class="fw-bold mb-2 text-dark">{{ $task->title }}</h3>

                <div class="text-muted small mb-3">
                    <i class="bx bx-folder me-1"></i> Project:
                    <a href="{{ route('projects.show', $task->project) }}" class="fw-bold text-primary text-decoration-none">
                        [{{ $task->project->code }}] {{ $task->project->title }}
                    </a>
                    @if($task->milestone)
                        &bull; <i class="bx bx-flag ms-2 me-1"></i> Milestone: <strong>{{ $task->milestone->title }}</strong>
                    @endif
                </div>

                <div class="p-3 bg-light rounded text-secondary mb-4" style="line-height: 1.7; min-height: 100px;">
                    {!! nl2br(e($task->description ?: 'No detailed task instructions provided.')) !!}
                </div>

                <!-- Instant Status Update Selector -->
                <div class="p-3 border rounded mb-3 bg-white">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Change Task Agile Status</label>
                    <div class="btn-group w-100" role="group">
                        @foreach(['backlog' => 'Backlog', 'todo' => 'To Do', 'in_progress' => 'In Progress', 'in_review' => 'In Review', 'done' => 'Done'] as $stVal => $stLabel)
                        <button type="button" class="btn btn-sm {{ $task->status === $stVal ? 'btn-primary' : 'btn-outline-secondary' }}" onclick="updateTaskStatus('{{ $stVal }}')">
                            {{ $stLabel }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments & Engineering Discussions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-comment-detail me-2 text-primary"></i>Team Collaboration & Discussion</h5>
                <span class="badge rounded-pill bg-label-primary">{{ $task->comments->count() }} Comments</span>
            </div>
            <div class="card-body">
                <!-- Comments List -->
                <div class="comments-list mb-4">
                    @forelse($task->comments as $comment)
                    <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="avatar avatar-sm me-3 flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold">
                                {{ $comment->user ? $comment->user->initials : 'U' }}
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-semibold text-dark">{{ $comment->user ? $comment->user->name : 'Anonymous' }}</h6>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="small text-muted mb-1">{{ $comment->user ? $comment->user->job_title : '' }}</div>
                            <p class="mb-0 text-secondary small bg-light p-2 rounded">{{ $comment->comment }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">No comments yet. Start the conversation below.</div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('tasks.comments.store', $task) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-semibold">Add a Comment / Note</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Share technical updates, blocker notices, or review feedback..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-send me-1"></i> Post Comment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Meta Info, Assignee & Time Tracking -->
    <div class="col-lg-4">
        <!-- Assignee & Creator Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bx bx-user-check me-2 text-info"></i>Assignment Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Assigned Specialist:</span>
                    @if($task->assignee)
                    <div class="d-flex align-items-center p-2 rounded bg-light">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded-circle bg-primary text-white">{{ $task->assignee->initials }}</span>
                        </div>
                        <div>
                            <div class="fw-bold small text-dark">{{ $task->assignee->name }}</div>
                            <small class="text-muted">{{ $task->assignee->job_title }}</small>
                        </div>
                    </div>
                    @else
                    <span class="badge bg-label-secondary">Unassigned</span>
                    @endif
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Created By:</span>
                    <div class="small fw-semibold">{{ $task->creator ? $task->creator->name : 'System' }}</div>
                </div>

                <hr>

                <div class="row g-2 text-center">
                    <div class="col-6">
                        <span class="text-muted small d-block">Est. Effort</span>
                        <span class="fw-bold fs-5 text-dark">{{ $task->estimated_hours }}h</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Logged Hours</span>
                        <span class="fw-bold fs-5 text-success">{{ $task->logged_hours }}h</span>
                    </div>
                </div>

                <hr>

                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Start Date:</span>
                        <span class="fw-semibold">{{ $task->start_date ? $task->start_date->format('M d, Y') : 'N/A' }}</span>
                    </li>
                    <li class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Target Due Date:</span>
                        <span class="fw-semibold {{ ($task->due_date && $task->due_date->isPast() && $task->status !== 'done') ? 'text-danger' : '' }}">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Completed On:</span>
                        <span class="fw-semibold text-success">{{ $task->completed_at ? $task->completed_at->format('M d, Y') : 'Incomplete' }}</span>
                    </li>
                </ul>

                <button type="button" class="btn btn-outline-success btn-sm w-100 mt-3" data-bs-toggle="modal" data-bs-target="#taskLogTimeModal">
                    <i class="bx bx-time me-1"></i> Log Hours on Task
                </button>
            </div>
        </div>

        <!-- Task Time Logs History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bx bx-time me-2 text-warning"></i>Logged Time Records</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($task->timeLogs as $tlog)
                    <li class="list-group-item py-2 px-3 small">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">{{ $tlog->hours }} hours</span>
                            <span class="text-muted">{{ $tlog->log_date ? $tlog->log_date->format('M d') : '' }}</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">by {{ $tlog->user ? $tlog->user->name : 'N/A' }}</div>
                        @if($tlog->notes)
                            <div class="text-secondary mt-1 fst-italic" style="font-size: 0.72rem;">"{{ $tlog->notes }}"</div>
                        @endif
                    </li>
                    @empty
                    <li class="list-group-item text-center py-3 text-muted small">No hours logged directly to this task.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Time on Task -->
<div class="modal fade" id="taskLogTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('timelogs.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="project_id" value="{{ $task->project_id }}">
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bx bx-time me-2 text-success"></i>Log Hours for {{ $task->task_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="task_log_hours">Hours Worked <span class="text-danger">*</span></label>
                    <input type="number" step="0.25" min="0.25" max="24" class="form-control" id="task_log_hours" name="hours" value="2.0" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="task_log_date">Log Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="task_log_date" name="log_date" value="{{ date('Y-m-d') }}" required />
                </div>
                <div class="col-12">
                    <label class="form-label" for="task_log_notes">Work Description</label>
                    <textarea class="form-control" id="task_log_notes" name="notes" rows="3" placeholder="What did you complete?"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="bx bx-check me-1"></i>Save Time Log</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTaskStatus(newStatus) {
    fetch("{{ route('tasks.update-status', $task) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "Accept": "application/json"
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(e => console.error(e));
}
</script>
@endpush
