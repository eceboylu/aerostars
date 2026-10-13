<section class="section section--panel"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2>
  <p>{{ $paragraph }}</p></div>
  <dl class="criteria">@foreach ($items as $item)<div><dt>{{ $item['title'] }}</dt><dd>{{ $item['text'] }}</dd></div>@endforeach</dl>
  <p class="note-line"><a href="/judging/">{{ $note_text }}</a></p>
</div></section>
