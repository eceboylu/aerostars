@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-4">
        <h1 class="h3 mb-1">Hoş geldiniz</h1>
        <p class="text-muted mb-0">Sitenin genel durumuna hızlı bir bakış.</p>
    </div>

    @if ($overrideEnabled)
        <div class="alert alert-danger d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Tam Sayfa Düzenleyici şu anda aktif.</strong>
                Anasayfa, normal bölümler yerine orada yazılan özel HTML ile gösteriliyor.
            </div>
            <a href="{{ route('admin.homepage-override.edit') }}" class="btn btn-sm btn-outline-danger text-nowrap">
                Kontrol et
            </a>
        </div>
    @endif

    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Sayfalar</div>
                    <div class="h3 mb-0">{{ $pageTotal }}</div>
                    <div class="text-muted small">{{ $pageActive }} aktif</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Kategoriler</div>
                    <div class="h3 mb-0">{{ $categoryCount }}</div>
                    <div class="text-muted small">{{ $familyCount }} aile altında</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Anasayfa Bölümleri</div>
                    <div class="h3 mb-0">{{ $sectionTotal }}</div>
                    <div class="text-muted small">
                        {{ $sectionActive }} aktif
                        @if ($sectionDrafts > 0)
                            &middot; {{ $sectionDrafts }} yayınlanmamış taslak
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Menüler</div>
                    <div class="h3 mb-0">{{ $headerMenuCount + $footerMenuCount }}</div>
                    <div class="text-muted small">{{ $headerMenuCount }} header &middot; {{ $footerMenuCount }} footer</div>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Hızlı işlemler</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Yeni Sayfa
                </a>
                <a href="{{ route('admin.home-sections.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-lg"></i>
                    Yeni Anasayfa Bölümü
                </a>
                <a href="{{ route('home', ['preview' => 1]) }}" target="_blank" class="btn btn-outline-secondary">
                    Anasayfayı Görüntüle
                </a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                    Sayfaları Listele
                </a>
                <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
                    Anasayfa Bölümlerini Listele
                </a>
            </div>
        </div>
    </div>

@endsection
