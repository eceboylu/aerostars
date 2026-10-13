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

<section class="section"><div class="wrap">

    <div class="filter" role="group" aria-label="Filter by family">
        <button class="chip" type="button" data-filter="all" aria-pressed="true">All</button>
        @foreach ($families as $family)
            <button class="chip" type="button" data-filter="{{ basename($family->slug) }}" aria-pressed="false">{{ $family->title }}</button>
        @endforeach
    </div>

    @foreach ($families as $family)
        <div class="family-block" data-family="{{ basename($family->slug) }}">
            <h2><a href="{{ $family->publicUrl() }}">{{ $family->title }}</a></h2>
            <p>{{ \App\Http\Controllers\SiteController::shortDescription($family) }}</p>

            <ul class="rows">
                @foreach ($familyAwards[$family->id] as $award)
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
    @endforeach

</div></section>

{!! $page->custom_html !!}

@endsection
