<section class="section section--panel section--criteria"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2>
  <div class="section__aside"><p>{{ $paragraph }}</p>
  @if (! empty($stat_3_num))<p class="criteria__count"><span class="stat__num">{{ $stat_3_num }}</span><span class="stat__label">{{ $stat_3_label }}</span></p>@endif
  </div></div>
  <dl class="criteria">@foreach ($items as $item)<div><dt>{{ $item['title'] }}</dt><dd>{{ $item['text'] }}</dd></div>@endforeach</dl>
  <p class="note-line"><a href="/judging/">{{ $note_text }}</a></p>
</div></section>
