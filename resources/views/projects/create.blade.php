@extends('layouts.app')

@section('title', 'Create Enterprise Project')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-plus-circle me-2 text-primary"></i>Initiate New Enterprise Project</h5>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Projects
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Project Title -->
                        <div class="col-md-8">
                            <label class="form-label" for="title">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. NextGen Microservices Payment Gateway" required />
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Project Code -->
                        <div class="col-md-4">
                            <label class="form-label" for="code">Project Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $suggestedCode) }}" required />
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Client -->
                        <div class="col-md-6">
                            <label class="form-label" for="client_id">Client Company / Stakeholder</label>
                            <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id">
                                <option value="">-- Internal Company Project (No External Client) --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name }} ({{ $client->contact_person }})
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Project Manager -->
                        <div class="col-md-6">
                            <label class="form-label" for="manager_id">Designated Project Manager <span class="text-danger">*</span></label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id" required>
                                @foreach($managers as $m)
                                    <option value="{{ $m->id }}" {{ old('manager_id', auth()->id()) == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->job_title }})
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Category -->
                        <div class="col-md-4">
                            <label class="form-label" for="category">Domain / Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="Financial Technology" {{ old('category') == 'Financial Technology' ? 'selected' : '' }}>Financial Technology</option>
                                <option value="Healthcare & MedTech" {{ old('category') == 'Healthcare & MedTech' ? 'selected' : '' }}>Healthcare & MedTech</option>
                                <option value="Retail & E-Commerce" {{ old('category') == 'Retail & E-Commerce' ? 'selected' : '' }}>Retail & E-Commerce</option>
                                <option value="Supply Chain & AI" {{ old('category') == 'Supply Chain & AI' ? 'selected' : '' }}>Supply Chain & AI</option>
                                <option value="Cloud Infrastructure & Security" {{ old('category') == 'Cloud Infrastructure & Security' ? 'selected' : '' }}>Cloud Infrastructure & Security</option>
                                <option value="Mobile Application" {{ old('category') == 'Mobile Application' ? 'selected' : '' }}>Mobile Application</option>
                                <option value="Enterprise ERP" {{ old('category') == 'Enterprise ERP' ? 'selected' : '' }}>Enterprise ERP</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-4">
                            <label class="form-label" for="priority">Priority Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-4">
                            <label class="form-label" for="status">Initial Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-3">
                            <label class="form-label" for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" />
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-3">
                            <label class="form-label" for="due_date">Target Completion Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}" />
                        </div>

                        <!-- Budget -->
                        <div class="col-md-3">
                            <label class="form-label" for="budget">Allocated Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="{{ old('budget', '50000.00') }}" />
                            </div>
                        </div>

                        <!-- Currency -->
                        <div class="col-md-3">
                            <label class="form-label" for="currency">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="USD" selected>USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="AED">AED (د.إ)</option>
                                <option value="PKR">PKR (₨)</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label" for="description">Project Scope & Technical Objectives</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Detail high-level deliverables, compliance expectations, architecture specifications...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Team Members Multiselect -->
                        <div class="col-12">
                            <label class="form-label">Assign Team Specialists</label>
                            <div class="row g-2 p-3 border rounded bg-light">
                                @foreach($teamMembers as $member)
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="members[]" value="{{ $member->id }}" id="member_{{ $member->id }}" {{ in_array($member->id, old('members', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="member_{{ $member->id }}">
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

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check me-1"></i> Save Project</button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
