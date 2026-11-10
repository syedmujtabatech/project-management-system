@extends('layouts.app')

@section('title', 'Add Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-buildings me-2 text-primary"></i>Register New Client Company</h5>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="contact_person">Primary Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Billing / Contact Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" />
                        </div>

                        <div class="col-md-8">
                            <label class="form-label" for="website">Company Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="status">Account Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="address">Corporate Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="notes">Internal Account Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bx bx-check me-1"></i> Register Client</button>
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
