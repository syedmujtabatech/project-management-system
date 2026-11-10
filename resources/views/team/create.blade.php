@extends('layouts.app')

@section('title', 'Add Specialist')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-user-plus me-2 text-primary"></i>Onboard New Specialist</h5>
                <a href="{{ route('team.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('team.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Liam Foster" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Work Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="liam@apexcompany.com" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 characters" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="role">Role Permission <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="team_member" selected>Team Member (Engineer / Designer / QA)</option>
                                <option value="project_manager">Project Manager</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="job_title">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="e.g. Senior Security Specialist" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="department">Department <span class="text-danger">*</span></label>
                            <select class="form-select" id="department" name="department" required>
                                <option value="Core Engineering" selected>Core Engineering</option>
                                <option value="Product Design & UI">Product Design & UI</option>
                                <option value="Quality Assurance">Quality Assurance</option>
                                <option value="Cloud Infrastructure">Cloud Infrastructure</option>
                                <option value="Project Management Office">Project Management Office</option>
                                <option value="Executive Management">Executive Management</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="phone">Direct Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" />
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check me-1"></i> Register Specialist</button>
                        <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
