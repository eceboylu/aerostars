@extends('layouts.site')

@section('head')
<title>Flyoneers Awards 2027 | Airport Passenger Services Awards</title>
<meta name="description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<link rel="canonical" href="https://flyoneers.example/">
<meta property="og:site_name" content="Flyoneers">
<meta property="og:type" content="website">
<meta property="og:title" content="Flyoneers Awards 2027 | Airport Passenger Services Awards">
<meta property="og:description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<meta property="og:url" content="https://flyoneers.example/">
<meta property="og:image" content="https://flyoneers.example/assets/img/og.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#0B0D10">
<script type="application/ld+json">{"@@context": "https://schema.org", "@@type": "WebSite", "name": "Flyoneers", "url": "https://flyoneers.example/"}</script>
@endsection

@section('content')
@if ($preview)
<div style="position:sticky;top:0;z-index:50;background:#C9A227;color:#0B0D10;padding:10px 20px;font:700 0.9rem/1.3 system-ui,sans-serif;text-align:center;">
    Bu bir taslak önizlemesidir — yayınlanmamış değişiklikler burada gösteriliyor. Ziyaretçiler hâlâ yayındaki hali görüyor.
</div>
@endif
@foreach ($sections as $section)
    @php
        $isCustom = $preview ? $section->isCustom() : $section->isPublishedCustom();
        $html = $preview ? $section->custom_html : $section->published_custom_html;
        $css = $preview ? $section->scoped_custom_css : $section->scoped_published_custom_css;
        $values = $preview ? $section->settingsWithDefaults() : $section->publishedSettingsWithDefaults();
    @endphp
    @if ($isCustom)
        @if ($css)
            <style>{!! $css !!}</style>
        @endif
        <div id="home-section-{{ $section->id }}">{!! $html !!}</div>
    @elseif ($section->partialView())
        @include($section->partialView(), $values ?? [])
    @endif
@endforeach
@endsection
