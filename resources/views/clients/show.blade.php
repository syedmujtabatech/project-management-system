@extends('layouts.app')

@section('title', 'Client Profile: ' . $client->company_name)

@section('content')
<div class="row g-4">
    <!-- Client Overview Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary fs-2 font-weight-bold">
                        {{ strtoupper(substr($client->company_name, 0, 2)) }}
                    </span>
                </div>
                <h4 class="mb-1 fw-bold text-dark">{{ $client->company_name }}</h4>
                <p class="text-muted mb-2">Stakeholder: <strong>{{ $client->contact_person }}</strong></p>
                <span class="badge bg-label-{{ $client->status === 'active' ? 'success' : 'secondary' }} text-capitalize mb-3">
                    {{ $client->status }} Client
                </span>

                <hr>

                <div class="text-start small">
                    <div class="mb-2"><i class="bx bx-envelope text-primary me-2"></i> {{ $client->email }}</div>
                    <div class="mb-2"><i class="bx bx-phone text-primary me-2"></i> {{ $client->phone ?: 'No phone recorded' }}</div>
                    @if($client->website)
                    <div class="mb-2"><i class="bx bx-globe text-primary me-2"></i> <a href="{{ $client->website }}" target="_blank">{{ $client->website }}</a></div>
                    @endif
                    <div class="mb-2"><i class="bx bx-map-pin text-primary me-2"></i> {{ $client->address ? $client->address . ', ' : '' }}{{ $client->city }} {{ $client->country }}</div>
                </div>

                @if($client->notes)
                <div class="mt-3 p-3 bg-light rounded text-start small text-secondary">
                    <div class="fw-bold mb-1 text-dark">Notes:</div>
                    {{ $client->notes }}
                </div>
                @endif

                @if(auth()->user()->role !== 'client')
                <div class="mt-4 pt-2 border-top">
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bx bx-edit me-1"></i> Edit Client Profile
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Associated Projects -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-briefcase-alt-2 me-2 text-primary"></i>Projects for {{ $client->company_name }}</h5>
                @if(auth()->user()->role !== 'client')
                <a href="{{ route('projects.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> New Project for Client
                </a>
                @endif
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Budget</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->projects as $prj)
                        <tr>
                            <td><span class="badge bg-label-primary font-monospace">{{ $prj->code }}</span></td>
                            <td>
                                <a href="{{ route('projects.show', $prj) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $prj->title }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $stCol = match($prj->status) {
                                        'completed' => 'success',
                                        'in_progress' => 'primary',
                                        'planning' => 'info',
                                        'on_hold' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $stCol }} text-capitalize">{{ str_replace('_', ' ', $prj->status) }}</span>
                            </td>
                            <td><span class="badge bg-label-secondary text-capitalize">{{ $prj->priority }}</span></td>
                            <td style="min-width: 120px;">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $prj->progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $prj->progress }}%</small>
                            </td>
                            <td><strong>${{ number_format($prj->budget, 0) }}</strong></td>
                            <td>
                                <a href="{{ route('projects.show', $prj) }}" class="btn btn-xs btn-label-primary"><i class="bx bx-show"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No projects currently assigned to this client.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
