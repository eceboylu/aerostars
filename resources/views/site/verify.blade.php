@extends('layouts.site')

@php
    $year = $winner->won_at?->year;
    $awardTitle = $winner->category->title;
    $verificationUrl = route('site.verify', $winner->verification_slug);
    $seoTitle = $winner->company_name.' — Flyoneers Verified Award Winner';
    $seoDescription = $winner->company_name.' is listed as a verified Flyoneers award winner for '.$awardTitle.' '.$year.'.';
@endphp

@section('head')
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $verificationUrl }}">
<meta name="robots" content="index,follow">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $verificationUrl }}">
<meta property="og:type" content="website">
@if ($winner->category->badge_image)
<meta property="og:image" content="{{ url($winner->category->badge_image) }}">
@endif
@endsection

@section('content')

<div class="pagehead"><div class="wrap">
    <p class="eyebrow">Digital Award Verification</p>
    <h1>Verified Award Winner</h1>
</div></div>

<section class="section"><div class="wrap">

    <div class="winner verify">
        @if ($winner->category->badge_image)
            <img class="winner__badge" src="{{ $winner->category->badge_image }}" alt="Flyoneers Awards Winner {{ $year }} badge">
        @else
            <div class="winner__badge verify__badge-empty">Flyoneers<br>Winner {{ $year }}</div>
        @endif

        <p class="verify__status verify__status--ok"><strong>Award record verified</strong></p>

        <span class="winner__name">{{ $winner->company_name }}</span>
        <span class="winner__category">{{ $awardTitle }}</span>
        <span class="winner__date">Award year: {{ $year }}</span>

        <p class="winner__desc">Issued by Flyoneers Awards.</p>
        <span class="winner__date">Always check that this page is hosted on the official Flyoneers website.</span>

        @if ($winnersPage)
            <div class="btn-row btn-row--center">
                <a class="btn" href="{{ $winnersPage->publicUrl() }}">Back to winners</a>
            </div>
        @endif
    </div>

</div></section>

@endsection
