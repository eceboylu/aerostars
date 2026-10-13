@php
    $selectedStatus = old('status', isset($category) ? (int) $category->status : 1);
@endphp

<div class="mb-3">
    <label for="parent_id" class="form-label">Üst Kategori</label>
    <select
        id="parent_id"
        name="parent_id"
        class="form-select @error('parent_id') is-invalid @enderror"
    >
        <option value="">Ana Seviye</option>
        @foreach ($categories as $parentCategory)
            <option
                value="{{ $parentCategory->id }}"
                @selected(old('parent_id', $category->parent_id ?? '') == $parentCategory->id)
            >
                {{ $parentCategory->name }}
            </option>
        @endforeach
    </select>
    @error('parent_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="name" class="form-label">Kategori Adı</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $category->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        required
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="slug" class="form-label">Slug</label>
    <input
        type="text"
        id="slug"
        name="slug"
        value="{{ old('slug', $category->slug ?? '') }}"
        class="form-control @error('slug') is-invalid @enderror"
        required
    >
    @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="status" class="form-label">Durum</label>
    <select
        id="status"
        name="status"
        class="form-select @error('status') is-invalid @enderror"
    >
        <option value="1" @selected($selectedStatus == 1)>Aktif</option>
        <option value="0" @selected($selectedStatus == 0)>Pasif</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
