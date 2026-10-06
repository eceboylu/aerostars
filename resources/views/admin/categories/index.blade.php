@extends('layouts.admin')

@section('title', 'Kategoriler')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Kategoriler</h1>
            <p class="text-muted mb-0">Site kategorilerini buradan yönetebilirsiniz.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Yeni Kategori
        </a>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Ad</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Durum</th>
                        <th scope="col" class="text-end">İşlemler</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($categories as $category)

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-semibold">{{ $category->name }}</td>

                            <td class="text-muted">{{ $category->slug }}</td>

                            <td>
                                @if ($category->status)
                                    <span class="badge text-bg-success">Aktif</span>
                                @else
                                    <span class="badge text-bg-secondary">Pasif</span>
                                @endif
                            </td>

                            <td class="text-end text-nowrap">

                                <a
                                    href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="Düzenle"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('admin.categories.destroy', $category->id) }}"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Sil"
                                        onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Henüz kategori eklenmemiş.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection
