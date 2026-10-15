@extends('layouts.admin')

@section('title', 'Footer Menu')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Footer Menu</h1>
            <p class="text-muted mb-0">Footer menü öğelerini buradan yönetebilirsiniz.</p>
        </div>

        <a href="{{ route('admin.footer-menu.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Yeni Menü Öğesi
        </a>

    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-1">Footer Bilgileri</h2>
            <p class="text-muted small mb-3">Footer'da logonun altındaki açıklama ve en alttaki satır. Boş bırakılan alan sitede gösterilmez.</p>

            <form method="POST" action="{{ route('admin.footer-menu.settings') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="footer_description" class="form-label">Açıklama (logonun altı)</label>
                    <textarea
                        id="footer_description"
                        name="footer_description"
                        rows="2"
                        maxlength="500"
                        class="form-control @error('footer_description') is-invalid @enderror"
                    >{{ old('footer_description', $settings['footer_description']) }}</textarea>
                    @error('footer_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label for="footer_copyright" class="form-label">Telif satırı</label>
                        <input
                            type="text"
                            id="footer_copyright"
                            name="footer_copyright"
                            value="{{ old('footer_copyright', $settings['footer_copyright']) }}"
                            class="form-control @error('footer_copyright') is-invalid @enderror"
                            placeholder="© 2027 Flyoneers. All rights reserved."
                        >
                        @error('footer_copyright')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label for="footer_email" class="form-label">E-posta</label>
                        <input
                            type="email"
                            id="footer_email"
                            name="footer_email"
                            value="{{ old('footer_email', $settings['footer_email']) }}"
                            class="form-control @error('footer_email') is-invalid @enderror"
                        >
                        @error('footer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    Kaydet
                </button>
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
                                    href="{{ route('admin.footer-menu.edit', $menuItem->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.footer-menu.destroy', $menuItem->id) }}"
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
                                        href="{{ route('admin.footer-menu.edit', $child->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Düzenle"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form
                                        method="POST"
                                        action="{{ route('admin.footer-menu.destroy', $child->id) }}"
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
