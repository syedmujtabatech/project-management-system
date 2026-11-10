@extends('layouts.app')

@section('title', 'Enterprise Clients CRM')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bx bx-buildings me-2 text-primary"></i>Enterprise Clients & Accounts</h4>
        <p class="text-muted mb-0">Client relationship management and project stakeholder directory.</p>
    </div>
    @if(auth()->user()->role !== 'client')
    <a href="{{ route('clients.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="bx bx-plus-circle me-1"></i> Add Client
    </a>
    @endif
</div>

<!-- Search & Filter -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ route('clients.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6 col-sm-12">
                <input type="text" name="search" class="form-control" placeholder="Search company name, contact person, or email..." value="{{ request('search') }}" />
            </div>
            <div class="col-md-3 col-sm-6">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Clients Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Company Name</th>
                    <th>Contact Person</th>
                    <th>Email & Phone</th>
                    <th>Location</th>
                    <th>Projects</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-info fw-bold">
                                    {{ strtoupper(substr($c->company_name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <a href="{{ route('clients.show', $c) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $c->company_name }}
                                </a>
                                @if($c->website)
                                    <div class="text-muted small"><a href="{{ $c->website }}" target="_blank" class="text-muted text-decoration-none"><i class="bx bx-link-external me-1"></i>Website</a></div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-semibold">{{ $c->contact_person }}</span>
                    </td>
                    <td>
                        <div class="small text-dark">{{ $c->email }}</div>
                        <small class="text-muted">{{ $c->phone ?: '-' }}</small>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $c->city ? $c->city . ', ' . $c->country : ($c->country ?: '-') }}</span>
                    </td>
                    <td>
                        <span class="badge bg-label-primary fs-6">{{ $c->projects_count }} Total</span>
                        <span class="badge bg-label-success ms-1">{{ $c->active_projects_count }} Active</span>
                    </td>
                    <td>
                        <span class="badge bg-label-{{ $c->status === 'active' ? 'success' : 'secondary' }} text-capitalize">
                            {{ $c->status }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('clients.show', $c) }}" class="btn btn-xs btn-label-primary"><i class="bx bx-show"></i></a>
                            @if(auth()->user()->role !== 'client')
                            <a href="{{ route('clients.edit', $c) }}" class="btn btn-xs btn-label-info"><i class="bx bx-edit"></i></a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">No clients found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clients->hasPages())
    <div class="card-footer py-3 border-top">
        {{ $clients->links() }}
    </div>
    @endif
</div>
@endsection
