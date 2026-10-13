@extends('layouts.site')

@section('head')
<title>Önizleme | Flyoneers</title>
<meta name="robots" content="noindex,nofollow">
@if ($css)
<style>{!! $css !!}</style>
@endif
@endsection

@section('content')
<div style="position:sticky;top:0;z-index:50;background:#C9A227;color:#0B0D10;padding:10px 20px;font:700 0.9rem/1.3 system-ui,sans-serif;text-align:center;">
    Bu bir önizlemedir — hiçbir şey kaydedilmedi ve henüz etkin değil.
</div>
{!! $html !!}
@if ($js)
<script>{!! $js !!}</script>
@endif
@endsection
