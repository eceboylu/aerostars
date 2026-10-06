
@extends('layouts.admin')

@section('title', 'Sayfa Düzenle')

@section('content')

    <h1>Sayfa Düzenle</h1>

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('admin.pages.update', $page->id) }}"
    >

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label class="form-label">
                Sayfa Başlığı
            </label>

            <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title', $page->title) }}"
            >

        </div>


        <div class="mb-3">

            <label class="form-label">
                Slug
            </label>

            <input
                type="text"
                name="slug"
                class="form-control"
                value="{{ old('slug', $page->slug) }}"
            >

        </div>


        <div class="mb-3">

            <label class="form-label">
                Kategori
            </label>

            <select
                name="category_id"
                class="form-select"
            >

                <option value="">
                    Kategori Yok
                </option>

                @foreach ($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        {{ old('category_id', $page->category_id) == $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="mb-3">

            <label class="form-label">
                İçerik
            </label>

            <textarea
                name="content"
                class="form-control"
                rows="10"
            >{{ old('content', $page->content) }}</textarea>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Durum
            </label>

            <select
                name="status"
                class="form-select"
            >

                <option
                    value="1"
                    {{ old('status', $page->status) == 1 ? 'selected' : '' }}
                >
                    Aktif
                </option>

                <option
                    value="0"
                    {{ old('status', $page->status) == 0 ? 'selected' : '' }}
                >
                    Pasif
                </option>

            </select>

        </div>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Güncelle
        </button>

    </form>

@endsection
