@extends('layouts.app')

@section('title', 'My Profile & Security')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bx bx-user me-2"></i>Account & Profile Settings</span>
                <span class="badge bg-label-primary text-uppercase">{{ str_replace('_', ' ', $user->role) }}</span>
            </h5>
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4 pb-4 border-bottom mb-4">
                    <div class="avatar avatar-xl">
                        <span class="avatar-initial rounded-circle bg-primary text-white fs-2">
                            {{ $user->initials }}
                        </span>
                    </div>
                    <div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->job_title ?? 'Employee' }} &bull; {{ $user->department ?? 'Engineering' }}</p>
                        <small class="text-secondary"><i class="bx bx-envelope me-1"></i>{{ $user->email }}</small>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email Address (Read Only)</label>
                            <input class="form-control bg-light" type="text" id="email" value="{{ $user->email }}" readonly />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="job_title" class="form-label">Job Title / Designation</label>
                            <input class="form-control" type="text" id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <input class="form-control" type="text" id="department" name="department" value="{{ old('department', $user->department) }}" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input class="form-control" type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000" />
                        </div>
                    </div>

                    <h5 class="mt-4 pt-3 border-top mb-3"><i class="bx bx-lock-alt me-2"></i>Change Password (Optional)</h5>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input class="form-control" type="password" id="current_password" name="current_password" placeholder="••••••••" />
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="new_password" class="form-label">New Password</label>
                            <input class="form-control" type="password" id="new_password" name="new_password" placeholder="At least 6 characters" />
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Confirm password" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2"><i class="bx bx-save me-1"></i>Save Changes</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
