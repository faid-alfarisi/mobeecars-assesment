@if ($message = Session::get('success'))
    <div class="alert alert-success d-flex align-items-center px-5 py-3">
        <i class="fas fa-check-circle text-success me-4"><span class="path1"></span><span
                class="path2"></span></i>
        <div class="d-flex flex-column">
            <h5 class="mb-1 text-success">{{ $message }}</h5>
        </div>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger d-flex align-items-center px-5 py-3">
        <i class="fas fa-times-circle text-danger me-4"><span class="path1"></span><span
                class="path2"></span></i>
        <div class="d-flex flex-column">
            <h5 class="mb-1 text-danger">{{ $message }}</h5>
        </div>
    </div>
@endif
