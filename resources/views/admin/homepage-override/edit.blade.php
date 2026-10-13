@extends('layouts.admin')

@section('title', ' Sayfa Düzenleyici')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1"> Sayfa Düzenleyici</h1>
        </div>

        <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Anasayfa Bölümlerine Dön
        </a>

    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Etkinleştirildiğinde, anasayfanın tamamı aşağıdaki HTML/CSS/JS ile değişir — <strong>Anasayfa Bölümleri</strong>'nde oluşturulan her bölüm (Award Families ve FAQ gibi otomatik olanlar dahil) bu açıkken yok sayılır. Gerçekten tek seferlik bir tam override gerekmedikçe kapalı bırakın.
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.homepage-override.update') }}">

                @csrf
                @method('PUT')

                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="enabled" value="0">
                    <input
                        type="checkbox"
                        id="enabled"
                        name="enabled"
                        value="1"
                        class="form-check-input"
                        role="switch"
                        @checked(old('enabled', $enabled))
                    >
                    <label for="enabled" class="form-check-label fw-semibold">Bu override'ı canlı anasayfada etkinleştir</label>
                    <div class="form-text">Varsayılan olarak kapalı. Bu HTML'in normal anasayfanın yerine her ziyaretçi için geçmesini istediğinizde açın.</div>
                </div>

                <div class="mb-3">
                    <label for="page-html-editor" class="form-label">HTML</label>
                    <textarea
                        id="page-html-editor"
                        name="html"
                        class="form-control @error('html') is-invalid @enderror"
                        data-start-mode="source"
                    >{{ old('html', $html) }}</textarea>
                    @error('html')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="css" class="form-label">CSS</label>
                    <textarea
                        id="css"
                        name="css"
                        rows="10"
                        class="form-control font-monospace admin-code-field @error('css') is-invalid @enderror"
                        placeholder=".my-section { ... }"
                    >{{ old('css', $css) }}</textarea>
                    <div class="form-text">Sadece CSS, <code>&lt;style&gt;</code> etiketi eklemeyin.</div>
                    @error('css')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="js" class="form-label">JavaScript</label>
                    <textarea
                        id="js"
                        name="js"
                        rows="10"
                        class="form-control font-monospace admin-code-field @error('js') is-invalid @enderror"
                        placeholder="// düz JS, &lt;script&gt; etiketi eklemeyin"
                    >{{ old('js', $js) }}</textarea>
                    <div class="form-text">Sadece JS, <code>&lt;script&gt;</code> etiketi eklemeyin. Bu admin panelindeki diğer tüm içerik alanlarının aksine, burası hiçbir temizlemeden (sanitisation) geçmeden olduğu gibi çalışır — sadece siz yazabilirsiniz, dikkatli olun.</div>
                    @error('js')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <button
                        type="submit"
                        class="btn btn-outline-primary"
                        formaction="{{ route('admin.homepage-override.preview') }}"
                        formtarget="_blank"
                        formnovalidate
                    >
                        <i class="bi bi-eye"></i>
                        Önizle
                    </button>

                    <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
