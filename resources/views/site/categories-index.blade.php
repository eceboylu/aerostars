@extends('layouts.site')

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
            <div class="family-block__head">
                <span class="family-block__icon" aria-hidden="true">@include('partials.family-icon', ['page' => $family])</span>
                <div>
                    <h2><a href="{{ $family->publicUrl() }}">{{ $family->title }}</a></h2>
                    <p>{{ $family->shortDescription() }}</p>
                </div>
            </div>

            <ul class="rows">
                @foreach ($familyAwards[$family->id] as $award)
                    <li class="row">
                        <a class="row__link" href="{{ $award->publicUrl() }}">
                            @if ($award->icon)
                                <span class="row__icon" aria-hidden="true"><img src="{{ $award->icon }}" alt="" loading="lazy"></span>
                            @endif
                            <div class="row__body">
                                <span class="row__name">{{ $award->title }}</span>
                                <span class="row__text">{{ $award->shortDescription() }}</span>
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
