@php
    $winner = $winner ?? null;
    $selectedCategoryId = old('category_page_id', $winner?->category_page_id ?? '');
    $selectedFamilyId = old('family_id', $winner?->category?->parent_id ?? '');
    $selectedStatus = old('status', $winner ? (int) $winner->status : 1);
    $logo = old('logo', $winner?->logo ?? '');
    $categoryBadge = old('category_badge_image', $winner?->category?->badge_image ?? '');
@endphp

<div class="row g-3 mb-3">

    <div class="col-md-6">
        <label for="family_id" class="form-label">Üst Kategori</label>
        <select id="family_id" class="form-select">
            <option value="">Üst kategori seçin</option>
            @foreach ($families as $family)
                <option value="{{ $family->id }}" @selected((string) $selectedFamilyId === (string) $family->id)>
                    {{ $family->title }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="category_page_id" class="form-label">Alt Kategori</label>
        <select
            id="category_page_id"
            name="category_page_id"
            class="form-select @error('category_page_id') is-invalid @enderror"
            required
        >
            <option value="">Önce üst kategori seçin</option>
            @foreach ($families as $family)
                @foreach ($family->children as $category)
                    <option
                        value="{{ $category->id }}"
                        data-family="{{ $family->id }}"
                        @selected((string) $selectedCategoryId === (string) $category->id)
                    >
                        {{ $category->title }}
                    </option>
                @endforeach
            @endforeach
        </select>
        @error('category_page_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mb-3">
    <label for="company_name" class="form-label">Şirket Adı</label>
    <input
        type="text"
        id="company_name"
        name="company_name"
        value="{{ old('company_name', $winner?->company_name ?? '') }}"
        class="form-control @error('company_name') is-invalid @enderror"
        required
    >
    @error('company_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="verification_slug" class="form-label">Doğrulama Slug</label>
    <input
        type="text"
        id="verification_slug"
        name="verification_slug"
        value="{{ old('verification_slug', $winner?->verification_slug ?? '') }}"
        class="form-control @error('verification_slug') is-invalid @enderror"
        placeholder="Boş bırakılırsa otomatik oluşturulur"
        @if ($winner) required @endif
    >
    <div class="form-text">Doğrulama adresi: /verify/slug. Slug değiştirilirse firmanın sitesine eklediği eski embed linki çalışmaz.</div>
    @error('verification_slug')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Logo</label>
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <img
                id="logo_preview"
                src="{{ $logo ?: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                alt=""
                style="width:48px;height:48px;object-fit:cover;border-radius:4px;background:#e9ecef;"
            >
        </div>
        <div class="col">
            <input
                type="text"
                id="logo"
                name="logo"
                value="{{ $logo }}"
                class="form-control @error('logo') is-invalid @enderror"
                placeholder="Görsel yolu"
            >
        </div>
        <div class="col-auto">
            <button
                type="button"
                class="btn btn-outline-secondary js-pick-image"
                data-input="logo"
                data-preview="logo_preview"
                data-fm-url="{{ route('admin.filemanager.index', ['picker' => 1]) }}"
            >
                Seç
            </button>
        </div>
    </div>
    @error('logo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Kategori Rozeti</label>
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <img
                id="category_badge_image_preview"
                src="{{ $categoryBadge ?: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                alt=""
                style="width:48px;height:48px;object-fit:contain;border-radius:4px;background:#e9ecef;"
            >
        </div>
        <div class="col">
            <input
                type="text"
                id="category_badge_image"
                name="category_badge_image"
                value="{{ $categoryBadge }}"
                class="form-control @error('category_badge_image') is-invalid @enderror"
                placeholder="Görsel yolu"
            >
        </div>
        <div class="col-auto">
            <button
                type="button"
                class="btn btn-outline-secondary js-pick-image"
                data-input="category_badge_image"
                data-preview="category_badge_image_preview"
                data-fm-url="{{ route('admin.filemanager.index', ['picker' => 1]) }}"
            >
                Seç
            </button>
        </div>
    </div>
    <div class="form-text">Seçtiğiniz kategorinin rozet görseli. Burada değiştirirseniz o kategorinin tüm kazananlarında güncellenir.</div>
    @error('category_badge_image')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label for="won_at" class="form-label">Kazandığı Tarih</label>
        <input
            type="date"
            id="won_at"
            name="won_at"
            value="{{ old('won_at', $winner?->won_at?->format('Y-m-d') ?? '') }}"
            class="form-control @error('won_at') is-invalid @enderror"
            required
        >
        @error('won_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Durum</label>
        <select
            id="status"
            name="status"
            class="form-select @error('status') is-invalid @enderror"
        >
            <option value="1" @selected($selectedStatus == 1)>Yayında</option>
            <option value="0" @selected($selectedStatus == 0)>Taslak</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mb-4">
    <label for="description" class="form-label">Açıklama</label>
    <textarea
        id="description"
        name="description"
        rows="4"
        class="form-control @error('description') is-invalid @enderror"
    >{{ old('description', $winner?->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<script>
    (function () {
        var familySelect = document.getElementById('family_id');
        var categorySelect = document.getElementById('category_page_id');

        function filterCategories(keepSelection) {
            var familyId = familySelect.value;
            var hasVisibleSelection = false;

            Array.from(categorySelect.options).forEach(function (option) {
                if (!option.value) {
                    return;
                }

                var matches = option.dataset.family === familyId;
                option.hidden = !matches;

                if (matches && option.selected) {
                    hasVisibleSelection = true;
                }
            });

            if (!keepSelection && !hasVisibleSelection) {
                categorySelect.value = '';
            }
        }

        filterCategories(true);

        familySelect.addEventListener('change', function () {
            filterCategories(false);
        });
    })();
</script>
