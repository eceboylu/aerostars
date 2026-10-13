<section class="section"><div class="wrap">
  <div class="section__head"><h2>{{ $heading }}</h2>
  <p>{{ $paragraph }}</p></div>
  <ul class="jurors">@foreach ($members as $member)<li class="juror">@if (!empty($member['image']))<img class="juror__photo" src="{{ $member['image'] }}" alt="{{ $member['name'] }}">@else<span class="juror__photo juror__photo--empty" aria-hidden="true">JN</span>@endif<span class="juror__name">{{ $member['name'] }}</span><span class="juror__role">{{ $member['role'] }}</span><span class="juror__org">{{ $member['org'] }}</span></li>@endforeach</ul>
</div></section>
