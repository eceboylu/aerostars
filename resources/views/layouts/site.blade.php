<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="application/ld+json">{"@@context": "https://schema.org", "@@type": "Organization", "name": "Flyoneers", "url": @json(url('/').'/'), "logo": @json(url('/icon-512.png')), @if (filled($siteChrome['footer_email']))"email": @json($siteChrome['footer_email']), @endif"description": "International awards for companies that sell, provide and manage airport passenger services."}</script>
@isset($page)
<title>{{ $page->seoTitle() }}</title>
@if ($page->seoDescription())
<meta name="description" content="{{ $page->seoDescription() }}">
@endif
@if ($page->meta_keywords)
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif
<link rel="canonical" href="{{ $page->seoCanonicalUrl() }}">
<meta name="robots" content="{{ $page->seoRobotsContent() }}">
<meta property="og:title" content="{{ $page->seoTitle() }}">
@if ($page->seoDescription())
<meta property="og:description" content="{{ $page->seoDescription() }}">
@endif
<meta property="og:url" content="{{ $page->seoCanonicalUrl() }}">
<meta property="og:type" content="website">
<script type="application/ld+json">@json($breadcrumbSchema)</script>
@endisset
@yield('head')
<link rel="icon" href="/favicon.ico" sizes="48x48">
<link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
<link rel="icon" href="/icon-192.png" type="image/png" sizes="192x192">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap">
@vite([
    'resources/css/site.css',
    'resources/js/site.js'
])
@stack('styles')
<style>#cms-page-content { background: transparent; }</style>
 <script>document.documentElement.classList.add('js')</script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand{{ blank($siteChrome['site_logo']) ? ' is-broken' : '' }}" href="/" aria-label="Flyoneers home">@if (filled($siteChrome['site_logo']))<img src="{{ $siteChrome['site_logo'] }}" alt="Flyoneers" width="152" height="53">@endif<span class="brand__fallback" aria-hidden="true">flyoneers</span></a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav">Menu</button>
    <nav class="nav" id="nav" aria-label="Main">
      @foreach ($headerMenuItems as $item)
        @continue(! $item->isVisible())
        @if ($item->url())
          <a href="{{ $item->url() }}" @if (request()->url() === $item->url()) aria-current="page" @endif @if ($item->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
        @else
          <span>{{ $item->label }}</span>
        @endif
        @foreach ($item->children as $child)
          @continue(! $child->isVisible())
          @if ($child->url())
            <a href="{{ $child->url() }}" @if (request()->url() === $child->url()) aria-current="page" @endif @if ($child->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a>
          @else
            <span>{{ $child->label }}</span>
          @endif
        @endforeach
      @endforeach
      @if (filled($siteChrome['header_cta_label']) && filled($siteChrome['header_cta_url']))
        <a class="btn btn--signal" href="{{ $siteChrome['header_cta_url'] }}">{{ $siteChrome['header_cta_label'] }}</a>
      @endif
    </nav>
  </div>
</header>

<main id="main">
<div class="wrap">
    @include('partials.breadcrumb')
</div>
@yield('content')
</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="foot">
      <div>
        <a class="brand{{ blank($siteChrome['site_logo']) ? ' is-broken' : '' }}" href="/" aria-label="Flyoneers home">@if (filled($siteChrome['site_logo']))<img src="{{ $siteChrome['site_logo'] }}" alt="Flyoneers" width="152" height="53">@endif<span class="brand__fallback" aria-hidden="true">flyoneers</span></a>
        @if (filled($siteChrome['footer_description']))
          <p>{{ $siteChrome['footer_description'] }}</p>
        @endif
      </div>
      @foreach ($footerMenuItems as $group)
        @continue(! $group->status)
        <div><h2>{{ $group->label }}</h2><ul>
          @foreach ($group->children as $child)
            @continue(! $child->isVisible())
            <li>
              @if ($child->url())
                <a href="{{ $child->url() }}" @if ($child->open_in_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a>
              @else
                {{ $child->label }}
              @endif
            </li>
          @endforeach
        </ul></div>
      @endforeach
    </div>
    @if (filled($siteChrome['footer_copyright']) || filled($siteChrome['footer_email']))
      <div class="legal">
        <span>{{ $siteChrome['footer_copyright'] }}</span>
        @if (filled($siteChrome['footer_email']))
          <a href="mailto:{{ $siteChrome['footer_email'] }}">{{ $siteChrome['footer_email'] }}</a>
        @endif
      </div>
    @endif
  </div>
</footer>
</body>
</html>
