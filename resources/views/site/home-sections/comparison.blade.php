<section class="section section--panel"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2>
  <p>{{ $paragraph }}</p></div>
  <div class="split">
    <div class="split__col"><h3>{{ $left_heading }}</h3><ul class="plain">
      @foreach ($left_items as $item)<li>{{ $item }}</li>@endforeach</ul></div>
    <div class="split__col split__col--not"><h3>{{ $right_heading }}</h3><ul class="plain">
      @foreach ($right_items as $item)<li>{{ $item }}</li>@endforeach</ul></div>
  </div>
</div></section>
