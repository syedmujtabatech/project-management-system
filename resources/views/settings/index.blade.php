@extends('layouts.app')

@section('title', 'Enterprise Company Settings')

@section('content')
<div class="row g-4">
    <!-- Company Profile Settings -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="bx bx-cog me-2 text-primary"></i>Company Profile & System Defaults</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="company_name">Company Legal Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $setting->company_name) }}" required />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="tagline">Enterprise Tagline</label>
                            <input type="text" class="form-control" id="tagline" name="tagline" value="{{ old('tagline', $setting->tagline) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="company_email">Official Corporate Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="company_email" name="company_email" value="{{ old('company_email', $setting->company_email) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="company_phone">Support / Hotline Phone</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone" value="{{ old('company_phone', $setting->company_phone) }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="website">Company Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $setting->website) }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="address">Corporate Headquarters Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $setting->address) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="currency">Default Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="USD" {{ $setting->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ $setting->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ $setting->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="AED" {{ $setting->currency === 'AED' ? 'selected' : '' }}>AED</option>
                                <option value="PKR" {{ $setting->currency === 'PKR' ? 'selected' : '' }}>PKR</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="currency_symbol">Currency Symbol</label>
                            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $setting->currency_symbol) }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="timezone">Workspace Timezone</label>
                            <input type="text" class="form-control" id="timezone" name="timezone" value="{{ old('timezone', $setting->timezone) }}" />
                        </div>
                    </div>

                    @if(auth()->user()->role === 'super_admin')
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save Company Profile</button>
                    </div>
                    @else
                    <div class="alert alert-info mt-4 mb-0 small">
                        <i class="bx bx-info-circle me-1"></i> Only Super Administrators can modify organizational configuration settings.
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Right Side: System Logo Preview & Audit Stream -->
    <div class="col-lg-5">
        <!-- Logo Preview -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bx bx-image me-2 text-info"></i>System Brand Identity</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="p-3 bg-light rounded border mb-3">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt="ApexPMS Enterprise" style="max-height: 56px; width: auto;" />
                </div>
                <div class="p-3 bg-dark rounded border mb-3">
                    <img src="{{ asset('assets/img/logo.svg') }}" alt="ApexPMS Dark Mode" style="max-height: 56px; width: auto;" />
                </div>
                <small class="text-muted d-block">SVG Vector Brand Asset &bull; Resolution Independent</small>
            </div>
        </div>

        <!-- Recent Audit Events -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bx bx-shield-quarter me-2 text-warning"></i>Security & Activity Audit Stream</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                    @forelse($recentLogs as $log)
                    <li class="list-group-item py-2 px-3 small">
                        <div class="d-flex justify-content-between">
                            <strong class="text-dark">{{ $log->action }}</strong>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $log->description }}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">IP: {{ $log->ip_address ?: '127.0.0.1' }} &bull; User: {{ $log->user ? $log->user->name : 'System' }}</small>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-3 text-muted small">No audit logs recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
