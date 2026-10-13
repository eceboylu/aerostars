@extends('layouts.admin')

@section('title', 'Yeni Sayfa')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Yeni Sayfa Ekle</h1>
            <p class="text-muted mb-0">Yeni bir sayfa oluşturun.</p>
        </div>

        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.pages.store') }}">

                @csrf

                @include('admin.pages._fields')

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <button
                        type="submit"
                        class="btn btn-outline-primary"
                        formaction="{{ route('admin.pages.preview') }}"
                        formtarget="_blank"
                        formnovalidate
                    >
                        <i class="bi bi-eye"></i>
                        Önizle
                    </button>

                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
