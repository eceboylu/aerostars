<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@yield('head')
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&family=Source+Serif+4:opsz,wght@8..60,400..700&display=swap">
@vite([
    'resources/css/site.css',
    'resources/js/site.js'
]) <script>document.documentElement.classList.add('js')</script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header">
  <div class="wrap bar">
    <a class="brand" href="/" aria-label="AirStars home"><svg viewBox="0 0 32 32" aria-hidden="true" focusable="false"><rect width="32" height="32" fill="#C9A227"/><path d="M16 2.5c1.1 0 1.8 1 1.8 2.2v7.6L28.5 18v2.4l-10.7-3v6.3l3.1 2.3v2.1L16 26.8l-5.9 1.3v-2.1l3.1-2.3v-6.3l-10.7 3V18l10.7-5.7V4.7c0-1.2.7-2.2 1.8-2.2z" fill="#0B0D10"/></svg><span>AirStars</span></a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav">Menu</button>
    <nav class="nav" id="nav" aria-label="Main">
      <a href="/categories/" @if (request()->is('categories*')) aria-current="page" @endif>Categories</a>
      <a href="/how-it-works/" @if (request()->is('how-it-works*')) aria-current="page" @endif>How it works</a>
      <a href="/judging/" @if (request()->is('judging*')) aria-current="page" @endif>Judging</a>
      <a href="/winners/" @if (request()->is('winners*')) aria-current="page" @endif>Winners</a>
      <a href="/about/" @if (request()->is('about*')) aria-current="page" @endif>About</a>
      <a class="btn btn--signal" href="/enter/">Enter the awards</a>
    </nav>
  </div>
</header>

<main id="main">
@yield('content')
</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="foot">
      <div>
        <a class="brand" href="/"><svg viewBox="0 0 32 32" aria-hidden="true" focusable="false"><rect width="32" height="32" fill="#C9A227"/><path d="M16 2.5c1.1 0 1.8 1 1.8 2.2v7.6L28.5 18v2.4l-10.7-3v6.3l3.1 2.3v2.1L16 26.8l-5.9 1.3v-2.1l3.1-2.3v-6.3l-10.7 3V18l10.7-5.7V4.7c0-1.2.7-2.2 1.8-2.2z" fill="#0B0D10"/></svg><span>AirStars</span></a>
        <p>International awards for the companies that sell, provide and manage airport passenger services.</p>
      </div>
      <div><h2>Awards</h2><ul>
        <li><a href="/categories/airport-lounge/">Airport Lounge</a></li>
        <li><a href="/categories/fast-track/">Fast Track</a></li>
        <li><a href="/categories/meet-and-greet/">Meet &amp; Greet</a></li>
        <li><a href="/categories/airport-service-platforms/">Airport Service Platforms</a></li>
        <li><a href="/categories/airport-services-technology/">Airport Services Technology</a></li></ul></div>
      <div><h2>Programme</h2><ul>
        <li><a href="/how-it-works/">How it works</a></li>
        <li><a href="/judging/">Judging</a></li>
        <li><a href="/winners/">Winners</a></li>
        <li><a href="/faq/">Questions</a></li>
        <li><a href="/enter/">Enter the awards</a></li></ul></div>
      <div><h2>AirStars</h2><ul>
        <li><a href="/about/">About</a></li>
        <li><a href="/contact/">Contact</a></li>
        <li><a href="/terms/">Terms</a></li>
        <li><a href="/privacy/">Privacy</a></li></ul></div>
    </div>
    <div class="legal"><span>&copy; 2027 AirStars. All rights reserved.</span><a href="mailto:awards@@airstars.example">awards@@airstars.example</a></div>
  </div>
</footer>
</body>
</html>
