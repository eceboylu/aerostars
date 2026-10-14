@php
    $faqPage = \App\Models\Page::where('slug', 'faq')->where('status', true)->first();
    $faqItems = $faqPage ? $faqPage->extractFaqPreview(3) : [];
@endphp
@if ($faqPage && $faqItems)
<section class="section section--panel"><div class="wrap faq">
  <div class="section__head"><h2>Common questions</h2></div>
  <div class="faq-top">@foreach ($faqItems as $item)<details name="faq"><summary>{{ $item['question'] }}</summary><p>{{ $item['answer'] }}</p></details>@endforeach</div>
  <p class="note-line"><a href="{{ $faqPage->publicUrl() }}">All questions and answers</a></p>
</div></section>
@endif
