@php
    $selectedStatus = old('status', isset($menuItem) ? (int) $menuItem->status : 1);
    $selectedLinkType = old('link_type', $menuItem->link_type ?? 'none');
@endphp

<div class="mb-3">
    <label for="parent_id" class="form-label">Üst Öğe</label>
    <select
        id="parent_id"
        name="parent_id"
        class="form-select @error('parent_id') is-invalid @enderror"
    >
        <option value="">Ana Seviye</option>
        @foreach ($parentItems as $parentItem)
            <option
                value="{{ $parentItem->id }}"
                @selected(old('parent_id', $menuItem->parent_id ?? '') == $parentItem->id)
            >
                {{ $parentItem->label }}
            </option>
        @endforeach
    </select>
    @error('parent_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="label" class="form-label">Etiket</label>
    <input
        type="text"
        id="label"
        name="label"
        value="{{ old('label', $menuItem->label ?? '') }}"
        class="form-control @error('label') is-invalid @enderror"
        required
    >
    @error('label')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="link_type" class="form-label">Bağlantı Türü</label>
    <select
        id="link_type"
        name="link_type"
        class="form-select @error('link_type') is-invalid @enderror"
    >
        <option value="none" @selected($selectedLinkType == 'none')>Yok (sadece başlık/dropdown)</option>
        <option value="page" @selected($selectedLinkType == 'page')>Sayfa</option>
        <option value="custom" @selected($selectedLinkType == 'custom')>Özel Adres</option>
    </select>
    @error('link_type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="page_id" class="form-label">Sayfa</label>
    <select
        id="page_id"
        name="page_id"
        class="form-select @error('page_id') is-invalid @enderror"
    >
        <option value="">Seçiniz</option>
        @foreach ($pages as $page)
            <option
                value="{{ $page->id }}"
                @selected(old('page_id', $menuItem->page_id ?? '') == $page->id)
            >
                {{ $page->title }} (/{{ $page->slug }})
            </option>
        @endforeach
    </select>
    <div class="form-text">Yalnızca "Bağlantı Türü: Sayfa" seçiliyse kullanılır.</div>
    @error('page_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="custom_url" class="form-label">Özel Adres</label>
    <input
        type="text"
        id="custom_url"
        name="custom_url"
        value="{{ old('custom_url', $menuItem->custom_url ?? '') }}"
        class="form-control @error('custom_url') is-invalid @enderror"
        placeholder="https://instagram.com/airstars"
    >
    <div class="form-text">Yalnızca "Bağlantı Türü: Özel Adres" seçiliyse kullanılır.</div>
    @error('custom_url')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="sort_order" class="form-label">Sıra</label>
    <input
        type="number"
        id="sort_order"
        name="sort_order"
        value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}"
        class="form-control @error('sort_order') is-invalid @enderror"
    >
    @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3 form-check">
    <input
        type="checkbox"
        id="open_in_new_tab"
        name="open_in_new_tab"
        value="1"
        class="form-check-input"
        @checked(old('open_in_new_tab', $menuItem->open_in_new_tab ?? false))
    >
    <label for="open_in_new_tab" class="form-check-label">Yeni sekmede aç</label>
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
