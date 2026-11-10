@extends('layouts.app')

@section('title', 'Agile Kanban Board')

@section('content')
<!-- Header & Filters -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-grid-alt me-2 text-primary"></i>Agile Kanban Board</h4>
        <p class="text-muted mb-0">Drag and drop tasks between swimlanes to update real-time progress.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="bx bx-list-ul me-1"></i> List View
        </a>
        @if(auth()->user()->role !== 'client')
        <a href="{{ route('tasks.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bx bx-plus-circle me-1"></i> New Task
        </a>
        @endif
    </div>
</div>

<!-- Filters Bar -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('tasks.kanban') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-6">
                <select name="project_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Projects --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                            [{{ $p->code }}] {{ $p->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Assignees --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->job_title }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="priority" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Priorities --</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <a href="{{ route('tasks.kanban') }}" class="btn btn-outline-secondary w-100">
                    <i class="bx bx-reset me-1"></i> Reset Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Toast for Real-time AJAX Updates -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1090">
    <div id="kanbanToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center" id="toastMessage">
                <i class="bx bx-check-circle me-2 fs-5"></i> Task status updated!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Kanban Swimlanes Board -->
<div class="kanban-board">
    @foreach($columns as $colKey => $colData)
    <div class="kanban-col" data-status="{{ $colKey }}">
        <!-- Column Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <span class="badge bg-label-{{ $colData['color'] }} p-2 rounded me-2">
                    <i class="bx {{ $colData['icon'] }} fs-5"></i>
                </span>
                <h6 class="mb-0 fw-bold text-dark">{{ $colData['title'] }}</h6>
            </div>
            <span class="badge rounded-pill bg-{{ $colData['color'] }} px-2 py-1 col-counter" id="counter-{{ $colKey }}">
                {{ $colData['tasks']->count() }}
            </span>
        </div>

        <!-- Column Drop Body -->
        <div class="kanban-col-body" id="col-{{ $colKey }}">
            @foreach($colData['tasks'] as $task)
            <div class="kanban-card priority-{{ $task->priority }}" draggable="true" data-task-id="{{ $task->id }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-label-secondary font-monospace" style="font-size: 0.72rem;">{{ $task->task_number }}</span>
                    @php
                        $priorityBadge = match($task->priority) {
                            'urgent' => 'danger',
                            'high' => 'warning',
                            'medium' => 'info',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-label-{{ $priorityBadge }} text-capitalize" style="font-size: 0.68rem;">{{ $task->priority }}</span>
                </div>

                <a href="{{ route('tasks.show', $task) }}" class="fw-bold text-dark text-decoration-none d-block mb-1">
                    {{ $task->title }}
                </a>

                <div class="small text-muted mb-2">
                    <i class="bx bx-folder me-1"></i>
                    {{ $task->project ? Str::limit($task->project->title, 26) : 'N/A' }}
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                    <div class="small text-muted">
                        @if($task->due_date)
                            <span class="{{ ($task->due_date->isPast() && $task->status !== 'done') ? 'text-danger fw-bold' : '' }}">
                                <i class="bx bx-calendar me-1"></i>{{ $task->due_date->format('M d') }}
                            </span>
                        @else
                            <span>No date</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if($task->comments->count() > 0)
                            <span class="small text-muted" title="{{ $task->comments->count() }} comments">
                                <i class="bx bx-comment-detail"></i> {{ $task->comments->count() }}
                            </span>
                        @endif

                        @if($task->assignee)
                            <div class="avatar avatar-xs" title="{{ $task->assignee->name }}">
                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold" style="font-size: 0.65rem;">
                                    {{ $task->assignee->initials }}
                                </span>
                            </div>
                        @else
                            <span class="avatar avatar-xs" title="Unassigned">
                                <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size: 0.65rem;">?</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let draggedCard = null;
    let sourceCol = null;

    const cards = document.querySelectorAll('.kanban-card');
    const cols = document.querySelectorAll('.kanban-col');

    cards.forEach(card => {
        card.addEventListener('dragstart', (e) => {
            draggedCard = card;
            sourceCol = card.closest('.kanban-col');
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.dataset.taskId);
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', () => {
            if (draggedCard) {
                draggedCard.classList.remove('dragging');
            }
            cols.forEach(c => c.classList.remove('drag-over'));
            draggedCard = null;
        });
    });

    cols.forEach(col => {
        const body = col.querySelector('.kanban-col-body');

        col.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('drag-over');
        });

        col.addEventListener('dragleave', (e) => {
            // Only remove if leaving the column element completely
            if (!col.contains(e.relatedTarget)) {
                col.classList.remove('drag-over');
            }
        });

        col.addEventListener('drop', (e) => {
            e.preventDefault();
            col.classList.remove('drag-over');

            if (!draggedCard) return;

            const targetStatus = col.dataset.status;
            const taskId = draggedCard.dataset.taskId;

            // Append to new column body
            body.appendChild(draggedCard);

            // Update column badge counters
            updateCounters();

            // Send AJAX update to backend
            fetch(`{{ url('/tasks') }}/${taskId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: targetStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                }
            })
            .catch(err => {
                console.error('Error updating status:', err);
                // Revert on error
                if (sourceCol) {
                    sourceCol.querySelector('.kanban-col-body').appendChild(draggedCard);
                    updateCounters();
                }
            });
        });
    });

    function updateCounters() {
        cols.forEach(col => {
            const count = col.querySelectorAll('.kanban-card').length;
            const counter = col.querySelector('.col-counter');
            if (counter) counter.textContent = count;
        });
    }

    function showToast(message) {
        const toastEl = document.getElementById('kanbanToast');
        document.getElementById('toastMessage').innerHTML = `<i class="bx bx-check-circle me-2 fs-5"></i> ${message}`;
        const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
        toast.show();
    }
});
</script>
@endpush
