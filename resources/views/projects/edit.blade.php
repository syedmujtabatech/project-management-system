@extends('layouts.app')

@section('title', 'Edit Project: ' . $project->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-edit me-2 text-primary"></i>Modify Project: {{ $project->title }}</h5>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Details
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="title">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $project->title) }}" required />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="code">Project Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="code" name="code" value="{{ old('code', $project->code) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="client_id">Client Company / Stakeholder</label>
                            <select class="form-select" id="client_id" name="client_id">
                                <option value="">-- Internal Company Project --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="manager_id">Designated Project Manager</label>
                            <select class="form-select" id="manager_id" name="manager_id">
                                @foreach($managers as $m)
                                    <option value="{{ $m->id }}" {{ old('manager_id', $project->manager_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->job_title }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="category">Category</label>
                            <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $project->category) }}" required />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="priority">Priority Level</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $project->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="in_progress" {{ old('status', $project->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $project->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="due_date">Target Completion Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $project->due_date ? $project->due_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="budget">Allocated Budget</label>
                            <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="currency">Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency" value="{{ old('currency', $project->currency) }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="description">Scope & Technical Objectives</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Assigned Team Specialists</label>
                            <div class="row g-2 p-3 border rounded bg-light">
                                @foreach($teamMembers as $member)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="members[]" value="{{ $member->id }}" id="edit_member_{{ $member->id }}" {{ in_array($member->id, old('members', $selectedMembers)) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="edit_member_{{ $member->id }}">
                                            <span class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 0.65rem;">{{ $member->initials }}</span>
                                            </span>
                                            <div>
                                                <div class="fw-semibold small">{{ $member->name }}</div>
                                                <small class="text-muted" style="font-size: 0.72rem;">{{ $member->job_title }}</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Project</button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project and all its tasks?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Delete Project
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
