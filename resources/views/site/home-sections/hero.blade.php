@php
    $heroStats = [[$stat_1_num, $stat_1_label], [$stat_2_num, $stat_2_label], [$stat_4_num, $stat_4_label]];
    $padStat = fn ($value) => ctype_digit((string) $value) && strlen((string) $value) === 1 ? '0'.$value : $value;
    $awardCategoryCount = \App\Models\Page::activeAwardCategoryCount();
@endphp
<section class="hero hero--landing"><div class="hero__runway" aria-hidden="true"></div><div class="wrap">
  @if (filled($hero_emblem ?? null))
  <img class="hero__emblem" src="{{ $hero_emblem }}" alt="" width="132" height="120" decoding="async" fetchpriority="high">
  @endif
  <p class="eyebrow">{{ $hero_eyebrow }}</p>
  <h1>{{ $hero_title }}</h1>
  <p class="hero__lede">{{ $hero_lede }}</p>
  <div class="btn-row btn-row--center"><a class="btn btn--signal" href="/enter/">Start your entry</a><a class="btn" href="/categories/">Browse {{ $awardCategoryCount }} {{ Str::plural('category', $awardCategoryCount) }}</a></div>
  <ul class="stats">
    @foreach ($heroStats as [$statNum, $statLabel])
      <li><span class="stat__num">{{ $padStat($statNum) }}</span><span class="stat__label">{{ $statLabel }}</span></li>
    @endforeach
  </ul>
</div></section>
