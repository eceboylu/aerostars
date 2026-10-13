<section class="section"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2>
  <p>{{ $paragraph }}</p></div>
  <ol class="steps">@foreach ($steps as $step)<li class="step{{ $loop->last ? ' step--last' : '' }}"><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></li>@endforeach</ol>
  <p class="note-line"><a href="/how-it-works/">{{ $note_text }}</a></p>
</div></section>
