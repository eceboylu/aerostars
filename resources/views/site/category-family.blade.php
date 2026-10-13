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

<div class="pagehead"><div class="wrap"><h1>{{ $page->title }} awards</h1><p class="pagehead__lede">{{ $page->meta_description }}</p></div></div>

<div class="wrap layout">

    <div>

        <h2 style="margin-top:0">{{ $awards->count() }} {{ \Illuminate\Support\Str::plural('category', $awards->count()) }}</h2>

        <ul class="rows">
            @foreach ($awards as $award)
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

        <div style="margin-top:32px">
            {!! $page->custom_html !!}
        </div>

    </div>

    <aside class="facts" aria-label="Key facts">
        <h2>At a glance</h2>
        <dl>
            <dt>Categories</dt><dd>{{ $awards->count() }}</dd>
            <dt>Entries open</dt><dd>To be announced soon</dd>
            <dt>Entries close</dt><dd>To be announced soon</dd>
            <dt>Winners announced</dt><dd>To be announced soon</dd>
        </dl>
        <a class="btn btn--signal" href="/enter/">Start your entry</a>

        @if ($siblingFamilies->isNotEmpty())
            <h2 style="margin-top:24px">Other families</h2>
            <ul class="plain">
                @foreach ($siblingFamilies as $sibling)
                    <li><a href="{{ $sibling->publicUrl() }}">{{ $sibling->title }}</a></li>
                @endforeach
            </ul>
        @endif
    </aside>

</div>

@endsection
