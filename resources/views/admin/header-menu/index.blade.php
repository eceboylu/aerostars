@extends('layouts.admin')

@section('title', 'Header Menu')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Header Menu</h1>
            <p class="text-muted mb-0">Navbar menü öğelerini buradan yönetebilirsiniz.</p>
        </div>

        <a href="{{ route('admin.header-menu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Yeni Menü Öğesi
        </a>

    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-1">Header Ayarları</h2>
            <p class="text-muted small mb-3">Site logosu ve menünün sağındaki altın buton. Buton yazısı ile bağlantıyı boş bırakırsanız buton sitede gösterilmez.</p>

            <form method="POST" action="{{ route('admin.header-menu.settings') }}">
                @csrf
                @method('PUT')

                @php $emptyImage = 'data:image/gif;base64,R0lGODlhAQABAAAAACw='; @endphp
                <div class="mb-4">
                    <label for="site_logo" class="form-label">Logo</label>
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <img
                                id="site_logo_preview"
                                src="{{ old('site_logo', $settings['site_logo']) ?: $emptyImage }}"
                                data-placeholder="{{ $emptyImage }}"
                                alt="Logo önizlemesi"
                                style="height:48px;width:140px;object-fit:contain;border-radius:6px;background:#0B0D10;padding:6px;"
                            >
                        </div>
                        <div class="col">
                            <input
                                type="text"
                                id="site_logo"
                                name="site_logo"
                                value="{{ old('site_logo', $settings['site_logo']) }}"
                                class="form-control @error('site_logo') is-invalid @enderror"
                                placeholder="Dosya yöneticisinden seçin"
                            >
                        </div>
                        <div class="col-auto d-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-secondary js-pick-image"
                                data-input="site_logo"
                                data-preview="site_logo_preview"
                                data-fm-url="{{ route('admin.filemanager.index', ['picker' => 1]) }}"
                            >
                                Seç / Yükle
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-danger js-clear-image"
                                data-input="site_logo"
                                data-preview="site_logo_preview"
                            >
                                Kaldır
                            </button>
                        </div>
                    </div>
                    <div class="form-text">Header, footer ve admin panelinde görünür. Boş bırakılırsa sitede "flyoneers" yazısı gösterilir.</div>
                    @error('site_logo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 align-items-start">
                    <div class="col-md-5">
                        <label for="header_cta_label" class="form-label">Buton yazısı</label>
                        <input
                            type="text"
                            id="header_cta_label"
                            name="header_cta_label"
                            value="{{ old('header_cta_label', $settings['header_cta_label']) }}"
                            class="form-control @error('header_cta_label') is-invalid @enderror"
                            maxlength="60"
                        >
                        @error('header_cta_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label for="header_cta_url" class="form-label">Bağlantı</label>
                        <input
                            type="text"
                            id="header_cta_url"
                            name="header_cta_url"
                            value="{{ old('header_cta_url', $settings['header_cta_url']) }}"
                            class="form-control @error('header_cta_url') is-invalid @enderror"
                            placeholder="/enter/ veya https://..."
                        >
                        @error('header_cta_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 d-grid">
                        <label class="form-label invisible" aria-hidden="true">Kaydet</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Etiket</th>
                        <th scope="col">Bağlantı</th>
                        <th scope="col">Sıra</th>
                        <th scope="col">Durum</th>
                        <th scope="col" class="text-end">İşlemler</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($menuItems as $menuItem)

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-semibold">{{ $menuItem->label }}</td>

                            <td class="text-muted">{{ $menuItem->url() ?? '—' }}</td>

                            <td>{{ $menuItem->sort_order }}</td>

                            <td>
                                @if ($menuItem->status)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Pasif</span>
                                @endif
                            </td>

                            <td class="text-end text-nowrap">

                                <a
                                    href="{{ route('admin.header-menu.edit', $menuItem->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.header-menu.destroy', $menuItem->id) }}"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Sil"
                                        onclick="return confirm('Bu menü öğesini silmek istediğinize emin misiniz?')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                        @foreach ($menuItem->children as $child)
                            <tr>
                                <td></td>
                                <td class="ps-4 text-muted">↳ {{ $child->label }}</td>
                                <td class="text-muted">{{ $child->url() ?? '—' }}</td>
                                <td>{{ $child->sort_order }}</td>
                                <td>
                                    @if ($child->status)
                                        <span class="badge text-bg-success">Aktif</span>
                                    @else
                                        <span class="badge text-bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a
                                        href="{{ route('admin.header-menu.edit', $child->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Düzenle"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.header-menu.destroy', $child->id) }}"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Sil"
                                            onclick="return confirm('Bu menü öğesini silmek istediğinize emin misiniz?')"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    @empty

                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Henüz menü öğesi eklenmemiş.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
