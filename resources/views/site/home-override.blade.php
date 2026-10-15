@extends('layouts.site')

@section('head')
<title>Flyoneers Awards 2027 | Airport Passenger Services Awards</title>
<meta name="description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<link rel="canonical" href="{{ url('/') }}/">
<meta property="og:site_name" content="Flyoneers">
<meta property="og:type" content="website">
<meta property="og:title" content="Flyoneers Awards 2027 | Airport Passenger Services Awards">
<meta property="og:description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<meta property="og:url" content="{{ url('/') }}/">
<meta name="theme-color" content="#0B0D10">
@if ($css)
<style>{!! $css !!}</style>
@endif
@endsection

@section('content')
{!! $html !!}
@if ($js)
<script>{!! $js !!}</script>
@endif
@endsection
