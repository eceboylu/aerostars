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

@section('content')

<div class="pagehead"><div class="wrap"><h1>{{ $page->title }}</h1><p class="pagehead__lede">{{ $page->meta_description }}</p></div></div>

@if ($winners->isNotEmpty())

    <section class="section"><div class="wrap">

        <ul class="winners">
            @foreach ($winners as $winner)
                <li class="winner">
                    @if ($winner->category->badge_image)
                        <img class="winner__badge" src="{{ $winner->category->badge_image }}" alt="{{ $winner->category->title }} badge">
                    @endif
                    @if ($winner->logo)
                        <img class="winner__logo" src="{{ $winner->logo }}" alt="{{ $winner->company_name }} logo">
                    @endif
                    <span class="winner__category">{{ $winner->category->title }}</span>
                    <span class="winner__name">{{ $winner->company_name }}</span>
                    @if ($winner->won_at)
                        <span class="winner__date">{{ $winner->won_at->format('d.m.Y') }}</span>
                    @endif
                    @if ($winner->description)
                        <p class="winner__desc">{{ $winner->description }}</p>
                    @endif
                </li>
            @endforeach
        </ul>

    </div></section>

@endif

@endsection
