@extends('layouts.admin')

@section('title', 'Yeni Bölüm')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Yeni Bölüm</h1>
            <p class="text-muted mb-0">Anasayfaya, tamamen kendi yazdığınız HTML ile yeni bir bölüm ekleyin.</p>
        </div>

        <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.home-sections.store') }}">

                @csrf

                <div class="mb-3">
                    <label for="label" class="form-label">Bölüm Adı</label>
                    <input
                        type="text"
                        id="label"
                        name="label"
                        value="{{ old('label') }}"
                        class="form-control @error('label') is-invalid @enderror"
                        placeholder="örn. Sponsorlar"
                        required
                    >
                    <div class="form-text">Bu isim sadece admin panelinde görünür, anasayfada görünmez.</div>
                    @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="page-html-editor" class="form-label">HTML İçerik</label>
                    <textarea
                        id="page-html-editor"
                        name="custom_html"
                        class="form-control @error('custom_html') is-invalid @enderror"
                        data-start-mode="source"
                    >{{ old('custom_html') }}</textarea>
                    <div class="form-text">Bu bölüm, anasayfada burada yazdığınız haliyle yayınlanır. Mevcut <code>.section</code> ve <code>.wrap</code> gibi class'ları kullanabilirsiniz.</div>
                    @error('custom_html')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="custom_css" class="form-label">Özel CSS</label>
                    <textarea
                        id="custom_css"
                        name="custom_css"
                        rows="10"
                        class="form-control font-monospace admin-code-field @error('custom_css') is-invalid @enderror"
                        placeholder=".my-section { ... }"
                    >{{ old('custom_css') }}</textarea>
                    <div class="form-text">Sadece CSS, <code>&lt;style&gt;</code> etiketi eklemeyin. Yalnızca bu bölüme uygulanır.</div>
                    @error('custom_css')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="status" class="form-label">Durum</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="1" @selected(old('status', '1') == '1')>Aktif</option>
                        <option value="0" @selected(old('status') === '0')>Pasif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
