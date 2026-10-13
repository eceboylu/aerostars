@extends('layouts.admin')

@section('title', 'Dosya Yöneticisi')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Dosya Yöneticisi</h1>
            <p class="text-muted mb-0">public/uploads klasöründeki görselleri yönetin.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @include('admin.filemanager._browser')
        </div>
    </div>

@endsection
