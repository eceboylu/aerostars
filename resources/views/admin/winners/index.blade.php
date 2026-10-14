@extends('layouts.admin')

@section('title', 'Kazananlar')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Kazananlar</h1>
            <p class="text-muted mb-0">Kategori bazında kazanan şirketleri buradan yönetebilirsiniz.</p>
        </div>

        <a href="{{ route('admin.winners.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Yeni Kazanan
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th scope="col">Logo</th>
                        <th scope="col">Şirket</th>
                        <th scope="col">Üst Kategori</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Tarih</th>
                        <th scope="col">Durum</th>
                        <th scope="col" class="text-end">İşlemler</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($winners as $winner)

                        <tr>
                            <td>
                                @if ($winner->logo)
                                    <img src="{{ $winner->logo }}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:4px;">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td class="fw-semibold">{{ $winner->company_name }}</td>

                            <td class="text-muted">{{ $winner->category?->parent?->title ?? '—' }}</td>

                            <td class="text-muted">{{ $winner->category?->title ?? '—' }}</td>

                            <td class="text-muted">{{ $winner->won_at?->format('d.m.Y') ?? '—' }}</td>

                            <td>
                                @if ($winner->status)
                                    <span class="badge text-bg-success">Yayında</span>
                                @else
                                    <span class="badge text-bg-secondary">Taslak</span>
                                @endif
                            </td>

                            <td class="text-end text-nowrap">

                                @if ($winner->verification_slug)
                                    <a
                                        href="{{ route('site.verify', $winner->verification_slug) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Doğrulama sayfasını aç"
                                    >
                                        <i class="bi bi-patch-check"></i>
                                    </a>
                                @endif

                                <a
                                    href="{{ route('admin.winners.edit', $winner) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.winners.destroy', $winner) }}"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Sil"
                                        onclick="return confirm('Bu kazananı silmek istediğinize emin misiniz?')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Henüz kazanan eklenmemiş.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
