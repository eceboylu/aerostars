@extends('layouts.site')

@section('head')
<title>Önizleme | Flyoneers</title>
<meta name="robots" content="noindex,nofollow">
@endsection

@push('styles')
    @vite(['resources/css/cms-bootstrap.css', 'resources/css/cms-site.css'])

    @php
        $cmsTailwindPath = public_path('cms/cms-tailwind.css');
        $cmsTailwindVersion = is_file($cmsTailwindPath) ? filemtime($cmsTailwindPath) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('cms/cms-tailwind.css') }}?v={{ $cmsTailwindVersion }}">

    @if ($css)
        <style>{!! $css !!}</style>
    @endif
@endpush

@section('content')
<div style="position:sticky;top:0;z-index:50;background:#C9A227;color:#0B0D10;padding:10px 20px;font:700 0.9rem/1.3 system-ui,sans-serif;text-align:center;">
    Bu bir önizlemedir — hiçbir şey kaydedilmedi. Bu sekmeyi kapatıp düzenlemeye devam edebilirsiniz.
</div>
<div
    id="cms-page-content"
    class="cms-page-content"
>
    {!! $html !!}
</div>
@endsection
