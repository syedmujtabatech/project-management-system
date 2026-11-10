@extends('layouts.app')

@section('title', 'Edit Task: ' . $task->task_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-edit me-2 text-primary"></i>Modify Task [{{ $task->task_number }}]</h5>
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Task Details
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="title">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" required />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="task_number">Task Code</label>
                            <input type="text" class="form-control font-monospace bg-light" id="task_number" value="{{ $task->task_number }}" readonly />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="project_id">Target Project <span class="text-danger">*</span></label>
                            <select class="form-select" id="project_id" name="project_id" required>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ old('project_id', $task->project_id) == $p->id ? 'selected' : '' }}>
                                        [{{ $p->code }}] {{ $p->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="milestone_id">Milestone / Sprint</label>
                            <select class="form-select" id="milestone_id" name="milestone_id">
                                <option value="">-- None / General Backlog --</option>
                                @foreach($milestones as $m)
                                    <option value="{{ $m->id }}" {{ old('milestone_id', $task->milestone_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="assigned_to">Assignee</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('assigned_to', $task->assigned_to) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->job_title }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="priority">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="backlog" {{ old('status', $task->status) === 'backlog' ? 'selected' : '' }}>Backlog</option>
                                <option value="todo" {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="in_review" {{ old('status', $task->status) === 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="done" {{ old('status', $task->status) === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="estimated_hours">Estimated Effort (Hours)</label>
                            <input type="number" step="0.5" class="form-control" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $task->estimated_hours) }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="description">Task Description & Deliverables</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Task</button>
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="bx bx-trash me-1"></i> Delete Task</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
