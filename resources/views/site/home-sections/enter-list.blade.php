<section class="section section--panel section--audience"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2></div>
  <ul class="cols">@foreach ($items as $item)<li>{{ $item }}</li>@endforeach</ul>
  <p class="note-line">{{ $note }}</p>
</div></section>
