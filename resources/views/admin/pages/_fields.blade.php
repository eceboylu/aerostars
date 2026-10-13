@php
    $selectedParentId = old('parent_id', $page->parent_id ?? ($presetParentId ?? ''));
    $selectedStatus = old('status', isset($page) ? (int) $page->status : 1);
    $selectedRobotsIndex = old('robots_index', isset($page) ? (int) $page->robots_index : 1);
    $lockedParent = ! isset($page) && ! empty($presetParentId) ? \App\Models\Page::find($presetParentId) : null;

    $contentFields = ['parent_id', 'title', 'slug', 'status'];
    $designFields = ['custom_html', 'custom_css'];
    $seoFields = [
        'meta_title', 'meta_description', 'meta_keywords',
        'canonical_url', 'robots_index',
    ];

    $activeTab = 'content';
    foreach (['content' => $contentFields, 'design' => $designFields, 'seo' => $seoFields] as $tab => $fields) {
        if ($errors->hasAny($fields)) {
            $activeTab = $tab;
            break;
        }
    }
@endphp

<ul class="nav nav-tabs mb-3" id="pageFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button
            class="nav-link @if ($activeTab === 'content') active @endif"
            id="page-tab-content-btn"
            data-bs-toggle="tab"
            data-bs-target="#page-tab-content"
            type="button"
            role="tab"
            aria-controls="page-tab-content"
            aria-selected="{{ $activeTab === 'content' ? 'true' : 'false' }}"
        >
            Giriş
            @if ($errors->hasAny($contentFields))
                <i class="bi bi-exclamation-circle-fill text-danger" aria-hidden="true"></i>
                <span class="visually-hidden">(bu sekmede doğrulama hatası var)</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button
            class="nav-link @if ($activeTab === 'design') active @endif"
            id="page-tab-design-btn"
            data-bs-toggle="tab"
            data-bs-target="#page-tab-design"
            type="button"
            role="tab"
            aria-controls="page-tab-design"
            aria-selected="{{ $activeTab === 'design' ? 'true' : 'false' }}"
        >
            Tasarım
            @if ($errors->hasAny($designFields))
                <i class="bi bi-exclamation-circle-fill text-danger" aria-hidden="true"></i>
                <span class="visually-hidden">(bu sekmede doğrulama hatası var)</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button
            class="nav-link @if ($activeTab === 'seo') active @endif"
            id="page-tab-seo-btn"
            data-bs-toggle="tab"
            data-bs-target="#page-tab-seo"
            type="button"
            role="tab"
            aria-controls="page-tab-seo"
            aria-selected="{{ $activeTab === 'seo' ? 'true' : 'false' }}"
        >
            SEO
            @if ($errors->hasAny($seoFields))
                <i class="bi bi-exclamation-circle-fill text-danger" aria-hidden="true"></i>
                <span class="visually-hidden">(bu sekmede doğrulama hatası var)</span>
            @endif
        </button>
    </li>
</ul>

