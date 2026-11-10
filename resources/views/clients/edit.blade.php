@extends('layouts.app')

@section('title', 'Edit Client: ' . $client->company_name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bx bx-edit me-2 text-primary"></i>Modify Client Profile</h5>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="contact_person">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="email">Billing / Contact Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}" required />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" />
                        </div>

                        <div class="col-md-8">
                            <label class="form-label" for="website">Company Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $client->website) }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="status">Account Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" {{ old('status', $client->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="address">Corporate Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $client->address) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $client->city) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $client->country) }}" />
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="notes">Internal Account Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Update Profile</button>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="bx bx-trash me-1"></i> Delete Client</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
