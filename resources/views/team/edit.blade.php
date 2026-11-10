@extends('layouts.app')

@section('title', 'Edit Specialist: ' . $user->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-edit me-2 text-primary"></i>Modify Specialist Details</h5>
                <a href="{{ route('team.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('team.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Work Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">Change Password (Leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="New password" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="role">Role Permission <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="team_member" {{ old('role', $user->role) === 'team_member' ? 'selected' : '' }}>Team Member</option>
                                <option value="project_manager" {{ old('role', $user->role) === 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="job_title">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="department">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="department" name="department" value="{{ old('department', $user->department) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phone">Direct Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="is_active">Account Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Active Account</option>
                                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Suspended / Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Specialist</button>
                        <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
