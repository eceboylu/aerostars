<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/ld+json">{"@@context": "https://schema.org", "@@type": "Organization", "name": "Flyoneers", "url": "https://flyoneers.example/", "logo": "https://flyoneers.example/favicon.svg", "email": "awards@@flyoneers.example", "description": "International awards for companies that sell, provide and manage airport passenger services."}</script>
@yield('head')
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&family=Source+Serif+4:opsz,wght@8..60,400..700&display=swap">
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
    <a class="brand" href="/" aria-label="Flyoneers home"><img src="/uploads/flyoneers-logo.png" alt="Flyoneers"></a>
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
      <a class="btn btn--signal" href="/enter/">Enter the awards</a>
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
        <a class="brand" href="/" aria-label="Flyoneers home"><img src="/uploads/flyoneers-logo.png" alt="Flyoneers"></a>
        <p>International awards for the companies that sell, provide and manage airport passenger services.</p>
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
    <div class="legal"><span>&copy; 2027 Flyoneers. All rights reserved.</span><a href="mailto:awards@@flyoneers.example">awards@@flyoneers.example</a></div>
  </div>
</footer>
</body>
</html>
