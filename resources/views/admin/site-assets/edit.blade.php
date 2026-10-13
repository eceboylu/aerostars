@extends('layouts.admin')

@section('title', 'Ayarlar')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Ayarlar</h1>
            <p class="text-muted mb-0">Sitenin CSS ve JavaScript dosyalarını buradan görüp düzenleyebilirsiniz.</p>
        </div>

        @if ($hasUnpublishedChanges)
            <span class="badge text-bg-warning">Yayınlanmamış taslak var</span>
        @endif

    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Bu, sitenin <strong>tüm sayfalarında</strong> kullanılan ana CSS/JS dosyalarıdır. "Kaydet" sadece taslağı kaydeder, hiçbir şeyi etkilemez. "Yayınla" dediğinizde dosyalar güncellenir, site yeniden derlenir ve değişiklik <strong>anında tüm ziyaretçiler için</strong> geçerli olur.
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @error('build')
        <div class="alert alert-danger">
            <i class="bi bi-x-circle"></i>
            {{ $message }}
        </div>
    @enderror

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.site-assets.update') }}">

                @csrf

                <div class="mb-4">
                    <label for="css" class="form-label">site.css</label>
                    <textarea
                        id="css"
                        name="css"
                        rows="22"
                        class="form-control font-monospace admin-code-field @error('css') is-invalid @enderror"
                        style="max-height: none;"
                    >{{ old('css', $css) }}</textarea>
                    @error('css')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-0">
                    <label for="js" class="form-label">site.js</label>
                    <textarea
                        id="js"
                        name="js"
                        rows="14"
                        class="form-control font-monospace admin-code-field @error('js') is-invalid @enderror"
                        style="max-height: none;"
                    >{{ old('js', $js) }}</textarea>
                    @error('js')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet (taslak)
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        formaction="{{ route('admin.site-assets.publish') }}"
                        onclick="return confirm('Bu değişiklik anında tüm ziyaretçiler için geçerli olacak. Yayınlamak istediğinize emin misiniz?')"
                    >
                        <i class="bi bi-cloud-upload"></i>
                        Yayınla
                    </button>

                    <button
                        type="submit"
                        class="btn btn-outline-warning"
                        formaction="{{ route('admin.site-assets.discard') }}"
                        formnovalidate
                        onclick="return confirm('Kaydedilmemiş taslak değişiklikleriniz silinip yayındaki hal geri yüklenecek. Emin misiniz?')"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Taslağı Sıfırla
                    </button>
                </div>

            </form>

        </div>

    </div>

@endsection
