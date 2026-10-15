@extends('layouts.site')

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
