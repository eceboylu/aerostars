{{-- Yeni sayfa formu. Adres: /admin/pages/create --}}
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

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select
                        id="category_id"
                        name="category_id"
                        class="form-select @error('category_id') is-invalid @enderror"
                    >
                        <option value="">Kategori yok</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('category_id') == $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Başlık</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        class="form-control @error('title') is-invalid @enderror"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug') }}"
                        class="form-control @error('slug') is-invalid @enderror"
                        required
                    >
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">İçerik</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="8"
                        class="form-control @error('content') is-invalid @enderror"
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label">Durum</label>
                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                    >
                        <option value="1" @selected(old('status', '1') == '1')>Aktif</option>
                        <option value="0" @selected(old('status') === '0')>Pasif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <a href="{{ route('admin.pages.index') }}" class="btn btn-light">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