<div class="tab-content" id="pageFormTabsContent">

    <div
        class="tab-pane fade @if ($activeTab === 'content') show active @endif"
        id="page-tab-content"
        role="tabpanel"
        aria-labelledby="page-tab-content-btn"
        tabindex="0"
    >

        <div class="mb-3">
            <label class="form-label">Üst Sayfa</label>

            @if ($lockedParent)
                <input type="text" class="form-control" value="{{ $lockedParent->title }}" disabled>
                <input type="hidden" name="parent_id" value="{{ $lockedParent->id }}">
            @else
                <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                    <option value="">Ana Seviye</option>
                    @foreach ($pages as $parentPage)
                        <option value="{{ $parentPage->id }}" @selected($selectedParentId == $parentPage->id)>
                            {{ $parentPage->title }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">Bu sayfanın menüde/breadcrumb'ta hangi sayfanın altında göründüğünü belirler. "Categories" sayfasının altındaki sayfalar otomatik olarak aile (family), onların altındakiler ise ödül kategorisi olarak genel sitede listelenir.</div>
            @endif

            @error('parent_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Başlık</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $page->title ?? '') }}"
                class="form-control @error('title') is-invalid @enderror"
                required
            >
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $page->slug ?? '') }}"
                class="form-control @error('slug') is-invalid @enderror"
                required
            >
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori Rozeti</label>
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <img
                        id="badge_image_preview"
                        src="{{ old('badge_image', $page->badge_image ?? '') ?: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                        alt=""
                        style="width:48px;height:48px;object-fit:contain;border-radius:4px;background:#e9ecef;"
                    >
                </div>
                <div class="col">
                    <input
                        type="text"
                        id="badge_image"
                        name="badge_image"
                        value="{{ old('badge_image', $page->badge_image ?? '') }}"
                        class="form-control @error('badge_image') is-invalid @enderror"
                        placeholder="Görsel yolu"
                    >
                </div>
                <div class="col-auto">
                    <button
                        type="button"
                        class="btn btn-outline-secondary js-pick-image"
                        data-input="badge_image"
                        data-preview="badge_image_preview"
                        data-fm-url="{{ route('admin.filemanager.index', ['picker' => 1]) }}"
                    >
                        Seç
                    </button>
                </div>
            </div>
            <div class="form-text">Bu kategori bir ödül kategorisiyse, kazanan kartında görünen rozet/widget görseli. Diğer sayfalarda kullanılmaz.</div>
            @error('badge_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="status" class="form-label">Durum</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="1" @selected($selectedStatus == 1)>Aktif</option>
                <option value="0" @selected($selectedStatus == 0)>Pasif</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div
        class="tab-pane fade @if ($activeTab === 'design') show active @endif"
        id="page-tab-design"
        role="tabpanel"
        aria-labelledby="page-tab-design-btn"
        tabindex="0"
    >

        <div class="mb-3">
            <label for="page-html-editor" class="form-label">Sayfa İçeriği</label>
            <textarea
                id="page-html-editor"
                name="custom_html"
                class="form-control @error('custom_html') is-invalid @enderror"
            >{{ old('custom_html', $page->custom_html ?? '') }}</textarea>
            <div class="form-text">Sayfanın ana içerik alanı. Sitede yayınlanacak HTML burada düzenlenir.</div>
            @error('custom_html')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label for="custom_css" class="form-label">Sayfaya Özel CSS</label>
            <textarea
                id="custom_css"
                name="custom_css"
                rows="12"
                class="form-control font-monospace admin-code-field @error('custom_css') is-invalid @enderror"
                placeholder=".hover-lift { transition: transform .2s ease; }"
            >{{ old('custom_css', $page->custom_css ?? '') }}</textarea>
            <div class="form-text">Yalnızca CSS kodu girin, &lt;style&gt; etiketi eklemeyin. Yalnızca bu sayfanın içerik alanına uygulanır. Alan belirli bir yüksekliği aşınca kendi içinde kaydırılır (scroll).</div>
            @error('custom_css')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div
        class="tab-pane fade @if ($activeTab === 'seo') show active @endif"
        id="page-tab-seo"
        role="tabpanel"
        aria-labelledby="page-tab-seo-btn"
        tabindex="0"
    >

        <fieldset class="admin-fieldset">
            <legend>Search</legend>
            <p class="text-muted small">Google gibi arama motorlarında bu sayfa için gösterilecek bilgiler.</p>

            <div class="mb-3">
                <label for="meta_title" class="form-label">Meta Title</label>
                <input
                    type="text"
                    id="meta_title"
                    name="meta_title"
                    value="{{ old('meta_title', $page->meta_title ?? '') }}"
                    class="form-control @error('meta_title') is-invalid @enderror"
                >
                <div class="form-text">Arama sonuçlarında ve tarayıcı sekmesinde görünen başlık. Boş bırakılırsa Başlık kullanılır.</div>
                @error('meta_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="meta_description" class="form-label">Meta Description</label>
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="3"
                    class="form-control @error('meta_description') is-invalid @enderror"
                >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                <div class="form-text">Arama sonuçlarında başlığın altında görünen kısa açıklama (yaklaşık 1-2 cümle).</div>
                @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-0">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <textarea
                    id="meta_keywords"
                    name="meta_keywords"
                    rows="2"
                    class="form-control @error('meta_keywords') is-invalid @enderror"
                >{{ old('meta_keywords', $page->meta_keywords ?? '') }}</textarea>
                <div class="form-text">Virgülle ayrılmış anahtar kelimeler. Günümüz arama motorlarında sıralamaya etkisi azdır, bilgi amaçlı tutulur.</div>
                @error('meta_keywords')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </fieldset>

        <fieldset class="admin-fieldset mb-0">
            <legend>Indexing</legend>
            <p class="text-muted small">Sayfanın hangi adresten ve arama motorlarınca nasıl taranacağı.</p>

            <div class="mb-3">
                <label for="canonical_url" class="form-label">Canonical URL</label>
                <input
                    type="url"
                    id="canonical_url"
                    name="canonical_url"
                    value="{{ old('canonical_url', $page->canonical_url ?? '') }}"
                    class="form-control @error('canonical_url') is-invalid @enderror"
                >
                <div class="form-text">Bu içeriğin "asıl" adresi. Aynı içerik birden fazla URL'de erişilebilirse arama motorlarına hangisinin esas alınacağını söyler. Boş bırakılırsa sistem otomatik URL üretir.</div>
                @error('canonical_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-0">
                <label for="robots_index" class="form-label">Robots</label>
                <select id="robots_index" name="robots_index" class="form-select @error('robots_index') is-invalid @enderror">
                    <option value="1" @selected($selectedRobotsIndex == 1)>Index (aranabilir)</option>
                    <option value="0" @selected($selectedRobotsIndex == 0)>Noindex (aranamaz)</option>
                </select>
                <div class="form-text">"Noindex" seçilirse arama motorları bu sayfayı sonuçlarında göstermez (örn. teşekkür sayfası gibi).</div>
                @error('robots_index')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </fieldset>

    </div>

</div>
