@extends('layouts.admin')

@section('title', 'Sayfa Düzenle')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Sayfa Düzenle</h1>
            <p class="text-muted mb-0">{{ $page->title }} sayfasını güncelleyin.</p>
        </div>

        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Geri
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.pages.update', $page->id) }}">

                @csrf
                @method('PUT')

                <input type="hidden" name="back_url" value="{{ $backUrl }}">

                @include('admin.pages._fields')

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Güncelle
                    </button>

                    <button
                        type="submit"
                        class="btn btn-outline-primary"
                        formaction="{{ $page->publicUrl() }}?preview=1"
                        formtarget="_blank"
                        formnovalidate
                    >
                        <i class="bi bi-eye"></i>
                        Önizle
                    </button>

                    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
