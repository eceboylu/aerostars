@extends('layouts.admin')

@section('title', 'Sayfalar')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Sayfalar</h1>
            <p class="text-muted mb-0">Site sayfalarını buradan yönetebilirsiniz.</p>
        </div>

        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Yeni Sayfa
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Başlık</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Durum</th>
                        <th class="text-end">
    İşlemler
</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($pages as $page)

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-semibold">{{ $page->title }}</td>

                            <td class="text-muted">{{ $page->slug }}</td>

                            <td>{{ $page->category?->name ?? '—' }}</td>

                            <td>
                                @if ($page->status)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td class="text-end">

    <a
        href="{{ route('admin.pages.edit', $page->id) }}"
        class="btn btn-sm btn-outline-primary"
    >
        <i class="bi bi-pencil"></i>
    </a>
    <form
    method="POST"
    action="{{ route('admin.pages.destroy', $page->id) }}"
    class="d-inline"
>

    @csrf
    @method('DELETE')

    <button
        type="submit"
        class="btn btn-sm btn-outline-danger"
        onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')"
    >
        <i class="bi bi-trash"></i>
    </button>

</form>

</td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Henüz sayfa eklenmemiş.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
