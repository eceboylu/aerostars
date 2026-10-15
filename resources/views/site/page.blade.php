@extends('layouts.site')

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
@if ($errors->any())
<div class="wrap">
    <div class="form-errors" role="alert">
        <p class="form-errors__title">Please check the following and send again:</p>
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
</div>
<script>window.flyOldInput = @json(session()->getOldInput());</script>
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
</section>
@endif
@endsection
