@extends('layouts.admin')

@section('title', 'Anasayfa Bölümleri')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Anasayfa Bölümleri</h1>
            <p class="text-muted mb-0">Anasayfa bölümlerini yeniden sıralayın, pasife alın, silin veya kendi HTML'inizle değiştirin.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('home', ['preview' => 1]) }}" target="_blank" class="btn btn-outline-secondary">
                Anasayfayı Görüntüle
            </a>
            <a href="{{ route('admin.home-sections.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Yeni Bölüm
            </a>
        </div>

    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th scope="col" style="width:90px">Sıra</th>
                        <th scope="col">Bölüm</th>
                        <th scope="col">Mod</th>
                        <th scope="col">Durum</th>
                        <th scope="col" class="text-end">İşlemler</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($sections as $section)

                        <tr>
                            <td class="text-nowrap">
                                <form method="POST" action="{{ route('admin.home-sections.move-up', $section->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Yukarı taşı" @if ($loop->first) disabled @endif>
                                        <i class="bi bi-arrow-up"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.home-sections.move-down', $section->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Aşağı taşı" @if ($loop->last) disabled @endif>
                                        <i class="bi bi-arrow-down"></i>
                                    </button>
                                </form>
                            </td>

                            <td class="fw-semibold">{{ $section->label }}</td>

                            <td>
                                @if ($section->isFullyDynamic())
                                    <span class="badge text-bg-primary" title="Aktif kategori sayfalarından otomatik oluşturulur">Otomatik</span>
                                @elseif ($section->isCustom())
                                    <span class="badge text-bg-info">Özel HTML</span>
                                @else
                                    <span class="badge text-bg-light text-dark border">Varsayılan</span>
                                @endif
                                @if ($section->hasUnpublishedChanges())
                                    <span class="badge text-bg-warning" title="Kaydedildi ama henüz yayınlanmadı">Taslak</span>
                                @endif
                            </td>

                            <td>
                                @if ($section->status)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Pasif</span>
                                @endif
                            </td>

                            <td class="text-end text-nowrap">
                                <a
                                    href="{{ route('admin.home-sections.edit', $section->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @if ($section->hasUnpublishedChanges())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.home-sections.publish', $section->id) }}"
                                        class="d-inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-success"
                                            title="Yayınla"
                                        >
                                            <i class="bi bi-cloud-upload"></i>
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.home-sections.revert', $section->id) }}"
                                        class="d-inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Yayındaki haline geri al"
                                            onclick="return confirm('Taslak silinip yayındaki hale geri dönülecek. Emin misiniz?')"
                                        >
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ route('admin.home-sections.destroy', $section->id) }}"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Sil"
                                        onclick="return confirm('Bu bölüm anasayfadan kaldırılsın mı?')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Henüz bölüm yok.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <p class="text-muted small mt-3 mb-0">
        Tüm anasayfayı kendi özel HTML/CSS/JS'inizle değiştirmeniz mi gerekiyor?
        <a href="{{ route('admin.homepage-override.edit') }}">Tam Sayfa Düzenleyici (gelişmiş)</a>
    </p>

@endsection
