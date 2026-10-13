@extends('layouts.site')

@section('head')
<title>Flyoneers Awards 2027 | Airport Passenger Services Awards</title>
<meta name="description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<link rel="canonical" href="https://flyoneers.example/">
<meta property="og:site_name" content="Flyoneers">
<meta property="og:type" content="website">
<meta property="og:title" content="Flyoneers Awards 2027 | Airport Passenger Services Awards">
<meta property="og:description" content="Flyoneers is an international awards programme for companies that sell, book and manage airport passenger services: travel technology platforms, travel guides, ground services and accessible travel resources.">
<meta property="og:url" content="https://flyoneers.example/">
<meta property="og:image" content="https://flyoneers.example/assets/img/og.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
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
