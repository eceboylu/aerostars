<section class="section section--dates"><div class="wrap split-board">
  <div>
    <div class="section__head"><h2>{{ $heading }}</h2>
    <p>{{ $paragraph }}</p></div>
    <div class="btn-row"><a class="btn btn--signal" href="/how-it-works/">{{ $button_1_text }}</a><a class="btn" href="/judging/">{{ $button_2_text }}</a></div>
  </div>
  <div class="board board--live"><div class="board__head"><span>Flyoneers 2027 programme</span><span>Status</span></div><ol class="board__rows">@foreach ($dates as $i => $date)<li class="board__row" style="--i:{{ $i }}"><span class="board__time">{{ $date['time'] }}</span><span>{{ $date['label'] }}</span><span class="board__status">{{ $date['status'] }}</span></li>@endforeach</ol><p class="board__note">Dates are provisional until entries open.</p></div>
</div></section>
