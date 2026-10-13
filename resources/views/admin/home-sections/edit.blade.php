@extends('layouts.admin')

@section('title', 'Bölüm Düzenle')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">{{ $section->label }}</h1>
            <p class="text-muted mb-0">
                @if ($section->isFullyDynamic())
                    Bu bölümün içeriği otomatik oluşturulur ve HTML olarak düzenlenemez.
                @elseif ($section->key)
                    Bu, sitenin varsayılan tasarımından gelen bir bölümdür. Olduğu gibi bırakabilir veya kendi HTML'inizle değiştirebilirsiniz.
                @else
                    Bu, sizin eklediğiniz özel bir bölümdür.
                @endif
            </p>
        </div>

        <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    @unless ($section->isFullyDynamic())
        <div class="alert alert-info">
            Buradaki kayıt sadece taslağı günceller. Bölüm listesinden yayınlamadan siteye hiçbir şey yansımaz — önce taslağı görmek için oradaki "Anasayfayı Görüntüle" linkini kullanın.
        </div>
    @endunless

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.home-sections.update', $section->id) }}">

                @csrf
                @method('PUT')
                <input type="hidden" name="key" value="{{ $section->key }}">

                <div class="mb-3">
                    <label for="label" class="form-label">Bölüm Adı</label>
                    <input
                        type="text"
                        id="label"
                        name="label"
                        value="{{ old('label', $section->label) }}"
                        class="form-control @error('label') is-invalid @enderror"
                        required
                    >
                    <div class="form-text">Bu isim sadece admin panelinde görünür, anasayfada görünmez.</div>
                    @error('label')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if ($section->isFullyDynamic())
                    <div class="alert alert-secondary mb-0">
                        <i class="bi bi-info-circle"></i>
                        Bu bölümün içeriği, <strong>Sayfalar &rarr; Categories</strong> altındaki aktif kategori sayfalarından otomatik oluşturulur. Burada sırasını değiştirebilir, aktif/pasif yapabilir veya silebilirsiniz; ama metni ve sayıları her zaman güncel sayfalarınızı yansıtır — bunları değiştirmek için kategori sayfalarının kendisini düzenleyin.
                    </div>
                @elseif ($section->key)
                    @php $currentMode = old('mode', $section->mode); @endphp

                    <div class="admin-mode-group mb-3">
                        <input type="radio" name="mode" id="mode_default" value="default" class="admin-mode-radio" @checked($currentMode === 'default')>
                        <label for="mode_default" class="admin-mode-btn">Varsayılan</label>

                        <input type="radio" name="mode" id="mode_custom" value="custom" class="admin-mode-radio" @checked($currentMode === 'custom')>
                        <label for="mode_custom" class="admin-mode-btn">HTML</label>
                        @error('mode')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="admin-mode-panel admin-mode-panel--default">
                            @if ($settingsFields)
                                <fieldset class="admin-fieldset">
                                    <legend>İçerik</legend>

                                    @if ($section->key === 'hero')
                                        <div class="mb-3">
                                            <label for="hero_eyebrow" class="form-label">Üst Etiket</label>
                                            <input type="text" id="hero_eyebrow" name="hero_eyebrow" value="{{ old('hero_eyebrow', $settingsFields['hero_eyebrow']) }}" class="form-control @error('hero_eyebrow') is-invalid @enderror">
                                            @error('hero_eyebrow') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="hero_title" class="form-label">Başlık</label>
                                            <input type="text" id="hero_title" name="hero_title" value="{{ old('hero_title', $settingsFields['hero_title']) }}" class="form-control @error('hero_title') is-invalid @enderror">
                                            @error('hero_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="hero_lede" class="form-label">Giriş Metni</label>
                                            <textarea id="hero_lede" name="hero_lede" rows="3" class="form-control @error('hero_lede') is-invalid @enderror">{{ old('hero_lede', $settingsFields['hero_lede']) }}</textarea>
                                            @error('hero_lede') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <label class="form-label">İstatistikler</label>
                                        @for ($i = 1; $i <= 4; $i++)
                                            <div class="row g-2 mb-3">
                                                <div class="col-4 col-md-3">
                                                    <input type="text" name="stat_{{ $i }}_num" value="{{ old('stat_'.$i.'_num', $settingsFields['stat_'.$i.'_num']) }}" class="form-control @error('stat_'.$i.'_num') is-invalid @enderror" placeholder="Sayı">
                                                </div>
                                                <div class="col-8 col-md-9">
                                                    <input type="text" name="stat_{{ $i }}_label" value="{{ old('stat_'.$i.'_label', $settingsFields['stat_'.$i.'_label']) }}" class="form-control @error('stat_'.$i.'_label') is-invalid @enderror" placeholder="Etiket">
                                                </div>
                                            </div>
                                        @endfor
                                    @elseif ($section->key === 'cta')
                                        <div class="mb-3">
                                            <label for="closing_title" class="form-label">Başlık</label>
                                            <input type="text" id="closing_title" name="closing_title" value="{{ old('closing_title', $settingsFields['closing_title']) }}" class="form-control @error('closing_title') is-invalid @enderror">
                                            @error('closing_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="closing_text" class="form-label">Metin</label>
                                            <input type="text" id="closing_text" name="closing_text" value="{{ old('closing_text', $settingsFields['closing_text']) }}" class="form-control @error('closing_text') is-invalid @enderror">
                                            @error('closing_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="row g-2 mb-0">
                                            <div class="col-md-6">
                                                <label for="closing_button_1_text" class="form-label">Buton 1</label>
                                                <input type="text" id="closing_button_1_text" name="closing_button_1_text" value="{{ old('closing_button_1_text', $settingsFields['closing_button_1_text']) }}" class="form-control @error('closing_button_1_text') is-invalid @enderror">
                                                @error('closing_button_1_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="closing_button_2_text" class="form-label">Buton 2</label>
                                                <input type="text" id="closing_button_2_text" name="closing_button_2_text" value="{{ old('closing_button_2_text', $settingsFields['closing_button_2_text']) }}" class="form-control @error('closing_button_2_text') is-invalid @enderror">
                                                @error('closing_button_2_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    @elseif ($section->key === 'ticker')
                                        <label class="form-label">Kayan Öğeler</label>
                                        <div class="form-text mb-2">Bir öğeyi boş bırakırsanız kaldırılır. Liste, kayan şeridi doldurmak için otomatik tekrar eder.</div>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <input type="text" name="item_{{ $i }}" value="{{ old('item_'.$i, $settingsFields['item_'.$i] ?? '') }}" class="form-control mb-2 @error('item_'.$i) is-invalid @enderror" placeholder="Öğe {{ $i }}">
                                        @endfor
                                    @elseif ($section->key === 'enter_list')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <label class="form-label">Liste Öğeleri</label>
                                        <div class="form-text mb-2">Bir öğeyi boş bırakırsanız kaldırılır. Aşağıdaki boş bir alana yazarak yeni öğe ekleyebilirsiniz.</div>
                                        @for ($i = 1; $i <= 16; $i++)
                                            <input type="text" name="item_{{ $i }}" value="{{ old('item_'.$i, $settingsFields['item_'.$i] ?? '') }}" class="form-control mb-2 @error('item_'.$i) is-invalid @enderror" placeholder="Öğe {{ $i }}">
                                        @endfor

                                        <div class="mb-0 mt-3">
                                            <label for="note" class="form-label">Not (listenin altında)</label>
                                            <input type="text" id="note" name="note" value="{{ old('note', $settingsFields['note']) }}" class="form-control @error('note') is-invalid @enderror">
                                            @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    @elseif ($section->key === 'process')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="paragraph" class="form-label">Paragraf</label>
                                            <textarea id="paragraph" name="paragraph" rows="2" class="form-control @error('paragraph') is-invalid @enderror">{{ old('paragraph', $settingsFields['paragraph']) }}</textarea>
                                            @error('paragraph') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <label class="form-label">Adımlar</label>
                                        <div class="form-text mb-2">Bir adımın başlığını ve metnini boş bırakırsanız kaldırılır. Adım numarası otomatik eklenir.</div>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-4">
                                                    <input type="text" name="step_{{ $i }}_title" value="{{ old('step_'.$i.'_title', $settingsFields['step_'.$i.'_title'] ?? '') }}" class="form-control @error('step_'.$i.'_title') is-invalid @enderror" placeholder="Adım {{ $i }} başlığı">
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="step_{{ $i }}_text" value="{{ old('step_'.$i.'_text', $settingsFields['step_'.$i.'_text'] ?? '') }}" class="form-control @error('step_'.$i.'_text') is-invalid @enderror" placeholder="Adım {{ $i }} metni">
                                                </div>
                                            </div>
                                        @endfor

                                        <div class="mb-0 mt-3">
                                            <label for="note_text" class="form-label">Link metni (adımların altında)</label>
                                            <input type="text" id="note_text" name="note_text" value="{{ old('note_text', $settingsFields['note_text']) }}" class="form-control @error('note_text') is-invalid @enderror">
                                            @error('note_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    @elseif ($section->key === 'criteria')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="paragraph" class="form-label">Paragraf</label>
                                            <textarea id="paragraph" name="paragraph" rows="2" class="form-control @error('paragraph') is-invalid @enderror">{{ old('paragraph', $settingsFields['paragraph']) }}</textarea>
                                            @error('paragraph') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <label class="form-label">Kriterler</label>
                                        <div class="form-text mb-2">Bir satırın başlığını ve metnini boş bırakırsanız kaldırılır.</div>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-4">
                                                    <input type="text" name="item_{{ $i }}_title" value="{{ old('item_'.$i.'_title', $settingsFields['item_'.$i.'_title'] ?? '') }}" class="form-control @error('item_'.$i.'_title') is-invalid @enderror" placeholder="Başlık {{ $i }}">
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" name="item_{{ $i }}_text" value="{{ old('item_'.$i.'_text', $settingsFields['item_'.$i.'_text'] ?? '') }}" class="form-control @error('item_'.$i.'_text') is-invalid @enderror" placeholder="Metin {{ $i }}">
                                                </div>
                                            </div>
                                        @endfor

                                        <div class="mb-0 mt-3">
                                            <label for="note_text" class="form-label">Link metni (kriterlerin altında)</label>
                                            <input type="text" id="note_text" name="note_text" value="{{ old('note_text', $settingsFields['note_text']) }}" class="form-control @error('note_text') is-invalid @enderror">
                                            @error('note_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    @elseif ($section->key === 'jurors')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="paragraph" class="form-label">Paragraf</label>
                                            <textarea id="paragraph" name="paragraph" rows="2" class="form-control @error('paragraph') is-invalid @enderror">{{ old('paragraph', $settingsFields['paragraph']) }}</textarea>
                                            @error('paragraph') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <label class="form-label">Jüri Üyeleri</label>
                                        <div class="form-text mb-2">Bir satırın isim, rol ve kurum bilgisini boş bırakırsanız kaldırılır.</div>
                                        @for ($i = 1; $i <= 8; $i++)
                                            @php $memberImage = old('member_'.$i.'_image', $settingsFields['member_'.$i.'_image'] ?? ''); @endphp
                                            <div class="row g-2 mb-3 align-items-center">
                                                <div class="col-auto">
                                                    <img
                                                        id="member_{{ $i }}_image_preview"
                                                        src="{{ $memberImage ?: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                                                        alt=""
                                                        style="width:48px;height:48px;object-fit:cover;border-radius:50%;background:#e9ecef;"
                                                    >
                                                </div>
                                                <div class="col">
                                                    <input type="text" id="member_{{ $i }}_image" name="member_{{ $i }}_image" value="{{ $memberImage }}" class="form-control @error('member_'.$i.'_image') is-invalid @enderror" placeholder="Görsel yolu">
                                                </div>
                                                <div class="col-auto">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary js-pick-image"
                                                        data-input="member_{{ $i }}_image"
                                                        data-preview="member_{{ $i }}_image_preview"
                                                        data-fm-url="{{ route('admin.filemanager.index', ['picker' => 1]) }}"
                                                    >
                                                        Seç
                                                    </button>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="member_{{ $i }}_name" value="{{ old('member_'.$i.'_name', $settingsFields['member_'.$i.'_name'] ?? '') }}" class="form-control @error('member_'.$i.'_name') is-invalid @enderror" placeholder="İsim">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="member_{{ $i }}_role" value="{{ old('member_'.$i.'_role', $settingsFields['member_'.$i.'_role'] ?? '') }}" class="form-control @error('member_'.$i.'_role') is-invalid @enderror" placeholder="Rol">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="member_{{ $i }}_org" value="{{ old('member_'.$i.'_org', $settingsFields['member_'.$i.'_org'] ?? '') }}" class="form-control @error('member_'.$i.'_org') is-invalid @enderror" placeholder="Kurum">
                                                </div>
                                            </div>
                                        @endfor
                                    @elseif ($section->key === 'key_dates')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="paragraph" class="form-label">Paragraf</label>
                                            <textarea id="paragraph" name="paragraph" rows="2" class="form-control @error('paragraph') is-invalid @enderror">{{ old('paragraph', $settingsFields['paragraph']) }}</textarea>
                                            @error('paragraph') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label for="button_1_text" class="form-label">Buton 1</label>
                                                <input type="text" id="button_1_text" name="button_1_text" value="{{ old('button_1_text', $settingsFields['button_1_text']) }}" class="form-control @error('button_1_text') is-invalid @enderror">
                                                @error('button_1_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="button_2_text" class="form-label">Buton 2</label>
                                                <input type="text" id="button_2_text" name="button_2_text" value="{{ old('button_2_text', $settingsFields['button_2_text']) }}" class="form-control @error('button_2_text') is-invalid @enderror">
                                                @error('button_2_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <label class="form-label">Tarihler</label>
                                        <div class="form-text mb-2">Bir satırın tarih ve etiketini boş bırakırsanız kaldırılır. Durum boş bırakılırsa varsayılan olarak "Scheduled" yazılır.</div>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <div class="row g-2 mb-2">
                                                <div class="col-3 col-md-2">
                                                    <input type="text" name="date_{{ $i }}_time" value="{{ old('date_'.$i.'_time', $settingsFields['date_'.$i.'_time'] ?? '') }}" class="form-control @error('date_'.$i.'_time') is-invalid @enderror" placeholder="1 Feb">
                                                </div>
                                                <div class="col-6 col-md-7">
                                                    <input type="text" name="date_{{ $i }}_label" value="{{ old('date_'.$i.'_label', $settingsFields['date_'.$i.'_label'] ?? '') }}" class="form-control @error('date_'.$i.'_label') is-invalid @enderror" placeholder="Entries open">
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" name="date_{{ $i }}_status" value="{{ old('date_'.$i.'_status', $settingsFields['date_'.$i.'_status'] ?? '') }}" class="form-control @error('date_'.$i.'_status') is-invalid @enderror" placeholder="Scheduled">
                                                </div>
                                            </div>
                                        @endfor
                                    @elseif ($section->key === 'comparison')
                                        <div class="mb-3">
                                            <label for="heading" class="form-label">Başlık</label>
                                            <input type="text" id="heading" name="heading" value="{{ old('heading', $settingsFields['heading']) }}" class="form-control @error('heading') is-invalid @enderror">
                                            @error('heading') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="paragraph" class="form-label">Paragraf</label>
                                            <textarea id="paragraph" name="paragraph" rows="2" class="form-control @error('paragraph') is-invalid @enderror">{{ old('paragraph', $settingsFields['paragraph']) }}</textarea>
                                            @error('paragraph') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="left_heading" class="form-label">Sol sütun başlığı</label>
                                                <input type="text" id="left_heading" name="left_heading" value="{{ old('left_heading', $settingsFields['left_heading']) }}" class="form-control @error('left_heading') is-invalid @enderror">
                                                @error('left_heading') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                                <div class="form-text mt-2 mb-1">Bir öğeyi boş bırakırsanız kaldırılır.</div>
                                                @for ($i = 1; $i <= 6; $i++)
                                                    <input type="text" name="left_item_{{ $i }}" value="{{ old('left_item_'.$i, $settingsFields['left_item_'.$i] ?? '') }}" class="form-control mb-2 @error('left_item_'.$i) is-invalid @enderror" placeholder="Öğe {{ $i }}">
                                                @endfor
                                            </div>
                                            <div class="col-md-6">
                                                <label for="right_heading" class="form-label">Sağ sütun başlığı</label>
                                                <input type="text" id="right_heading" name="right_heading" value="{{ old('right_heading', $settingsFields['right_heading']) }}" class="form-control @error('right_heading') is-invalid @enderror">
                                                @error('right_heading') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                                <div class="form-text mt-2 mb-1">Bir öğeyi boş bırakırsanız kaldırılır.</div>
                                                @for ($i = 1; $i <= 6; $i++)
                                                    <input type="text" name="right_item_{{ $i }}" value="{{ old('right_item_'.$i, $settingsFields['right_item_'.$i] ?? '') }}" class="form-control mb-2 @error('right_item_'.$i) is-invalid @enderror" placeholder="Öğe {{ $i }}">
                                                @endfor
                                            </div>
                                        </div>
                                    @endif
                                </fieldset>
                            @else
                                <p class="text-muted mb-0">Bu bölümün henüz düzenlenebilir alanı yok — tasarlandığı haliyle gösteriliyor. Metnini veya düzenini değiştirmek için yukarıdan "HTML"e geçin.</p>
                            @endif
                        </div>

                        <div class="admin-mode-panel admin-mode-panel--custom">
                            <div class="mb-0">
                                <label for="page-html-editor" class="form-label">HTML İçerik</label>
                                <textarea
                                    id="page-html-editor"
                                    name="custom_html"
                                    class="form-control @error('custom_html') is-invalid @enderror"
                                    data-start-mode="source"
                                >{{ old('custom_html', $htmlSeed) }}</textarea>
                                <div class="form-text">Bu bölümün mevcut içeriğiyle önceden dolduruldu, ham HTML olarak gösteriliyor. Burası sadece kod modunda çalışır — görsel moda geçiş yoktur, çünkü görsel mod özel class'lı yapıları bozabilir/kaybedebilir.</div>
                                @error('custom_html')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0 mt-3">
                                <label for="custom_css" class="form-label">Özel CSS</label>
                                <textarea
                                    id="custom_css"
                                    name="custom_css"
                                    rows="10"
                                    class="form-control font-monospace admin-code-field @error('custom_css') is-invalid @enderror"
                                    placeholder=".my-section { ... }"
                                >{{ old('custom_css', $cssSeed) }}</textarea>
                                <div class="form-text">Sadece CSS, <code>&lt;style&gt;</code> etiketi eklemeyin. Yalnızca bu bölüme uygulanır.</div>
                                @error('custom_css')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <label for="page-html-editor" class="form-label">HTML İçerik</label>
                        <textarea
                            id="page-html-editor"
                            name="custom_html"
                            class="form-control @error('custom_html') is-invalid @enderror"
                            data-start-mode="source"
                        >{{ old('custom_html', $htmlSeed) }}</textarea>
                        @error('custom_html')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="custom_css" class="form-label">Özel CSS</label>
                        <textarea
                            id="custom_css"
                            name="custom_css"
                            rows="10"
                            class="form-control font-monospace admin-code-field @error('custom_css') is-invalid @enderror"
                            placeholder=".my-section { ... }"
                        >{{ old('custom_css', $cssSeed) }}</textarea>
                        <div class="form-text">Sadece CSS, <code>&lt;style&gt;</code> etiketi eklemeyin. Yalnızca bu bölüme uygulanır.</div>
                        @error('custom_css')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="mb-2">
                    <label for="status" class="form-label">Durum</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="1" @selected(old('status', (int) $section->status) == 1)>Aktif</option>
                        <option value="0" @selected(old('status', (int) $section->status) == 0)>Pasif</option>
                    </select>
                    <div class="form-text">Pasif bölümler anasayfada gizlenir; ayarları korunur.</div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        {{ $section->isFullyDynamic() ? 'Kaydet' : 'Taslak Kaydet' }}
                    </button>

                    <a href="{{ route('admin.home-sections.index') }}" class="btn btn-outline-secondary">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

@endsection
