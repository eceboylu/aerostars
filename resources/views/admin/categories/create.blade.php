@extends('layouts.admin')

@section('title', 'Yeni Kategori')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Yeni Kategori Ekle</h1>
            <p class="text-muted mb-0">Yeni bir kategori oluşturun.</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.categories.store') }}">

                @csrf

                @include('admin.categories._fields')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
