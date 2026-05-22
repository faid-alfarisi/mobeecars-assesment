@extends('layouts.app')

@section('title', 'Account Setting')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="mb-1">
            Account Setting
        </h3>

        <p class="text-muted mb-0">
            Manage your account
        </p>
    </div>

</div>

@include('sections.flash-message')

<form method="POST" action="{{ route('account.update', $user->id) }}" class="card">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" maxlength="255" required="required" value="{{ old('name', $user->name) }}">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" autocomplete="new-password" maxlength="255" required="required" value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">New Password:</label>
                    <input type="password" class="form-control required-add" name="password" autocomplete="new-password" placeholder="Keep empty if unchanged">
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label class="form-label">Confirm New Password:</label>
                    <input type="password" class="form-control required-add" name="confirm_password" autocomplete="new-password" placeholder="Keep empty if unchanged">
                    @error('confirm_password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-3 text-end">
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Changes</button>
    </div>
</form>
@endsection
