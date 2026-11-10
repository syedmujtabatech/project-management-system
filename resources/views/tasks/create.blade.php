@extends('layouts.app')

@section('title', 'Create Agile Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-plus-circle me-2 text-primary"></i>Create Agile Task</h5>
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Tasks
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="title">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Implement mTLS on Kubernetes Cilium" required />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="task_number">Task Code</label>
                            <input type="text" class="form-control font-monospace" id="task_number" name="task_number" value="{{ old('task_number', $suggestedTaskNumber) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="project_id">Target Project <span class="text-danger">*</span></label>
                            <select class="form-select" id="project_id" name="project_id" required>
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ (old('project_id', $selectedProject ? $selectedProject->id : null) == $p->id) ? 'selected' : '' }}>
                                        [{{ $p->code }}] {{ $p->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="milestone_id">Milestone / Sprint (Optional)</label>
                            <select class="form-select" id="milestone_id" name="milestone_id">
                                <option value="">-- None / General Backlog --</option>
                                @foreach($milestones as $m)
                                    <option value="{{ $m->id }}" {{ old('milestone_id') == $m->id ? 'selected' : '' }}>
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
                                    <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->job_title }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="priority">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="status">Initial Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="backlog">Backlog</option>
                                <option value="todo" selected>To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="in_review">In Review</option>
                                <option value="done">Done</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="estimated_hours">Estimated Effort (Hours)</label>
                            <input type="number" step="0.5" class="form-control" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', '8.0') }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="description">Task Description & Deliverables</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Detail implementation guidelines, test requirements, technical constraints...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check me-1"></i> Save Task</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
