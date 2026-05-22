@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-4">
        <div>
            <h3 class="mb-1">
                Reports
            </h3>

            <p class="text-muted mb-0">
                Show the most liked car
            </p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <small class="text-secondary opacity-75">Brand</small>
                    <div class="fw-medium">{{ $data['favorite_brand'] ?? 'No Data'}}</div>
                </li>
                <li class="list-group-item">
                    <small class="text-secondary opacity-75">Model</small>
                    <div class="fw-medium">{{ $data['favorite_model'] ?? 'No Data'}}</div>
                </li>
                <li class="list-group-item">
                    <small class="text-secondary opacity-75">Type/Category</small>
                    <div class="fw-medium">{{ $data['favorite_type'] ?? 'No Data'}}</div>
                </li>
            </ul>
        </div>
    </div>

@endsection
