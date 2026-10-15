@extends('layouts.site')

@section('content')

<div class="pagehead"><div class="wrap pagehead__inner">
    <span class="pagehead__icon" aria-hidden="true">@include('partials.family-icon', ['page' => $page])</span>
    <div><h1>{{ $page->title }} awards</h1><p class="pagehead__lede">{{ $page->meta_description }}</p></div>
</div></div>

<div class="wrap layout">

    <div>

        <h2 style="margin-top:0">{{ $awards->count() }} {{ \Illuminate\Support\Str::plural('category', $awards->count()) }}</h2>

        <ul class="rows">
            @foreach ($awards as $award)
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
