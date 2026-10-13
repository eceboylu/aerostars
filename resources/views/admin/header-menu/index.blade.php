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
