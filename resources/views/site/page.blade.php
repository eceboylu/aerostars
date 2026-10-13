@extends('layouts.site')

@section('head')
<title>{{ $page->seoTitle() }}</title>
@if ($page->seoDescription())
<meta name="description" content="{{ $page->seoDescription() }}">
@endif
@if ($page->meta_keywords)
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif
<link rel="canonical" href="{{ $page->seoCanonicalUrl() }}">
<meta name="robots" content="{{ $page->seoRobotsContent() }}">
<meta property="og:title" content="{{ $page->seoOgTitle() }}">
@if ($page->seoOgDescription())
<meta property="og:description" content="{{ $page->seoOgDescription() }}">
@endif
<meta property="og:url" content="{{ $page->seoCanonicalUrl() }}">
<meta property="og:type" content="website">
<script type="application/ld+json">@json($breadcrumbSchema)</script>
@endsection

@push('styles')
    @vite(['resources/css/cms-bootstrap.css', 'resources/css/cms-site.css'])

    @php
        $cmsTailwindPath = public_path('cms/cms-tailwind.css');
        $cmsTailwindVersion = is_file($cmsTailwindPath) ? filemtime($cmsTailwindPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('cms/cms-tailwind.css') }}?v={{ $cmsTailwindVersion }}">

    @if ($previewCss ?? $page->scoped_custom_css)
        <style>{!! $previewCss ?? $page->scoped_custom_css !!}</style>
    @endif
@endpush

@section('content')
@if ($previewHtml !== null)
<div style="position:sticky;top:0;z-index:50;background:#C9A227;color:#0B0D10;padding:10px 20px;font:700 0.9rem/1.3 system-ui,sans-serif;text-align:center;">
    Bu bir önizlemedir — hiçbir şey kaydedilmedi.
</div>
@endif
<div
    id="cms-page-content"
    class="cms-page-content"
>
    {!! $previewHtml ?? $page->custom_html !!}
</div>

@if (($siblingAwards ?? collect())->isNotEmpty())
<section class="section" style="padding-top:24px">
    <div class="wrap">
        <h2>Other {{ $family->title }} categories</h2>
        <ul class="rows">
            @foreach ($siblingAwards as $award)
                <li class="row">
                    <a class="row__link" href="{{ $award->publicUrl() }}">
                        <div class="row__body">
                            <span class="row__name">{{ $award->title }}</span>
                            <span class="row__text">{{ \App\Http\Controllers\SiteController::shortDescription($award) }}</span>
                        </div>
                        <span class="row__go">View category</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
@endsection
