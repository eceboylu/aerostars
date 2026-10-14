@extends('layouts.admin')

@section('title', 'Kazananı Düzenle')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 mb-1">Kazananı Düzenle</h1>
            <p class="text-muted mb-0">{{ $winner->company_name }}</p>
        </div>

        <a href="{{ route('admin.winners.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Listeye Dön
        </a>

    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
            <a href="#rozet-dogrulama" class="alert-link">Rozet ve Doğrulama</a>
        </div>
    @endif

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST" action="{{ route('admin.winners.update', $winner) }}">

                @csrf
                @method('PUT')

                @include('admin.winners._fields')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i>
                        Kaydet
                    </button>

                    <a href="{{ route('admin.winners.index') }}" class="btn btn-light">
                        İptal
                    </a>
                </div>

            </form>

        </div>

    </div>

    @php
        $verificationUrl = route('site.verify', $winner->verification_slug);
        $badgeUrl = $winner->category?->badge_image;
        $embedCode = '<a href="'.$verificationUrl.'" target="_blank" rel="noopener">'."\n"
            .'    <img src="'.$badgeUrl.'" alt="Flyoneers Awards Winner '.$winner->won_at?->year.'">'."\n"
            .'</a>';
    @endphp

    <div class="card border-0 shadow-sm mt-4" id="rozet-dogrulama">

        <div class="card-body p-4">

            <h2 class="h5 mb-3">Rozet ve Doğrulama</h2>

            @if (! $badgeUrl)
                <div class="alert alert-warning">Bu kategorinin rozet görseli yok. Embed kodu kullanmadan önce yukarıdan Kategori Rozeti seçin.</div>
            @endif

            <div class="mb-3">
                <label for="embed_code" class="form-label">HTML Embed Kodu</label>
                <textarea id="embed_code" rows="4" class="form-control font-monospace">{{ $embedCode }}</textarea>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2 js-copy-embed">Kodu Kopyala</button>
            </div>

        </div>

    </div>

    <script>
        document.querySelector('.js-copy-embed').addEventListener('click', function () {
            var button = this;
            navigator.clipboard.writeText(document.getElementById('embed_code').value);
            button.textContent = 'Kopyalandı!';
            setTimeout(function () {
                button.textContent = 'Kodu Kopyala';
            }, 1000);
        });
    </script>

@endsection
